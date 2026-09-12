// övning 13 forskningsdatabas 
// listsidan som visar alla research objects med kolumnerna Number, Name, Created, By, Entries, Last entry från DB
// besök http://thenest.umbrellacorp.top/index.php?site=research_read


<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "research_read.php";
$_SESSION['pagename'] = $pagename;

// Security Access Level B eller högre
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');
if (!in_array(strtoupper($currentUserAccessLevel), ['A', 'B'])) 
{
    die("<p style='color:red;'>Åtkomst nekad: Det krävs säkerhetsnivå B eller högre för att se forskningsdatabasen.</p>");
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
    die("<p style='color:red;'>Kunde inte ansluta till databasen (\$conn saknas).</p>");
}

// Hjälpfunktion: räkna entries + senaste entry-datum för ett research object
function getEntries($conn, $objectId)
{
    $sql = "SELECT COUNT(*) AS antal, MAX(entryDate) AS senaste 
            FROM ResearchEntries 
            WHERE research_object_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $objectId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row;
}

// Hämta research objects. Level A ser även arkiverade (gråa), level B ser bara Open.
if (strtoupper($currentUserAccessLevel) === 'A') 
{
    $sql = "SELECT id, number, name, createdDate, createdBy, status FROM ResearchObjects ORDER BY number ASC";
} 
else 
{
    $sql = "SELECT id, number, name, createdDate, createdBy, status FROM ResearchObjects WHERE status = 'Open' ORDER BY number ASC";
}
$result = mysqli_query($conn, $sql);

if (!$result) 
{
    echo "<p style='color:red;'>SQL-fel: " . mysqli_error($conn) . "</p>";
}
?>

<div style="text-align: right; margin-bottom: 15px;">
    <a href="index.php?site=research_add" style="color: #fff; text-decoration: none; font-weight: bold; font-size: 14px;">
        Add new research 🧪➕
    </a>
</div>

<table style="width: 100%; border-collapse: collapse; color: #fff; font-family: Arial, sans-serif; border: 1px solid #444;">
    <thead>
        <tr style="border-bottom: 2px solid #fff; background-color: #222;">
            <th style="text-align: left; padding: 10px;">Number</th>
            <th style="text-align: left; padding: 10px;">Name</th>
            <th style="text-align: left; padding: 10px;">Created</th>
            <th style="text-align: left; padding: 10px;">By</th>
            <th style="text-align: center; padding: 10px;">Entries</th>
            <th style="text-align: left; padding: 10px;">Last entry</th>
            <th style="text-align: right; padding: 10px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if ($result && mysqli_num_rows($result) > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                $entryInfo = getEntries($conn, $row['id']);
                $isArchived = (strtoupper($row['status']) === 'ARCHIVED');
                $rowStyle = $isArchived ? "color: #888;" : "color: #fff;";
                ?>
                <tr style="border-bottom: 1px solid #333; <?php echo $rowStyle; ?>">
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['number'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;">
                        <a href="index.php?site=research_read_object&id=<?php echo urlencode($row['id']); ?>" style="<?php echo $rowStyle; ?> text-decoration: underline;">
                            <?php echo htmlspecialchars($row['name'] ?? ''); ?>
                            <?php if ($isArchived) echo " (Archived)"; ?>
                        </a>
                    </td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['createdDate'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['createdBy'] ?? ''); ?></td>
                    <td style="padding: 8px 10px; text-align: center;"><?php echo (int)($entryInfo['antal'] ?? 0); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($entryInfo['senaste'] ?? '-'); ?></td>
                    <td style="text-align: right; padding: 8px 10px;">
                        <a href="index.php?site=research_edit&id=<?php echo urlencode($row['id']); ?>" title="Edit" style="text-decoration: none; margin-right: 5px;">🟢</a>
                        <a href="research_delete_doit.php?id=<?php echo urlencode($row['id']); ?>" title="Delete" style="text-decoration: none;" onclick="return confirm('Are you sure you want to delete the research (<?php echo htmlspecialchars($row['id']); ?>)?');">❌</a>
                    </td>
                </tr>
                <?php
            }
        } 
        else 
        {
            echo "<tr><td colspan='7' style='padding: 15px; text-align: center;'>Inga forskningsobjekt hittades.</td></tr>";
        }
        ?>
    </tbody>
</table>