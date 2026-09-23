// övning 13, forskningsdatabas

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

$objectId = $_GET['object_id'] ?? '';
$fileName = $_GET['file'] ?? '';
$author = $_SESSION['employeecode'] ?? 'SYSTEM';

if (empty($objectId) || !is_numeric($objectId) || empty($fileName)) 
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
$safeFileName = basename($fileName);
$imagesDir = __DIR__ . '/ResearchObjects/' . $safeFolderName . '/images/';

$originalPath = $imagesDir . $safeFileName;
$thumbPath = $imagesDir . 'thumb_' . $safeFileName;

if (file_exists($originalPath)) 
{
    unlink($originalPath);
}
if (file_exists($thumbPath)) 
{
    unlink($thumbPath);
}

logactivity($author, date("Y-m-d"), date("H:i:s"), "Image deleted", $obj['number'], "Image '$safeFileName' was deleted", "Research database");

header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
exit();
?>