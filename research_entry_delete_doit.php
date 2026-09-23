// övning 12 forskningsdatabas 
// raderar en entry 

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

$entryId  = $_GET['id'] ?? '';
$objectId = $_GET['object_id'] ?? '';

if (empty($entryId) || !is_numeric($entryId) || empty($objectId) || !is_numeric($objectId)) 
{
    header("Location: index.php?site=research_read");
    exit();
}

$author = $_SESSION['employeecode'] ?? 'SYSTEM';

$stmt = $conn->prepare("DELETE FROM ResearchEntries WHERE id = ?");
$stmt->bind_param("i", $entryId);

if ($stmt->execute()) 
{
    logactivity($author, date("Y-m-d"), date("H:i:s"), "Research entry deleted", $objectId, "Research entry $entryId was deleted", "Research database");
}

$stmt->close();
header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
exit();
?>