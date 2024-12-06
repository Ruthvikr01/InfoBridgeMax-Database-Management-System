<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Bridge Max</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Old+Standard+TT:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #E2F0D9, #A0D9E8);
            color: #333;
            font-family: 'Old Standard TT', serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #E2F0D9, #A0D9E8);
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            font-size: 3rem;
            font-family: 'Bebas Neue', sans-serif;
            font-weight: 700;
            color: #0A1828;
            margin: 0;
            text-transform: uppercase;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            color: #0A1828;
            margin: 0 20px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        nav a:hover {
            text-decoration: underline;
            color: #BFA181;
        }

        .content {
            margin: 20px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            flex-grow: 1;
        }

        footer {
            background: linear-gradient(135deg, #E2F0D9, #A0D9E8);
            color: #0A1828;
            text-align: center;
            padding: 15px;
            font-size: 1rem;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2);
        }

        footer p {
            margin: 0;
            font-weight: 400;
        }
    </style>
</head>
<body>

<header>
    <h1>INFO BRIDGE MAX</h1>
    <nav>
        <a href="?page=empdir"><i class="bi bi-person-rolodex"></i> Employee Directory</a>
        <a href="?page=deptoverview"><i class="bi bi-person-video2"></i> Department Overview</a>
        <a href="?page=empsalary"><i class="bi bi-person-vcard"></i> Employee Salaries by Department</a>
        <a href="?page=deptbrowse"><i class="bi bi-search"></i> Browse Departments</a>
    </nav>
</header>

<div class="content">
    <?php
    include_once "RedisConnect.php";

    $redisConnection = new RedisConnect();
    $redis = $redisConnection->connect();

    if (!$redis) {
        die("Failed to connect to Redis");
    }

    $page = isset($_GET['page']) ? $_GET['page'] : 'empdir';

    switch ($page) {
        case 'empdir':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                include "p1post.php";
            } else {
                include "p1.php";
            }
            break;

        case 'deptoverview':
            if (isset($_GET['dnumber'])) {
                include "deptView.php";
            } else {
                echo '<div class="text-center mb-4">';
                echo '    <h2>Department Overview</h2>';
                echo '    <form method="GET" action="" class="d-flex justify-content-center align-items-center">';
                echo '        <input type="hidden" name="page" value="deptoverview">';
                echo '        <label for="dnumber" class="me-2">Enter Department Number:</label>';
                echo '        <input type="number" name="dnumber" min="1" class="form-control form-control-sm me-2" placeholder="Department #" style="width: auto;" required>';
                echo '        <button type="submit" class="btn btn-warning btn-sm">View Department</button>';
                echo '    </form>';
                echo '</div>';
            }
            break;

        case 'empsalary':
            if (isset($_GET['dno'])) {
                include "empdept.php";
            } else {
                echo '<div class="text-center mb-4">';
                echo '    <h2>Employee Salaries by Department</h2>';
                echo '    <form method="GET" action="" class="d-flex justify-content-center align-items-center">';
                echo '        <input type="hidden" name="page" value="empsalary">';
                echo '        <label for="dno" class="me-2">Enter Department Number:</label>';
                echo '        <input type="number" name="dno" min="1" class="form-control form-control-sm me-2" placeholder="Department #" style="width: auto;" required>';
                echo '        <button type="submit" class="btn btn-warning btn-sm">View Employees</button>';
                echo '    </form>';
                echo '</div>';
            }
            break;

        case 'deptbrowse':
            include "companyBrowse.php";
            break;

        default:
            echo "<h2>Page Not Found</h2><p>The page you're looking for doesn't exist.</p>";
    }
    ?>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Info Bridge Pro</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>

