
<?php
session_start();

// LOCALS
$pagename = "logout_doit.php";
$allowedpage = "index.php";

// Kontrollera att anropet kommer från rätt sida
if (!isset($_SESSION['pagename']) || $_SESSION['pagename'] !== $allowedpage) 
{
    echo "Åtkomst nekad: Denna åtgärd måste utföras från rätt sida. <a href='javascript:history.back()'>Gå tillbaka</a>";
    exit();
}
?>
<?php 
include("_security.php");
include("_config.php");
include("_globals.php");


unset($_SESSION["loggedin"]);
unset($_SESSION["employeecode"]);



// Skicka användaren direkt till startsidan
header("Location: index.php");
exit(); // Stoppar exekveringen så att inget mer körs
?>