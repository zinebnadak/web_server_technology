// uppgift 12 forskningsdatabas
// sparar en ny research entry

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

$objectId  = $_POST['research_object_id'] ?? '';
$entryText = trim($_POST['entryText'] ?? '');
$author    = $_SESSION['employeecode'] ?? 'SYSTEM';

if (empty($objectId) || !is_numeric($objectId) || empty($entryText)) 
{
    header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
    exit();
}

// Hämta objektets number/name för loggning
$infoStmt = $conn->prepare("SELECT number, name FROM ResearchObjects WHERE id = ?");
$infoStmt->bind_param("i", $objectId);
$infoStmt->execute();
$infoResult = $infoStmt->get_result();
$objInfo = $infoResult->fetch_assoc();
$infoStmt->close();

$stmt = $conn->prepare("INSERT INTO ResearchEntries (research_object_id, author, entryDate, entryTime, entryText) VALUES (?, ?, CURDATE(), CURTIME(), ?)");
$stmt->bind_param("iss", $objectId, $author, $entryText);

if ($stmt->execute()) 
{
    $shortText = mb_substr($entryText, 0, 100);
    logactivity($author, date("Y-m-d"), date("H:i:s"), "New research entry added", $objInfo['number'] ?? $objectId, $shortText, "Research database");
}

$stmt->close();
header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
exit();
?>