// övning 13, forskningsadataabs

// formuläret för att redigera basinfo, förifyllt med befintliga värden

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "research_edit.php";
$_SESSION['pagename'] = $pagename;

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
if (!isset($conn)) 
{
    die("<p style='color:red;'>Kunde inte ansluta till databasen.</p>");
}

$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) 
{
    header("Location: index.php?site=research_read");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM ResearchObjects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$object = $result->fetch_assoc();
$stmt->close();

if (!$object) 
{
    header("Location: index.php?site=research_read");
    exit();
}
?>

<div style="color: #fff; font-family: Arial, sans-serif;">
    <h3>Edit Research Object</h3>

    <form action="research_edit_doit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($object['id']); ?>" />

        Number: <input type="text" name="fnumber" size="10" value="<?php echo htmlspecialchars($object['number']); ?>" /><p/>
        Name: <input type="text" name="fname" size="30" value="<?php echo htmlspecialchars($object['name']); ?>" /><p/>
        Text: <br/>
        <textarea name="ftext" rows="5" cols="50"><?php echo htmlspecialchars($object['text']); ?></textarea><p/>

        Status: 
        <select name="fstatus">
            <option value="Open" <?php echo (strtoupper($object['status']) === 'OPEN') ? 'selected' : ''; ?>>Open</option>
            <option value="Archived" <?php echo (strtoupper($object['status']) === 'ARCHIVED') ? 'selected' : ''; ?>>Archived</option>
        </select><p/>

        Security Data Sheet (PDF, lämna tomt för att behålla nuvarande): <input type="file" name="fdatasheet" /><p/>
        Security Presentation Video (URL): <input type="text" name="fpresentationvideo" size="40" value="<?php echo htmlspecialchars($object['securityPresentationVideo']); ?>" /><p/>
        Security Handling Video (URL): <input type="text" name="fhandlingvideo" size="40" value="<?php echo htmlspecialchars($object['securityHandlingVideo']); ?>" /><p/>

        <input class="button" type="submit" value="Save" />
        <input class="button" type="button" value="Cancel" onClick="window.location.href='index.php?site=research_read_object&id=<?php echo urlencode($id); ?>';" />

    </form>
</div>