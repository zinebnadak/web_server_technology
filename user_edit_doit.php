<?php
session_start();

// LOCALS
$pagename = "user_edit_doit.php";
$allowedpage = "user_edit.php";

// 1. Kontrollera att anropet kommer från rätt sida
if (!isset($_SESSION['pagename']) || $_SESSION['pagename'] !== $allowedpage) 
{
    echo "Åtkomst nekad: Denna åtgärd måste utföras från rätt sida. <a href='javascript:history.back()'>Gå tillbaka</a>";
    exit();
}

// 2. Kontrollera att användaren har behörighetsnivå A
$userLevel = $_SESSION['securityAccessLevel'] ?? $_SESSION['securityaccesslevel'] ?? '';
if (strtoupper($userLevel) !== 'A') 
{
    echo "Åtkomst nekad: Du saknar behörighet att redigera användare.";
    exit();
}

// Importera databasinställningar, och globals för uppg 12
include_once("_config.php");
include_once("_globals.php");

// 3. Hantera POST-begäran och uppdatera databasen
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Bestäm om det är en känd employeeCode eller fritt användarnamn ("or other user name")
    $selectVal = $_POST['employeeCodeSelect'] ?? '';
    $otherVal  = trim($_POST['otherCodeInput'] ?? '');
    $finalCode = ($selectVal === 'other') ? $otherVal : $selectVal;
    
    // Om vi redigerade en nyckel sätter vi ursprunglig kod
    $originalCode = $_POST['originalCode'] ?? $finalCode;

    $name     = $_POST['realName'] ?? '';
    $email    = $_POST['email'] ?? '';
    $lockout  = isset($_POST['lockout']) ? 'X' : '';
    $password = $_POST['password'] ?? '';

    if (!empty($finalCode)) 
    {
        // A. Uppdatera users-tabellen (email och lockout)
        $sql_users = "UPDATE users 
                      SET employeeCode = '$finalCode', email = '$email', lockout = '$lockout' 
                      WHERE employeeCode = '$originalCode'";
        mysqli_query($conn, $sql_users);

        // B. Uppdatera employee-tabellen om det är en befintlig anställd
        if ($selectVal !== 'other') {
            $sql_emp = "UPDATE employee 
                        SET name = '$name' 
                        WHERE employeeCode = '$finalCode'";
            mysqli_query($conn, $sql_emp);
        }

        // C. HÄR PLACERAS LÖSENORDSKODEN
        if (!empty($password)) {
            $hasUpperCase   = preg_match('/[A-Z]/', $password);
            $hasSpecialChar = preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]/', $password);

            if (strlen($password) < 8 || !$hasUpperCase || !$hasSpecialChar) {
                die("Fel: Lösenordet måste vara minst 8 tecken långt, innehålla minst en versal och minst ett specialtecken. <a href='javascript:history.back()'>Gå tillbaka</a>");
            }

            if ($password !== ($_POST['retypePassword'] ?? '')) {
                die("Fel: Lösenorden matchar inte. <a href='javascript:history.back()'>Gå tillbaka</a>");
            }

            // Hasha lösenordet och uppdatera
            $hashedPassword = hash('sha256', $password);
            
            $sql_pass = "UPDATE users SET password = '$hashedPassword' WHERE employeeCode = '$finalCode'";
            mysqli_query($conn, $sql_pass);
        }

        // Skicka tillbaka till användarlistan efter sparning
        // övning 12 aktivitetslogg
        logactivity($_SESSION['employeecode'] ?? 'SYSTEM', date("Y-m-d"), date("H:i:s"), "User account edited", $finalCode, "User account for $finalCode was updated", "Users");

        header("Location: index.php?site=user_read&msg=updated");
        exit();
    }
}

// Om något gick fel
header("Location: index.php?site=user_read&error=failed");
exit();
?>