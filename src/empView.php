<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> <!-- Google Font -->
    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Apply the Roboto font */
            background: linear-gradient(to bottom right, #E2F0D9 0%, #A0D9E8 100%); /* Optional gradient background */
            min-height: 100vh; /* Ensure the body takes up the full viewport height */
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <?php
            // Include Redis connection class
            include_once "RedisConnect.php";

            // Initialize Redis connection
            $redisConnection = new RedisConnect();
            $redis = $redisConnection->connect();

            if (!$redis) {
                die("Failed to connect to Redis");
            }

            // Get SSN from the URL
            $ssn = isset($_GET['ssn']) ? intval($_GET['ssn']) : null;

            if ($ssn) {
                // Define the Redis key for the employee using the SSN
                $redisKey = "EMPLOYEE:$ssn";

                // Fetch the employee data from Redis (HGETALL to get all fields of the hash)
                $employeeData = $redis->hGetAll($redisKey);

                // Check if employee data exists in Redis
                if ($employeeData) {
                    // Display employee details
                    echo '<div class="card mb-4">';
                    echo '  <div class="card-body">';
                    echo '    <h5 class="card-title">Employee: ' . htmlspecialchars($employeeData['fname']) . ' ' . htmlspecialchars($employeeData['minit']) . ' ' . htmlspecialchars($employeeData['lname']) . '</h5>';
                    echo '    <p class="card-text"><b>SSN:</b> ' . htmlspecialchars($employeeData['ssn']) . '</p>';
                    echo '    <p class="card-text"><b>Date of Birth:</b> ' . htmlspecialchars($employeeData['bdate']) . '</p>';
                    echo '    <p class="card-text"><b>Address:</b> ' . htmlspecialchars($employeeData['address']) . '</p>';
                    echo '    <p class="card-text"><b>Sex:</b> ' . htmlspecialchars($employeeData['sex']) . '</p>';
                    echo '    <p class="card-text"><b>Salary:</b> $' . htmlspecialchars(number_format($employeeData['salary'], 2)) . '</p>';
                    echo '    <p class="card-text"><b>Supervisor SSN:</b> ' . htmlspecialchars($employeeData['superssn']) . '</p>';
                    echo '    <p class="card-text"><b>Department Number:</b> ' . htmlspecialchars($employeeData['dno']) . '</p>';
                    echo '    <p class="card-text"><b>Joined At:</b> ' . htmlspecialchars($employeeData['createdAt']) . '</p>';
                    echo '    <p class="card-text"><b>Last Update:</b> ' . htmlspecialchars($employeeData['updatedAt']) . '</p>';
                    echo '  </div>';
                    echo '</div>';
                } else {
                    echo "<p class='text-center'>Employee not found in Redis.</p>";
                }
            } else {
                echo "<p class='text-center'>No SSN specified.</p>";
            }

            // Close the Redis connection
            $redis->close();
            ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
