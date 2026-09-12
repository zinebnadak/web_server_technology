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
include("_config.php");
include("_globals.php");


if (!isset($_POST['not_robot'])) 
{
    // Om rutan inte var ikryssad, stoppa exekveringen
    die("Du måste kryssa i 'I am not a robot' för att fortsätta.");
}

// Hämta värden från formuläret
$employeecode = trim($_POST["employeecode"] ?? '');
$email        = trim($_POST["email"] ?? '');



    // Kontrolera att fälten inte är tomma
if (empty($employeecode) || empty($email)) 
{
    echo "Fyll i både Employee Code och e-postadress. <a href='javascript:history.back()'>Gå tillbaka</a>";
    exit();
}

// Kontrollera att användaren och e-postadressen finns i databasen
$stmt = $conn->prepare("SELECT employeeCode FROM users WHERE employeeCode = ? AND email = ?");
$stmt->bind_param("ss", $employeecode, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) 
{
    echo "<div style='font-family: sans-serif; padding: 15px;'>
            <p style='color: red;'>Ingen användare hittades med den angivna koden och e-postadressen.</p>
            <a href='javascript:history.back()'>Gå tillbaka och försök igen</a>
          </div>";
    exit();
}

// Generera ett slumpmässigt lösenord (10 tecken: inkluderar versal och specialtecken)
function generateRandomPassword($length = 10) 
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = 'A!'; // Garanterar minst en versal och ett specialtecken
    for ($i = 2; $i < $length; $i++) 
    {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return str_shuffle($password);
}

$new_plain_password = generateRandomPassword(10);

// Hasha det nya lösenordet med SHA-256
$hashed_password = hash('sha256', $new_plain_password);

// Uppdatera lösenordet i databasen
$update_stmt = $conn->prepare("UPDATE users SET passwd = ? WHERE employeeCode = ?");
$update_stmt->bind_param("ss", $hashed_password, $employeecode);
$success = $update_stmt->execute();

//  Skicka e-post via sendmail() i _globals.php och visa bekräftelsen
if ($success) 
{
    $subject = "Ditt nya lösenord";
    $message = "Hej!\n\nDitt lösenord har återställts.\n\nDitt nya temporära lösenord är: " . $new_plain_password . "\n\nVänligen logga in och byt lösenord så snart som möjligt.";
    
    // Anropar din uppdaterade sendmail-funktion från _globals.php
    sendmail($email, $subject, $message, "Umbrella Support", $employeecode);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
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
                text-align: center; padding: 10px; 
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
                margin: 0 0 8px 0; color: #333; 
            }
            p 
            { 
                font-size: 13px; color: #666; margin-bottom: 20px; 
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
            <h3>E-post skickat!</h3>
            <p>Ett nytt lösenord har skickats till din e-postadress.</p>
            <button class="btn" onclick="window.top.location.href='index.php';">Till inloggningen</button>
        </div>
    </body>
    </html>
    <?php
} else 
{
    echo "Ett fel uppstod när lösenordet skulle sparas i databasen.";
}
?>