<?php
//echo phpinfo();

// ----------------- DISPLAY ALL ERRORS ------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------- DATABASE CONNECTION -----------------------
$dbhost = "localhost";
$dbname = "thenest_umbrellacorp_top";
$dbuser = "mysqluser";
$dbpassword = "thenest_appuser"; 

$conn = new mysqli($dbhost, $dbuser, $dbpassword, $dbname);
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}
?>