// övning 13, forskningsdatabas 
// fil som sparar i databasen. Och varje research object ska ha en egen mapp /ResearchObjects/TCLxx/ med undermappar files/ och images/

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$allowedpage = "research_add.php";
if (!isset($_SESSION['pagename']) || $_SESSION['pagename'] !== $allowedpage) 
{
    echo "Åtkomst nekad: Denna åtgärd måste utföras från rätt sida. <a href='javascript:history.back()'>Gå tillbaka</a>";
    exit();
}

// Security Access Level B eller högre
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');
if (!in_array(strtoupper($currentUserAccessLevel), ['A', 'B'])) 
{
    die("<p style='color:red;'>Åtkomst nekad: Det krävs säkerhetsnivå B eller högre.</p>");
}

if (!isset($conn)) 
{
    if (file_exists("_config.php")) {
        include_once("_config.php");
    }
}
if (file_exists("_globals.php")) 
{
    include_once("_globals.php");
}
if (!isset($conn)) 
{
    die("<p style='color:red;'>Kunde inte ansluta till databasen.</p>");
}

// Hämta formulärdata
$number  = trim($_POST['fnumber'] ?? '');
$name    = trim($_POST['fname'] ?? '');
$text    = trim($_POST['ftext'] ?? '');
$status  = trim($_POST['fstatus'] ?? 'Open');
$presentationVideo = trim($_POST['fpresentationvideo'] ?? '');
$handlingVideo     = trim($_POST['fhandlingvideo'] ?? '');
$createdBy = $_SESSION['employeecode'] ?? 'SYSTEM';

if (empty($number) || empty($name)) 
{
    echo "<script>alert('Number och Name krävs!'); window.history.back();</script>";
    exit();
}

// Skapa mappstruktur för research object: /ResearchObjects/<number>/files och /images
// Rensa bort # och andra otillåtna tecken ur mappnamnet
$safeFolderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $number);
$baseDir = __DIR__ . '/ResearchObjects/' . $safeFolderName;

if (!is_dir($baseDir)) 
{
    mkdir($baseDir, 0755, true);
    mkdir($baseDir . '/files', 0755, true);
    mkdir($baseDir . '/images', 0755, true);
}

// Hantera uppladdning av säkerhetsdatablad (endast PDF)
$dataSheetFilename = '';
if (isset($_FILES['fdatasheet']) && $_FILES['fdatasheet']['error'] === UPLOAD_ERR_OK) 
{
    $fileExtension = strtolower(pathinfo($_FILES['fdatasheet']['name'], PATHINFO_EXTENSION));
    if ($fileExtension === 'pdf') 
    {
        $dataSheetFilename = str_replace(' ', '_', $_FILES['fdatasheet']['name']);
        move_uploaded_file($_FILES['fdatasheet']['tmp_name'], $baseDir . '/files/' . $dataSheetFilename);
    }
}

// Spara i databasen
$sql = "INSERT INTO ResearchObjects (number, name, createdDate, createdBy, text, status, securityDataSheet, securityPresentationVideo, securityHandlingVideo)
        VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $number, $name, $createdBy, $text, $status, $dataSheetFilename, $presentationVideo, $handlingVideo);

if ($stmt->execute()) 
{
    $newId = $conn->insert_id;
    
    logactivity($createdBy, date("Y-m-d"), date("H:i:s"), "New research added", $number, "New research object '$name' ($number) was added", "Research database");
    
    $stmt->close();
    header("Location: index.php?site=research_read_object&id=" . $newId);
    exit();
} 
else 
{
    echo "<p style='color:red;'>Databasfel: " . htmlspecialchars($stmt->error) . "</p>";
    $stmt->close();
}
?>
