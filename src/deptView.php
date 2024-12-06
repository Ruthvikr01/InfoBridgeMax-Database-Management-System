<?php
include_once "RedisConnect.php";
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis");
}

// Check if dnumber is set in the URL
if (!isset($_GET['dnumber'])) {
    echo "Department number not provided.";
    exit; // Stop further execution
}

// Get department number from URL
$dno = $_GET['dnumber'];

// Define the Redis key for department cache
$cacheKey = "DEPARTMENT:$dno";

// Try fetching the department details from Redis cache
$deptInfo = $redis->hGetAll($cacheKey);

if ($deptInfo) {
    // If department info is found in Redis, fetch the manager's SSN
    $mgrssn = $deptInfo['mgrssn']; // Get the manager's SSN

    // Define the Redis key for the manager's details
    $managerKey = "EMPLOYEE:$mgrssn";

    // Try fetching the manager details from Redis cache
    $managerInfo = $redis->hGetAll($managerKey);

    // Display the department and manager info
    echo '<div class="d-flex justify-content-center mt-5">'; // Center horizontally and add top margin
    echo '  <div class="card" style="width: 18rem;">'; // Adjust the width for a square-like shape
    echo '    <div class="card-body text-center">'; // Center text in the card
    echo '      <h5 class="card-title">Department: ' . htmlspecialchars($deptInfo['dname']) . '</h5>';
    
    // Check if manager info is found
    if ($managerInfo) {
        echo '      <h6 class="card-subtitle mb-2 text-body-secondary">Manager: ' . htmlspecialchars($managerInfo['fname']) . ' ' . htmlspecialchars($managerInfo['lname']) . '</h6>';
    } else {
        echo '      <h6 class="card-subtitle mb-2 text-body-secondary">Manager: Not Found</h6>';
    }

    echo '      <p class="card-text">Manager Start Date: ' . htmlspecialchars($deptInfo['mgrstartdate']) . '</p>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
} else {
    // If department info is not found in Redis
    echo "<p>Department not found in cache.</p>";
}

// Close the Redis connection
$redis->close();
?>
