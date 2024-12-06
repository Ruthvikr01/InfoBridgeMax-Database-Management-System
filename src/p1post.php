<?php
include_once "RedisConnect.php";
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis");
}

// Retrieve SSN from form
$ssn = $_POST['ssn'];

// Define the Redis key for caching employee details
$employeeKey = "EMPLOYEE:$ssn";

// Check if the employee details are already cached in Redis
if ($redis->exists($employeeKey)) {
    // Retrieve employee details from Redis
    $employee = $redis->hGetAll($employeeKey);
} else {
    echo "<p class='text-center'>Employee not found in Redis.</p>";
    exit;
}

// Display employee information if available
if ($employee) {
    echo '<div class="d-flex justify-content-center mt-4">';
    echo '  <div class="card" style="width: 20rem;">';
    echo '    <div class="card-body">';
    echo '      <h5 class="card-title">' . htmlspecialchars($employee['fname']) . ' ' . htmlspecialchars($employee['minit']) . ' ' . htmlspecialchars($employee['lname']) . '</h5>';
    echo '      <h6 class="card-subtitle mb-2 text-body-secondary">Sex: ' . htmlspecialchars($employee['sex']) . '</h6>';
    echo '      <p class="card-text">Address: ' . htmlspecialchars($employee['address']) . '</p>';
    echo '      <p class="card-text">Birth Date: ' . htmlspecialchars($employee['bdate']) . '</p>';
    echo '      <p class="card-text">Department Number: ' . htmlspecialchars($employee['dno']) . '</p>';
    echo '    <p class="card-text"><b>Joined At:</b> ' . htmlspecialchars($employee['createdAt']) . '</p>';
    echo '    <p class="card-text"><b>Last Update:</b> ' . htmlspecialchars($employee['updatedAt']) . '</p>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
} else {
    echo "<p class='text-center'>Employee not found.</p>";
}
?>
