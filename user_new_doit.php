<?php
session_start();

// LOCALS
$pagename = "user_new_doit.php";
$allowedpage = "user_new.php";

// Kontrollera att anropet kommer från rätt sida
if (!isset($_SESSION['pagename']) || $_SESSION['pagename'] !== $allowedpage) 
{
    echo "Åtkomst nekad: Denna åtgärd måste utföras från rätt sida. <a href='javascript:history.back()'>Gå tillbaka</a>";
    exit();
}
// Kontrollera att användaren fortfarande har behörighetsnivå A
if (!isset($_SESSION["securityaccesslevel"]) || $_SESSION["securityaccesslevel"] !== "A") 
{
    echo "Åtkomst nekad: Du saknar behörighet att lägga till användare.";
    exit();
}
?>