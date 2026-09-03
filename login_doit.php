<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>

<?php
$employeecode = $_POST["employeecode"];
$password = $_POST["password"];
$loggedin="no";
$lockout="";

if($result = mysqli_query($conn, "SELECT * FROM users WHERE employeecode='$employeecode' AND passwd='$password'"))
{
    while($row = msqli_fetch_assoc($result))
    {
        $id = $row["id"];
        $logintimes = $row["logintimes"];
        $lastlogin = $row["lastlogin"];
        $lockout = $row["lockout"];
        $loggedin = "ok";
    }
}

// Inloggningen lyckades
if($loggedin == "ok" && $lockout!="x")
{
    // Skapa sessionerna
    $_SESSION["loggesin"] = "ok";
    $_SESSION["employeecode"] = $employeecode;
    
    echo "SUCCESS";
}
else
{
    echo "UNSUCCESS";
}

?>