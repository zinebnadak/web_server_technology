<?php
// uppgift 12, forskningsdatabas 
// visar basinfo för ett virus, dess research entries, uppladdade filer och bilder
// byggs i mindre delar

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "research_read_object.php";
$_SESSION['pagename'] = $pagename;

// Security Access Level B eller högre
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');
if (!in_array(strtoupper($currentUserAccessLevel), ['A', 'B'])) 
{
    die("<p style='color:red;'>Åtkomst nekad: Det krävs säkerhetsnivå B eller högre.</p>");
}

// Säkerställ databasanslutning
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

// Om inget id skickas, bounce tillbaka till listan
$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) 
{
    header("Location: index.php?site=research_read");
    exit();
}

// Hämta research object
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

$isArchived = (strtoupper($object['status']) === 'ARCHIVED');

// Hämta alla research entries för detta objekt
$entryStmt = $conn->prepare("SELECT * FROM ResearchEntries WHERE research_object_id = ? ORDER BY entryDate DESC, entryTime DESC");
$entryStmt->bind_param("i", $id);
$entryStmt->execute();
$entries = $entryStmt->get_result();
$entryStmt->close();

// Säkert mappnamn, används av både Attachments och Images
$safeFolderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $object['number']);
?>

<div style="color: #fff; font-family: Arial, sans-serif;">

    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h2><?php echo htmlspecialchars($object['number']); ?> — <?php echo htmlspecialchars($object['name']); ?>
                <?php if ($isArchived) echo " <span style='color:#888;'>(Archived)</span>"; ?>
            </h2>
            <p style="color: #aaa;">Created <?php echo htmlspecialchars($object['createdDate']); ?> by <?php echo htmlspecialchars($object['createdBy']); ?></p>
        </div>
        <div>
            <a href="index.php?site=research_edit&id=<?php echo urlencode($id); ?>" style="color: #fff;">Edit Info</a>
        </div>
    </div>

    <p><?php echo nl2br(htmlspecialchars($object['text'])); ?></p>

    <p><strong>Status:</strong> <?php echo htmlspecialchars($object['status']); ?></p>

    <hr style="border-color: #444;">

    <h3>Research Entries</h3>

    <form action="research_entry_add_doit.php" method="post">
        <input type="hidden" name="research_object_id" value="<?php echo htmlspecialchars($id); ?>" />
        <textarea name="entryText" rows="3" cols="60" placeholder="Write new research entry..."></textarea><br/>
        <input class="button" type="submit" value="Write new research entry" />
    </form>

    <div style="margin-top: 15px;">
        <?php 
        if ($entries && mysqli_num_rows($entries) > 0) 
        {
            while ($entryRow = mysqli_fetch_assoc($entries)) 
            {
                ?>
                <div style="border-bottom: 1px solid #333; padding: 8px 0;">
                    <span style="color: #aaa; font-size: 12px;">
                        <?php echo htmlspecialchars($entryRow['author']); ?> | 
                        <?php echo htmlspecialchars($entryRow['entryDate']); ?> | 
                        kl. <?php echo htmlspecialchars($entryRow['entryTime']); ?>
                    </span>
                    <p><?php echo nl2br(htmlspecialchars($entryRow['entryText'])); ?></p>
                    <a href="research_entry_delete_doit.php?id=<?php echo urlencode($entryRow['id']); ?>&object_id=<?php echo urlencode($id); ?>" 
                       onclick="return confirm('Delete this entry?');" style="color: #f66; font-size: 12px;">Delete</a>
                </div>
                <?php
            }
        } 
        else 
        {
            echo "<p style='color:#888;'>Inga research entries ännu.</p>";
        }
        ?>
    </div>

    <hr style="border-color: #444;">

    <h3>Attachments</h3>

    <form action="research_attachment_upload_doit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="research_object_id" value="<?php echo htmlspecialchars($id); ?>" />
        <input type="file" name="fattachment" />
        <input class="button" type="submit" value="Upload new file" />
    </form>

    <div style="margin-top: 10px;">
        <?php
        $filesDir = __DIR__ . '/ResearchObjects/' . $safeFolderName . '/files/';
        
        if (is_dir($filesDir)) 
        {
            $files = array_diff(scandir($filesDir), ['.', '..']);
            if (count($files) > 0) 
            {
                foreach ($files as $file) 
                {
                    $filePath = $filesDir . $file;
                    $fileSizeKb = round(filesize($filePath) / 1024);
                    $fileDate = date("d.m.Y", filemtime($filePath));
                    ?>
                    <div style="padding: 4px 0;">
                        <a href="ResearchObjects/<?php echo urlencode($safeFolderName); ?>/files/<?php echo urlencode($file); ?>" style="color: #45aeeb;" target="_blank"><?php echo htmlspecialchars($file); ?></a>
                        <span style="color: #888; font-size: 12px;"> — <?php echo $fileSizeKb; ?> KB — <?php echo $fileDate; ?></span>
                        <a href="research_attachment_delete_doit.php?object_id=<?php echo urlencode($id); ?>&file=<?php echo urlencode($file); ?>" 
                           onclick="return confirm('Delete this file?');" style="color: #f66; font-size: 12px; margin-left: 10px;">Delete</a>
                    </div>
                    <?php
                }
            } 
            else 
            {
                echo "<p style='color:#888;'>Inga filer uppladdade ännu.</p>";
            }
        }
        ?>
    </div>

    <hr style="border-color: #444;">

    <h3>Research Images</h3>

    <form action="research_image_upload_doit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="research_object_id" value="<?php echo htmlspecialchars($id); ?>" />
        <input type="file" name="fimage" accept=".jpg,.jpeg" />
        <input class="button" type="submit" value="Upload new image" />
    </form>

    <div style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
        <?php
        $imagesDir = __DIR__ . '/ResearchObjects/' . $safeFolderName . '/images/';

        if (is_dir($imagesDir)) 
        {
            $images = array_diff(scandir($imagesDir), ['.', '..']);
            $thumbnails = array_filter($images, function($f) { return strpos($f, 'thumb_') === 0; });

            if (count($thumbnails) > 0) 
            {
                foreach ($thumbnails as $thumb) 
                {
                    $originalName = substr($thumb, 6);
                    ?>
                    <div style="text-align: center;">
                        <a href="ResearchObjects/<?php echo urlencode($safeFolderName); ?>/images/<?php echo urlencode($originalName); ?>" target="_blank">
                            <img src="ResearchObjects/<?php echo urlencode($safeFolderName); ?>/images/<?php echo urlencode($thumb); ?>" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #444;" />
                        </a><br/>
                        <a href="research_image_delete_doit.php?object_id=<?php echo urlencode($id); ?>&file=<?php echo urlencode($originalName); ?>" 
                           onclick="return confirm('Delete this image?');" style="color: #f66; font-size: 11px;">Delete</a>
                    </div>
                    <?php
                }
            } 
            else 
            {
                echo "<p style='color:#888;'>Inga bilder uppladdade ännu.</p>";
            }
        }
        ?>
    </div>

</div>