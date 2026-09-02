<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbhost = "localhost";
$dbname = "thenest_umbrellacorp_top";
$dbuser = "mysqluser";
$dbpassword = "umbrella";

// Create the connection
$conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

// Check the connection
if (!$conn)
{
    die("Connection failed: " . mysqli_connect_error());
}

// Run SQL-statement
/*if($result = mysqli_query($conn, "SELECT * FROM users WHERE employeecode='$employeecode' AND passwd='password'"))
{
    // Loopa genom resultat-arrayen
    while($row = mysqli_fetch_assoc($result))
    {
        // Läs in värden
        $id = $row["id"];
        $logintimes = $row["logintimes"];
        $lastlogin = $row["lastlogin"];
        $lockout = $row["lockout"];
        $loggedin = "ok";
    }
}*/

?>
