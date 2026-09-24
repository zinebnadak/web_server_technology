// övning 13, forskningsdatabas
// bara JPG tillåts, bilden resizas till max 1920x1080, och en thumbnail skapas automatiskt.

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
$imagesDir = __DIR__ . '/ResearchObjects/' . $safeFolderName . '/images/';

if (!is_dir($imagesDir)) 
{
    mkdir($imagesDir, 0755, true);
}

// Endast JPG tillåts (enligt föreläsningen, för att hålla det enkelt med GD)
$allowedExtensions = ['jpg', 'jpeg'];

if (isset($_FILES['fimage']) && $_FILES['fimage']['error'] === UPLOAD_ERR_OK) 
{
    $fileExtension = strtolower(pathinfo($_FILES['fimage']['name'], PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) 
    {
        $fileName = 'img_' . time() . '.jpg';
        $destPath = $imagesDir . $fileName;

        if (move_uploaded_file($_FILES['fimage']['tmp_name'], $destPath)) 
        {
            // Krymp ner bilden till max 1920x1080 om den är större
            resizeimage($imagesDir, $fileName, 1920, 1080);

            // Skapa en thumbnail (200x200)
            createthumbnail($imagesDir, $fileName, 200, 200);

            logactivity($author, date("Y-m-d"), date("H:i:s"), "Image uploaded", $obj['number'], "New image uploaded to research object", "Research database");
        }
    } 
    else 
    {
        echo "<script>alert('Endast JPG-bilder tillåts.'); window.history.back();</script>";
        exit();
    }
}

header("Location: index.php?site=research_read_object&id=" . urlencode($objectId));
exit();
?>