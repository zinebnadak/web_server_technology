// ÖVNING 13 FORSKNINGSDATABAS
// basename($fileName) är ett viktigt säkerhetsskydd O det förhindrar att någon manipulerar URL:en för att radera filer utanför den avsedda mappen.

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
// Rensa filnamnet från farliga tecken (skydd mot path traversal, t.ex. ../../)
$safeFileName = basename($fileName);
$filePath = __DIR__ . '/ResearchObjects/' . $safeFolderName . '/files/' . $safeFileName;

if (file_exists($filePath)) 
{
    unlink($filePath);
    logactivity($author, date("Y-m-d"), date("H:i:s"), "File deleted", $obj['number'], "File '$safeFileName' was deleted", "Research database");
}

header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
exit();
?>