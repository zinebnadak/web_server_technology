// ÖVNING 13, FORSKNINGSDATABAS 
// sparar filen till rätt mapp med whitelist på filtyper

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
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

$objectId = $_POST['research_object_id'] ?? '';
$author = $_SESSION['employeecode'] ?? 'SYSTEM';

if (empty($objectId) || !is_numeric($objectId)) 
{
    header("Location: index.php?site=research_read");
    exit();
}

// Hämta objektets number för mappnamn
$stmt = $conn->prepare("SELECT number FROM ResearchObjects WHERE id = ?");
$stmt->bind_param("i", $objectId);
$stmt->execute();
$result = $stmt->get_result();
$obj = $result->fetch_assoc();
$stmt->close();

if (!$obj) 
{
    header("Location: index.php?site=research_read");
    exit();
}

$safeFolderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $obj['number']);
$filesDir = __DIR__ . '/ResearchObjects/' . $safeFolderName . '/files/';

if (!is_dir($filesDir)) 
{
    mkdir($filesDir, 0755, true);
}

// Whitelist: endast kontorsdokument, aldrig körbara filer
$allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

if (isset($_FILES['fattachment']) && $_FILES['fattachment']['error'] === UPLOAD_ERR_OK) 
{
    $fileExtension = strtolower(pathinfo($_FILES['fattachment']['name'], PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) 
    {
        $fileName = str_replace(' ', '_', $_FILES['fattachment']['name']);
        $destPath = $filesDir . $fileName;

        if (move_uploaded_file($_FILES['fattachment']['tmp_name'], $destPath)) 
        {
            logactivity($author, date("Y-m-d"), date("H:i:s"), "File uploaded", $obj['number'], "File '$fileName' uploaded to research object", "Research database");
        }
    } 
    else 
    {
        echo "<script>alert('Otillåten filtyp. Endast pdf, doc, docx, xls, xlsx, ppt, pptx, txt tillåts.'); window.history.back();</script>";
        exit();
    }
}

header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
exit();
?>