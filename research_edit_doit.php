// övning 13, forskningsdatabas

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$allowedpage = "research_edit.php";
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

$id = $_POST['id'] ?? '';
$author = $_SESSION['employeecode'] ?? 'SYSTEM';

if (empty($id) || !is_numeric($id)) 
{
    header("Location: index.php?site=research_read");
    exit();
}

// Hämta gammalt number (för att kunna döpa om mappen vid behov)
$oldStmt = $conn->prepare("SELECT number, securityDataSheet FROM ResearchObjects WHERE id = ?");
$oldStmt->bind_param("i", $id);
$oldStmt->execute();
$oldResult = $oldStmt->get_result();
$oldObj = $oldResult->fetch_assoc();
$oldStmt->close();

if (!$oldObj) 
{
    header("Location: index.php?site=research_read");
    exit();
}

$number  = trim($_POST['fnumber'] ?? '');
$name    = trim($_POST['fname'] ?? '');
$text    = trim($_POST['ftext'] ?? '');
$status  = trim($_POST['fstatus'] ?? 'Open');
$presentationVideo = trim($_POST['fpresentationvideo'] ?? '');
$handlingVideo     = trim($_POST['fhandlingvideo'] ?? '');

if (empty($number) || empty($name)) 
{
    echo "<script>alert('Number och Name krävs!'); window.history.back();</script>";
    exit();
}

$oldSafeFolderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $oldObj['number']);
$newSafeFolderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $number);
$oldDir = __DIR__ . '/ResearchObjects/' . $oldSafeFolderName;
$newDir = __DIR__ . '/ResearchObjects/' . $newSafeFolderName;

// Om number ändrats, döp om mappen så filer/bilder inte tappas bort
if ($oldSafeFolderName !== $newSafeFolderName && is_dir($oldDir)) 
{
    rename($oldDir, $newDir);
}

// Hantera ny uppladdning av säkerhetsdatablad (behåll gammalt om inget nytt laddas upp)
$dataSheetFilename = $oldObj['securityDataSheet'];
if (isset($_FILES['fdatasheet']) && $_FILES['fdatasheet']['error'] === UPLOAD_ERR_OK) 
{
    $fileExtension = strtolower(pathinfo($_FILES['fdatasheet']['name'], PATHINFO_EXTENSION));
    if ($fileExtension === 'pdf') 
    {
        $dataSheetFilename = str_replace(' ', '_', $_FILES['fdatasheet']['name']);
        if (!is_dir($newDir . '/files')) {
            mkdir($newDir . '/files', 0755, true);
        }
        move_uploaded_file($_FILES['fdatasheet']['tmp_name'], $newDir . '/files/' . $dataSheetFilename);
    }
}

$sql = "UPDATE ResearchObjects SET number = ?, name = ?, text = ?, status = ?, securityDataSheet = ?, securityPresentationVideo = ?, securityHandlingVideo = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $number, $name, $text, $status, $dataSheetFilename, $presentationVideo, $handlingVideo, $id);

if ($stmt->execute()) 
{
    logactivity($author, date("Y-m-d"), date("H:i:s"), "Research edited", $number, "Research object '$name' ($number) was updated", "Research database");
}
$stmt->close();

header("Location: index.php?site=research_read_object&id=" . urlencode($id));
exit();
?>