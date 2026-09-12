
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// Säkerställ databasanslutning
if (!isset($conn)) 
{
    if (file_exists("_config.php")) 
    {
        include_once("_config.php");
    }
}

if (!isset($conn)) 
{
    die("<p style='color:red;'>Kunde inte ansluta till databasen.</p>");
}

// Kontrollera att formuläret skickats via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    // Hämta alla fält från $_POST
    $employeeCode       = trim($_POST['femployeecode'] ?? '');
    $securityAccessLevel= trim($_POST['fsecurityaccess'] ?? 'C');
    $name               = trim($_POST['fname'] ?? '');
    $dateOfBirth        = trim($_POST['fdateofbirth'] ?? '');
    $sex                = trim($_POST['fsex'] ?? 'Other');
    $bloodType          = trim($_POST['fbloodtype'] ?? '');
    $height             = trim($_POST['fheight'] ?? '');
    $weight             = trim($_POST['fweight'] ?? '');
    $department         = trim($_POST['fdepartment'] ?? '');
    $rank               = trim($_POST['frank'] ?? '');
    $background         = trim($_POST['fbackground'] ?? '');
    $strengths          = trim($_POST['fstrengths'] ?? '');
    $weaknesses         = trim($_POST['fweaknesses'] ?? '');

    // Validera att vi faktiskt har en employeeCode
    if (empty($employeeCode)) 
    {
        die("<p style='color:red;'>Saknar Employee Code. Kan inte uppdatera.</p>");
    }

    // Hantera bilduppladdning (om en ny bild skickades med)
    $photoFilename = null; // Sätts om ny bild laddas upp

    if (isset($_FILES['ffile']) && $_FILES['ffile']['error'] === UPLOAD_ERR_OK) 
    {
        $fileTmpPath   = $_FILES['ffile']['tmp_name'];
        $fileName      = $_FILES['ffile']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) 
        {
            // Döp bilden efter employeeCode
            $newFileName = $employeeCode . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/photos/';

            // Skapa mappen om den inte skulle finnas
            if (!is_dir($uploadFileDir)) 
            {
                mkdir($uploadFileDir, 0755, true);
            }

            $destPath = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) 
            {
                $photoFilename = $newFileName;
            }
        }
    }

    // Bygg SQL UPDATE-satsen
    if ($photoFilename !== null) 
    {
        // Om ny bild laddades upp, uppdatera även photo-kolumnen
        $sql = "UPDATE employee SET 
                    securityAccessLevel = ?, 
                    name = ?, 
                    dateOfBirth = ?, 
                    sex = ?, 
                    bloodType = ?, 
                    height = ?, 
                    weight = ?, 
                    department = ?, 
                    rank = ?, 
                    background = ?, 
                    strengths = ?, 
                    weaknesses = ?, 
                    photo = ? 
                WHERE employeeCode = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssss", 
            $securityAccessLevel, 
            $name, 
            $dateOfBirth, 
            $sex, 
            $bloodType, 
            $height, 
            $weight, 
            $department, 
            $rank, 
            $background, 
            $strengths, 
            $weaknesses, 
            $photoFilename, 
            $employeeCode
        );
    } 
    else 
    {
        // Om ingen ny bild laddades upp, behåll befintligt bildnamn i databasen
        $sql = "UPDATE employee SET 
                    securityAccessLevel = ?, 
                    name = ?, 
                    dateOfBirth = ?, 
                    sex = ?, 
                    bloodType = ?, 
                    height = ?, 
                    weight = ?, 
                    department = ?, 
                    rank = ?, 
                    background = ?, 
                    strengths = ?, 
                    weaknesses = ? 
                WHERE employeeCode = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssss", 
            $securityAccessLevel, 
            $name, 
            $dateOfBirth, 
            $sex, 
            $bloodType, 
            $height, 
            $weight, 
            $department, 
            $rank, 
            $background, 
            $strengths, 
            $weaknesses, 
            $employeeCode
        );
    }

    // Exekvera och skicka användaren vidare
    if ($stmt->execute()) 
    {
        $stmt->close();
        // Omdirigera tillbaka till den anställdes profilsida eller lista
        $redirectUrl = "index.php?site=personnelregistry_read_employee&code=" . urlencode($employeeCode);

        echo "<script>window.location.href = " . json_encode($redirectUrl) . ";</script>";
        echo "<p>Uppdateringen lyckades! Om du inte skickas vidare automatiskt, <a href='" . htmlspecialchars($redirectUrl) . "'>klicka här</a>.</p>";
        exit();
    } 
    else 
    {
        echo "<p style='color:red;'>Fel vid uppdatering: " . htmlspecialchars($stmt->error) . "</p>";
        $stmt->close();
    }

} 
else 
{
    echo "<p style='color:red;'>Ogiltig förfrågan.</p>";
}
?>