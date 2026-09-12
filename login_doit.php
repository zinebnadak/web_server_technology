<?php
session_start();

// LOCALS
$pagename = "login_doit.php";
$allowedpage = "index.php";

// Kontrollera att anropet kommer från rätt sida
if (!isset($_SESSION['pagename']) || $_SESSION['pagename'] !== $allowedpage) 
{
    echo "Åtkomst nekad: Denna åtgärd måste utföras från rätt sida. <a href='javascript:history.back()'>Gå tillbaka</a>";
    exit();
}
?>
<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>

<?php 
// ----- Locals: ------

?>

<?php include("_master_head.php") ?>     
<div class="grid-container">

    <!---- GRID ROW 1 START ------------------------------------------>
    <div class="grid-emptyblack" ></div>
    <div class="grid-header">
        <?php include("_master_header.php") ?>     
    </div>
    <div class="grid-emptyblack"></div>
    <!---- GRID ROW 1 END --------------------------------------------->

    <!---- GRID ROW 2 START ------------------------------------------->
    
        <?php include("_master_breadcrum.php") ?>  

    <!---- GRID ROW 2 END --------------------------------------------->

    <!---- GRID ROW 3 START ------------------------------------------->
    <div class="grid-topmenu">
        <?php include("_master_menu.php") ?>
    </div>
    <!---- GRID ROW 3 END -------------------------------------------->

    <!---- GRID ROW 4 START ------------------------------------------>
    <div class="grid-empty"></div>
    <div class="grid-main">

<?php
$employeecode = $_POST["employeecode"];
$password = $_POST["password"];
$loggedin="no";
$lockout="";

if($result = mysqli_query($conn, "SELECT * FROM users WHERE employeecode='$employeecode' AND passwd='$password'"))
{
    while($row = mysqli_fetch_assoc($result))
    {
        $id = $row["id"];
        $logintimes = $row["logintimes"];
        $lastlogin = $row["lastlogin"];
        $lockout = $row["lockout"];
        $lastlogintimes = $row["lastlogintimes"];
        $loggedin = "ok";
    }
}

// Inloggningen lyckades
if($loggedin == "ok" && $lockout!="x")
{
    // Lägger till varje inloggning
    $logintimes = $logintimes +1;

    // Hämntar namn från employee
    $employee_result = mysqli_query($conn, "SELECT name, securityAccessLevel FROM employee WHERE id='$id'");
    $employee_name="";
    $securityAccessLevel="";
    if($employee_row = mysqli_fetch_assoc($employee_result))
    {
        $employee_name = $employee_row["name"];
        $securityAccessLevel = $employee_row["securityAccessLevel"];
    } 

    // Skapa sessionerna
    $_SESSION["loggedin"] = "ok";
    $_SESSION["employeename"] = $employee_name;
    $_SESSION["employeecode"] = $employeecode;
    $_SESSION["logintimes"] = $logintimes;
    $_SESSION["lastlogin"] = $lastlogin;
    $_SESSION["lastlogintimes"] = $lastlogintimes;
    $_SESSION["fromhost"] = $_SERVER['REMOTE_ADDR'];

    // Spara security level
    $_SESSION["securityAccessLevel"] = $securityAccessLevel;
    
    // Uppdatera db med ny info (datum o antal)
    mysqli_query($conn, "UPDATE users SET logintimes='$logintimes', lastlogin=CURDATE(), lastlogintimes=CURTIME() WHERE id='$id'");
    header("Location: index.php");
    exit();

    echo "SUCCESS";
}
else
{
    header("Location: index.php?error=wrong_password");
    exit();
}
?>
    <?php include("_master_info-middle.php") ?>
    </div>
        <div class="grid-rightmenu">        
        <?php include("_master_info-menu.php") ?>
    </div>
    <div class="grid-empty"></div>
    <!---- GRID ROW 4 END --------------------------------------------->

    <div class="grid-emptyblack" ></div>
    <div class="grid-footer">
        <?php include("_master_footer.php") ?>
    </div>
    <div class="grid-emptyblack"></div>

</div>

<?php include("_master_bottom.php") ?>
?>

