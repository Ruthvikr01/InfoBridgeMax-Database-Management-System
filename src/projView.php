<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Details</title>
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
            // Database connection parameters (DbConnect.php contains the Redis connection code)
            include_once "RedisConnect.php";
            $redisConnection = new RedisConnect();
            $redis = $redisConnection->connect();
            
            if (!$redis) {
                die("Failed to connect to Redis");
            }
            
            // Get project number from the URL
            $pnumber = isset($_GET['pnumber']) ? intval($_GET['pnumber']) : null;

            if ($pnumber) {
                // Define the Redis key for the project details
                $projectKey = "PROJECT:$pnumber";

                // Check if the project exists in Redis
                if ($redis->exists($projectKey)) {
                    // Retrieve project details from Redis
                    $project = $redis->hGetAll($projectKey);

                    // Display project details if available
                    if ($project) {
                        echo '<div class="card mb-4 text-center">';
                        echo '  <div class="card-body">';
                        echo '    <h5 class="card-title">Project Details</h5>';
                        echo '    <p class="card-text"><strong>Project Number:</strong> ' . htmlspecialchars($project['pnumber']) . '</p>';
                        echo '    <p class="card-text"><strong>Project Name:</strong> ' . htmlspecialchars($project['pname']) . '</p>';
                        echo '    <p class="card-text"><strong>Location:</strong> ' . htmlspecialchars($project['plocation']) . '</p>';
                        echo '    <p class="card-text"><strong>Department Number:</strong> ' . htmlspecialchars($project['dnum']) . '</p>';
                        echo '    <p class="card-text"><strong>Assigned On:</strong> ' . htmlspecialchars($project['createdAt']) . '</p>';
                        echo '    <p class="card-text"><strong>Last Update:</strong> ' . htmlspecialchars($project['updatedAt']) . '</p>';
                        echo '  </div>';
                        echo '</div>';
                    } else {
                        echo "<p class='text-center'>Project not found.</p>";
                    }
                } else {
                    echo "<p class='text-center'>Project not found in Redis.</p>";
                }
            } else {
                echo "<p class='text-center'>No project number specified.</p>";
            }

            ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
