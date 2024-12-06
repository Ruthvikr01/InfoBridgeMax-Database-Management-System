<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>

<h2>Department List</h2>
<?php
// Include Redis connection file
include_once "RedisConnect.php";

// Create a connection to Redis
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis");
}

// Fetch department keys from Redis
$departmentKeys = $redis->keys("DEPARTMENT:*");

if ($departmentKeys) {
    $departments = [];

    // Retrieve department details and store them in an array
    foreach ($departmentKeys as $key) {
        $departmentData = $redis->hGetAll($key);
        $dnumber = htmlspecialchars($departmentData['dnumber']);
        $dname = htmlspecialchars($departmentData['dname']);
        
        // Store department data in the array using dnumber as the key
        $departments[$dnumber] = [
            'dnumber' => $dnumber,
            'dname' => $dname
        ];
    }

    // Sort the departments by dnumber (numeric sort)
    ksort($departments, SORT_NUMERIC);

    // Display the table
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="table-warning">';
    echo '<tr><th>Department Number</th><th>Department Name</th></tr>';
    echo '</thead>';
    echo '<tbody>';

    // Loop through the sorted departments and display them in the table
    foreach ($departments as $department) {
        $dnumber = $department['dnumber'];
        $dname = $department['dname'];

        echo '<tr>';
        echo '<td><a href="departmentDetails.php?dnumber=' . $dnumber . '">' . $dnumber . '</a></td>';
        echo '<td>' . $dname . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No departments found in Redis.</p>';
}
?>
</body>
</html>
