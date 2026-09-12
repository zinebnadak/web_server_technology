// övning 12 aktvitetslogg
// besök 

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "activitylog_read.php";
$_SESSION['pagename'] = $pagename;

// Endast Security Access Level A
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');
if (strtoupper($currentUserAccessLevel) !== 'A') 
{
    die("<p style='color:red;'>Åtkomst nekad: Det krävs säkerhetsnivå A för att se aktivitetsloggen.</p>");
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

// Automatisk cleanup: radera poster äldre än 1 år
mysqli_query($conn, "DELETE FROM activitylog WHERE `date` < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)");

// ---- Sortering ----
$allowedSort = ['user' => 'user', 'category' => 'category', 'date' => '`date` DESC, `time` DESC'];
$sortKey = $_GET['sort'] ?? 'date';
if (!array_key_exists($sortKey, $allowedSort)) 
{
    $sortKey = 'date';
}
$orderBy = $allowedSort[$sortKey];

// ---- Sidstorlek (paginering) ----
$allowedLimits = [20, 50, 100, 150];
$limit = (int)($_GET['limit'] ?? 50);
if (!in_array($limit, $allowedLimits)) 
{
    $limit = 50;
}

// ---- Offset (vilken "sida" vi är på) ----
$offset = (int)($_GET['offset'] ?? 0);
if ($offset < 0) 
{
    $offset = 0;
}

// Totalt antal rader (för att veta om "next" ska visas)
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM activitylog");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRows = (int)$totalRow['total'];

// Hämta loggposter
$sql = "SELECT id, user, `date`, `time`, activity, object, info, category 
        FROM activitylog 
        ORDER BY $orderBy 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

if (!$result) 
{
    echo "<p style='color:red;'>SQL-fel: " . mysqli_error($conn) . "</p>";
}
?>

<div style="text-align: right; margin-bottom: 15px; color: #fff; font-family: Arial, sans-serif; font-size: 13px;">

    <!-- Sortering -->
    Order by:
    <a href="index.php?site=activitylog_read&sort=user&limit=<?php echo $limit; ?>" style="color: #fff; margin-right: 8px;">User</a>
    <a href="index.php?site=activitylog_read&sort=category&limit=<?php echo $limit; ?>" style="color: #fff; margin-right: 8px;">Category</a>
    <a href="index.php?site=activitylog_read&sort=date&limit=<?php echo $limit; ?>" style="color: #fff; margin-right: 20px;">Date</a>

    <!-- Show max -->
    Show max:
    <?php foreach ($allowedLimits as $l): ?>
        <a href="index.php?site=activitylog_read&sort=<?php echo $sortKey; ?>&limit=<?php echo $l; ?>" 
           style="color: <?php echo ($l === $limit) ? '#45aeeb' : '#fff'; ?>; margin-right: 6px; font-weight: <?php echo ($l === $limit) ? 'bold' : 'normal'; ?>;">
            <?php echo $l; ?>
        </a>
    <?php endforeach; ?>
</div>

<table style="width: 100%; border-collapse: collapse; color: #fff; font-family: Arial, sans-serif; border: 1px solid #444;">
    <thead>
        <tr style="border-bottom: 2px solid #fff; background-color: #222;">
            <th style="text-align: left; padding: 10px;">Date</th>
            <th style="text-align: left; padding: 10px;">Time</th>
            <th style="text-align: left; padding: 10px;">Activity</th>
            <th style="text-align: left; padding: 10px;">User</th>
            <th style="text-align: left; padding: 10px;">Category</th>
            <th style="text-align: right; padding: 10px;">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if ($result && mysqli_num_rows($result) > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                ?>
                <tr style="border-bottom: 1px solid #333;" title="<?php echo htmlspecialchars($row['info'] ?? ''); ?>">
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['date'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['time'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['activity'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['user'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
                    <td style="text-align: right; padding: 8px 10px;">
                        <a href="activitylog_delete_doit.php?id=<?php echo urlencode($row['id']); ?>" 
                           title="Delete" 
                           style="text-decoration: none;" 
                           onclick="return confirm('Are you sure you want to delete the activity log post (<?php echo htmlspecialchars($row['id']); ?>)?');">❌</a>
                    </td>
                </tr>
                <?php
            }
        } 
        else 
        {
            echo "<tr><td colspan='6' style='padding: 15px; text-align: center;'>Inga loggposter hittades.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Show previous / next -->
<div style="margin-top: 15px; color: #fff; font-family: Arial, sans-serif; font-size: 13px;">
    <?php if ($offset > 0): ?>
        <a href="index.php?site=activitylog_read&sort=<?php echo $sortKey; ?>&limit=<?php echo $limit; ?>&offset=<?php echo max(0, $offset - $limit); ?>" style="color: #fff;">&laquo; Show previous <?php echo $limit; ?></a>
    <?php endif; ?>

    <?php if ($offset > 0 && ($offset + $limit) < $totalRows): ?>
        &nbsp;|&nbsp;
    <?php endif; ?>

    <?php if (($offset + $limit) < $totalRows): ?>
        <a href="index.php?site=activitylog_read&sort=<?php echo $sortKey; ?>&limit=<?php echo $limit; ?>&offset=<?php echo $offset + $limit; ?>" style="color: #fff;">Show next <?php echo $limit; ?> &raquo;</a>
    <?php endif; ?>
</div>
