<?php
require_once 'constants.php';
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'zamfara_crime_db');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Add this to your config file
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
?>
