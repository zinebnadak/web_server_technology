<?php
session_start();

// LOCALS
$pagename = "password_forgot_doit.php";
$allowedpage = "password_forgot.php";

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

// Hämta värden utan hashing
$employeecode = $_SESSION["employeecode"] ?? '';
$raw_old      = $_POST["old_password"] ?? '';
$raw_new      = $_POST["new_password"] ?? '';
$raw_confirm  = $_POST["confirm_password"] ?? '';

// Kontrollera att de nya lösenorden matchar
if ($raw_new !== $raw_confirm) 
{
    echo "De nya lösenorden matchar inte varandra! <a href='javascript:history.back()'>Back</a>";
    exit();
}

// Kontrollera minst 8 tecken
if (strlen($raw_new) < 8) 
{
    echo "Det nya lösenordet måste vara minst 8 tecken långt! <a href='javascript:history.back()'>Back</a>";
    exit();
}

// Kontrollera att det innehåller minst en versal
if (!preg_match('/[A-Z]/', $raw_new)) 
{
    echo "Det nya lösenordet måste innehålla minst en stor bokstav! <a href='javascript:history.back()'>Back</a>";
    exit();
}

// Kontrollera att det innehåller minst ett specialtecken
if (!preg_match('/[^a-zA-Z0-9]/', $raw_new)) 
{
    echo "Det nya lösenordet måste innehålla minst ett specialtecken! <a href='javascript:history.back()'>BAck</a>";
    exit();
}

// Hasha lösenorden 
$old_password = hash('sha256', $raw_old);
$new_password = hash('sha256', $raw_new);

// Kontrollera att det gamla lösenordet stämmer i databasen
$result = mysqli_query($conn, "SELECT passwd FROM users WHERE employeecode='$employeecode'");

if ($row = mysqli_fetch_assoc($result)) 
{
    if ($row["passwd"] !== $old_password) 
    {
        echo "Det gamla lösenordet var felaktigt! <a href='index.php'>Back</a>";
        exit();
    }
} 
else 
{
    echo "Användaren hittades inte.";
    exit();
}

// Uppdatera till det nya lösenordet i databasen
$update = mysqli_query($conn, "UPDATE users SET passwd='$new_password' WHERE employeecode='$employeecode'");

if ($update) 
{
    // upgift 12 aktivitetslogg
    logactivity($employeecode, date("Y-m-d"), date("H:i:s"), "Password changed", $employeecode, "User $employeecode changed their password via forgot-password flow", "Password");
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body 
            {
                font-family: sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 85vh;
                margin: 0;
            }
            .card 
            {
                text-align: center;
                padding: 10px;
            }
            .icon-circle 
            {
                width: 50px;
                height: 50px;
                background-color: #28a745;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
                margin: 0 auto 15px auto;
            }
            h3 
            {
                margin: 0 0 8px 0; 
                color: #333; 
            }
            p 
            { 
                font-size: 13px; 
                color: #666; 
                margin-bottom: 20px; 
            }
            .btn 
            {
                background-color: #007bff;
                color: white;
                border: none;
                padding: 8px 18px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }
            .btn:hover 
            { 
                background-color: #0056b3; 
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icon-circle">✓</div>
            <h3>Klart!</h3>
            <p>Ditt lösenord har uppdaterats.</p>
            <button class="btn" onclick="window.top.location.reload();">Stäng</button>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>