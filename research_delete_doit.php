// övning 13, forskningsdatabas
// när ett research object raderas måste hela dess mapp (files + images) också raderas, och ResearchEntries raderas automatiskt tack vare ON DELETE CASCADE

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

$id = $_GET['id'] ?? '';
$author = $_SESSION['employeecode'] ?? 'SYSTEM';

if (empty($id) || !is_numeric($id)) 
{
    header("Location: index.php?site=research_read");
    exit();
}

// Hämta objektets info innan radering (för mappnamn + loggning)
$stmt = $conn->prepare("SELECT number, name FROM ResearchObjects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$obj = $result->fetch_assoc();
$stmt->close();

if (!$obj) 
{
    header("Location: index.php?site=research_read");
    exit();
}

// Radera hela mappen (files + images) rekursivt
function deleteFolderRecursive($dir) 
{
    if (!is_dir($dir)) return;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) 
    {
        $path = $dir . '/' . $item;
        is_dir($path) ? deleteFolderRecursive($path) : unlink($path);
    }
    rmdir($dir);
}

$safeFolderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $obj['number']);
$objectDir = __DIR__ . '/ResearchObjects/' . $safeFolderName;
deleteFolderRecursive($objectDir);

// Radera från databasen (ResearchEntries raderas automatiskt via ON DELETE CASCADE)
$deleteStmt = $conn->prepare("DELETE FROM ResearchObjects WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) 
{
    logactivity($author, date("Y-m-d"), date("H:i:s"), "Research deleted", $obj['number'], "Research object '{$obj['name']}' ({$obj['number']}) was deleted", "Research database");
}
$deleteStmt->close();

header("Location: index.php?site=research_read");
exit();
?>