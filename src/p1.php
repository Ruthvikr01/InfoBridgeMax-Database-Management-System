<?php
include_once "RedisConnect.php";

// Create a Redis connection
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis database");
}

// Retrieve all SSNs from Redis (keys of type EMPLOYEE:SSN)
$keys = $redis->keys("EMPLOYEE:*");
if (!$keys || count($keys) === 0) {
    die("No employees found in Redis.");
}

// Extract SSNs from the keys
$ssns = array_map(function ($key) {
    return explode(":", $key)[1]; // Extract SSN from EMPLOYEE:SSN key
}, $keys);
?>

<h4 class="text-center">Employee Details for:</h4>
<div class="d-flex justify-content-center">
    <form method="post" action="?page=empdir" class="d-flex align-items-center">
        <div class="dropdown me-2">
            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Select Employee SSN
            </button>
            <ul class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenuButton">
                <?php foreach ($ssns as $ssn): ?>
                    <li>
                        <a class="dropdown-item" href="#" onclick="selectSSN('<?php echo htmlspecialchars($ssn); ?>');">
                            <?php echo htmlspecialchars($ssn); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Hidden input to hold the selected SSN -->
        <input type="hidden" name="ssn" id="selectedSsn">

        <button type="submit" class="btn btn-warning">Get Employee Details</button>
    </form>
</div>

<script>
    // JavaScript function to update dropdown button text and hidden input
    function selectSSN(ssn) {
        document.getElementById('dropdownMenuButton').innerText = ssn;
        document.getElementById('selectedSsn').value = ssn;
    }
</script>

<style>
    /* Add custom CSS for scrollable dropdown */
    .scrollable-menu {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
