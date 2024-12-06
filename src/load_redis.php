<?php
require_once 'RedisConnect.php';
$redisConnection = new RedisConnect();
$redis = $redisConnection->connect();

if (!$redis) {
    die("Failed to connect to Redis");
}

$files = [
    'DEPARTMENT' => "/var/www/html/data/department.dat",
    'PROJECT' => "/var/www/html/data/project.dat",
    'DEPT_LOCATIONS' => "/var/www/html/data/dloc.dat",
    'EMPLOYEE' => "/var/www/html/data/employee.dat",
    'DEPENDENT' => "/var/www/html/data/dependent.dat",
    'WORKS_ON' => "/var/www/html/data/worksOn.dat",
];

function loadDepartmentData($redis, $filePath) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            $line = str_replace('"', '', $line);
            $data = explode(",", $line); 
            if (count($data) == 4) {
                $dname = trim($data[0]);
                $dnumber = trim($data[1]);
                $mgrssn = trim($data[2]);
                $mgrstartdate = trim($data[3]);
                $redis->hMSet("DEPARTMENT:$dnumber", [
                    "dname" => $dname,
                    "dnumber" => $dnumber,
                    "mgrssn" => $mgrssn,
                    "mgrstartdate" => $mgrstartdate
                ]);
            }
        }
        fclose($file);
    } else {
        echo "Error reading file: $filePath";
    }
}

function loadProjectData($redis, $filePath) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            $line = str_replace('"', '', $line);
            $data = explode(",", $line);
            if (count($data) == 4) {
                $pname = trim($data[0]);
                $pnumber = trim($data[1]);
                $plocation = trim($data[2]);
                $dnum = trim($data[3]);
                $timestamp = date('Y-m-d H:i:s');
                $redis->hMSet("PROJECT:$pnumber", [
                    "pname" => $pname,
                    "pnumber" => $pnumber,
                    "plocation" => $plocation,
                    "dnum" => $dnum,
                    "createdAt" => $timestamp,
                    "updatedAt" => $timestamp,
                ]);
            }
        }
        fclose($file);
    } else {
        echo "Error reading file: $filePath";
    }
}

function loadDeptLocationsData($redis, $filePath) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            $line = str_replace('"', '', $line);
            $data = explode(",", $line); 
            if (count($data) == 2) {
                $dnumber = trim($data[0]);
                $dlocation = trim($data[1]);
                $redis->hMSet("DEPT_LOCATIONS:$dnumber:$dlocation", [
                    "dnumber" => $dnumber,
                    "dlocation" => $dlocation
                ]);
            }
        }
        fclose($file);
    } else {
        echo "Error reading file: $filePath";
    }
}

function loadEmployeeData($redis, $filePath) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) == 10) {
                $fname = trim($data[0]);
                $minit = trim($data[1]);
                $lname = trim($data[2]);
                $ssn = trim($data[3]);
                $bdate = trim($data[4]);
                $address = trim($data[5]);
                $sex = trim($data[6]);
                $salary = trim($data[7]);
                $superssn = trim($data[8]) == "null" ? NULL : trim($data[8]);
                $dno = trim($data[9]);
                $timestamp = date('Y-m-d H:i:s');
                $redis->hMSet("EMPLOYEE:$ssn", [
                    "fname" => $fname,
                    "minit" => $minit,
                    "lname" => $lname,
                    "ssn" => $ssn,
                    "bdate" => $bdate,
                    "address" => $address,
                    "sex" => $sex,
                    "salary" => $salary,
                    "superssn" => $superssn,
                    "dno" => $dno,
                    "createdAt" => $timestamp,
                    "updatedAt" => $timestamp,
                ]);
            }
        }
        fclose($file);
    } else {
        echo "Error reading file: $filePath";
    }
}

function loadDependentData($redis, $filePath) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            $line = str_replace('"', '', $line);
            $data = explode(",", $line); 
            if (count($data) == 5) {
                $essn = trim($data[0]);
                $dependent_name = trim($data[1]);
                $sex = trim($data[2]);
                $bdate = trim($data[3]);
                $relationship = trim($data[4]);
                $redis->hMSet("DEPENDENT:$essn:$dependent_name", [
                    "essn" => $essn,
                    "dependent_name" => $dependent_name,
                    "sex" => $sex,
                    "bdate" => $bdate,
                    "relationship" => $relationship
                ]);
            }
        }
        fclose($file);
    } else {
        echo "Error reading file: $filePath";
    }
}

function loadWorksOnData($redis, $filePath) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            $line = str_replace('"', '', $line);
            $data = explode(",", $line); 
            if (count($data) == 3) {
                $essn = trim($data[0]);
                $pno = trim($data[1]);
                $hours = trim($data[2]);
                $redis->hMSet("WORKS_ON:$essn:$pno", [
                    "essn" => $essn,
                    "pno" => $pno,
                    "hours" => $hours
                ]);
            }
        }
        fclose($file);
    } else {
        echo "Error reading file: $filePath";
    }
}

foreach ($files as $table => $filePath) {
    if (file_exists($filePath)) {
        echo "Loading data from $filePath...<br>";
        switch ($table) {
            case 'DEPARTMENT':
                loadDepartmentData($redis, $filePath);
                break;
            case 'PROJECT':
                loadProjectData($redis, $filePath);
                break;
            case 'DEPT_LOCATIONS':
                loadDeptLocationsData($redis, $filePath);
                break;
            case 'EMPLOYEE':
                loadEmployeeData($redis, $filePath);
                break;
            case 'DEPENDENT':
                loadDependentData($redis, $filePath);
                break;
            case 'WORKS_ON':
                loadWorksOnData($redis, $filePath);
                break;
            default:
                echo "No loader function for $table";
        }
        echo "Done loading $table data.<br>";
    } else {
        echo "File not found: $filePath<br>";
    }
}
?>

