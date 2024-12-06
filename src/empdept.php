<?php
include_once "RedisConnect.php"; // Include the Redis connection file
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis");
}

// Check if 'dno' is set in the URL
if (isset($_GET['dno'])) {
    // Get department number from URL and sanitize it
    $dno = intval($_GET['dno']);

    // Define the Redis key for the department
    $departmentKey = "DEPARTMENT:$dno";  // Key for the department details

    // Retrieve department data from Redis
    $departmentInfo = $redis->hGetAll($departmentKey);

    if ($departmentInfo) {
        // Initialize an array to store the employees
        $employees = [];

        // Get all employee keys in Redis
        $employeeKeys = $redis->keys("EMPLOYEE:*");

        // Loop through all employee keys and retrieve employee data
        foreach ($employeeKeys as $employeeKey) {
            // Get employee data from Redis
            $employeeData = $redis->hGetAll($employeeKey);

            // Check if the employee belongs to the department (matching dno)
            if ($employeeData['dno'] == $dno) {
                // Store employee details (lname, salary) in the employees array
                $employees[] = [
                    'lname' => $employeeData['lname'],
                    'salary' => $employeeData['salary']
                ];
            }
        }

        // Check if any employees were found
        if ($employees) {
            echo '<h4 class="mt-4">Employees in Department ' . htmlspecialchars($dno) . '</h4>';

            // Display table with Bootstrap classes
            echo '<table class="table table-striped table-bordered mt-3">
                    <thead class="table-warning">
                        <tr>
                            <th scope="col">Last Name</th>
                            <th scope="col">Salary</th>
                        </tr>
                    </thead>
                    <tbody>';

            // Loop through employees and display their last name and salary
            foreach ($employees as $emp) {
                echo '<tr>
                        <td>' . htmlspecialchars($emp['lname']) . '</td>
                        <td>$' . number_format(htmlspecialchars($emp['salary']), 2) . '</td>
                      </tr>';
            }

            echo '  </tbody>
                  </table>';
        } else {
            echo '<h4 class="text-warning">No employees found in Department ' . htmlspecialchars($dno) . '.</h4>';
        }
    } else {
        echo '<h4 class="text-warning">No department found with number ' . htmlspecialchars($dno) . ' in Redis.</h4>';
    }
} else {
    echo '<h4 class="text-danger">No department number provided. Please specify a department number.</h4>';
}

// Close the Redis connection
$redis->close();
?>
