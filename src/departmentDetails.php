<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Old+Standard+TT:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #E2F0D9 0%, #A0D9E8 100%);
            min-height: 100vh;
        }

        h5.card-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.75rem;
        }

        h6.card-subtitle, h4 {
            font-family: 'Old Standard TT', serif;
        }
    </style>
</head>
<body>

<?php
require_once 'RedisConnect.php';

// Redis connection
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis");
}

// Get department number from the URL
$dno = isset($_GET['dnumber']) ? intval($_GET['dnumber']) : null;

if ($dno) {
    // Fetch department details from Redis
    $departmentKey = "DEPARTMENT:$dno";  // Corrected to use DEPARTMENT:dno
    if ($redis->exists($departmentKey)) {
        $department = $redis->hGetAll($departmentKey);

        // Fetch manager's details (manager's first and last name)
        $mgrSSN = $department['mgrssn'];
        $mgrKey = "EMPLOYEE:$mgrSSN";  // Corrected key for employee
        $manager = $redis->hGetAll($mgrKey);

        echo '<div class="container mt-5">';
        echo '  <div class="row justify-content-center">';
        echo '    <div class="col-md-6">';
        echo '      <div class="card mb-4 text-center">';
        echo '        <div class="card-body">';
        echo '          <h5 class="card-title">Department: ' . htmlspecialchars($department['dname']) . '</h5>';
        echo '          <h6 class="card-subtitle mb-2 text-muted">Manager: <a href="empView.php?ssn=' . htmlspecialchars($mgrSSN) . '">' . htmlspecialchars($manager['fname']) . ' ' . htmlspecialchars($manager['lname']) . '</a></h6>';
        echo '          <p class="card-text">Manager Start Date: ' . htmlspecialchars($department['mgrstartdate']) . '</p>';

        // Fetch department locations from Redis
        // Assuming $dno is the department number
        $locationsPattern = "DEPT_LOCATIONS:$dno:*";  // Correct pattern to match all locations for this department

        // Use Redis SCAN command or KEYS to find matching locations (SCAN is recommended for large datasets)
        $locations = $redis->keys($locationsPattern);

        // Display the locations
        echo '<h6 class="mt-3">Department Locations:</h6>';
        if ($locations) {
            foreach ($locations as $location) {
                // Extract the location name from the key (e.g., "DEPT_LOCATIONS:5:houston" => "houston")
                $locationParts = explode(':', $location);
                $locationName = end($locationParts);  // The last part is the location name
        
                echo '<span class="badge bg-primary me-1">' . htmlspecialchars($locationName) . '</span>';
            }
        } else {
                echo "<p>No locations found for this department.</p>";
        }

        echo '        </div>';
        echo '      </div>';
        echo '    </div>';
        echo '  </div>';

        // Fetch employees in the department
        echo '<div class="container mt-4">';
        echo '<h4 class="text-center">Employees</h4>';
        $employees = $redis->keys("EMPLOYEE:*"); // Retrieve all employee keys

        // Check each employee's department number (dno) to see if it matches the current department
        $employeeSSNs = [];
        foreach ($employees as $employeeKey) {
            $employee = $redis->hGetAll($employeeKey);
            if (isset($employee['dno']) && intval($employee['dno']) == $dno) {
                $employeeSSNs[] = $employee['ssn']; // Collect SSNs for employees belonging to this department
            }
        }

        if ($employeeSSNs) {
            echo '<table class="table table-striped table-bordered">';
            echo '  <thead class="table-warning">';
            echo '    <tr><th>Employee SSN</th><th>Last Name</th><th>First Name</th></tr>';
            echo '  </thead>';
            echo '  <tbody>';
            foreach ($employeeSSNs as $ssn) {
                $employeeKey = "EMPLOYEE:$ssn"; // Correct key for employee details
                $employee = $redis->hGetAll($employeeKey);
                echo '<tr>';
                echo '  <td><a href="empView.php?ssn=' . htmlspecialchars($ssn) . '">' . htmlspecialchars($ssn) . '</a></td>';
                echo '  <td>' . htmlspecialchars($employee['lname']) . '</td>';
                echo '  <td>' . htmlspecialchars($employee['fname']) . '</td>';
                echo '</tr>';
            }
            echo '  </tbody>';
            echo '</table>';
        } else {
            echo "<p class='text-center'>No employees found in this department.</p>";
        }
        echo '</div>';

        echo '<div class="container mt-4">';
        echo '<h4 class="text-center">Projects</h4>';
        
        // Retrieve all project keys and check for matching dnum
        $allProjects = $redis->keys("PROJECT:*");  // Retrieve all project keys

        if ($allProjects) {
            echo '<table class="table table-striped table-bordered">';
            echo '  <thead class="table-warning">';
            echo '    <tr><th>Project Number</th><th>Project Name</th><th>Location</th></tr>';
            echo '  </thead>';
            echo '  <tbody>';
            
            foreach ($allProjects as $projectKey) {
                $project = $redis->hGetAll($projectKey);

                // Check if the department number (dnumber) in the department matches the dnum in the project
                if (isset($project['dnum']) && intval($project['dnum']) == $dno) {
                    // Display project details
                    $pnumber = str_replace("PROJECT:", "", $projectKey); // Extract project number from the key
                    echo '<tr>';
                    echo '  <td><a href="projView.php?pnumber=' . htmlspecialchars($pnumber) . '">' . htmlspecialchars($pnumber) . '</a></td>';
                    echo '  <td>' . htmlspecialchars($project['pname']) . '</td>';
                    echo '  <td>' . htmlspecialchars($project['plocation']) . '</td>';
                    echo '</tr>';
                }
            }

            echo '  </tbody>';
            echo '</table>';
        } else {
            echo "<p class='text-center'>No projects found for this department.</p>";
        }
        echo '</div>';
    } else {
        echo "<p class='text-center'>Department not found.</p>";
    }
} else {
    echo "<p class='text-center'>No department number specified.</p>";
}

$redis->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
