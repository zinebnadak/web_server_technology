
<?php
// Slå på felmeddelanden för att se exakt vad som går fel
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Hämta den inloggades nivå från sessionen
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');


if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "personnelregistry_read.php";
$_SESSION['pagename'] = $pagename;

// Säkerställ databansanslutning
if (!isset($conn))
{
    if (file_exists("_config.php")) {
        include_once("_config.php");
    }
}

// Om anslutningen fortfarande saknas
if (!isset($conn)) 
{
    die("<p style='color:red;'>Kunde inte ansluta till databasen (\$conn saknas).</p>");
}

// SQL-fråga
$sql = "SELECT 
            employeeCode, 
            name, 
            signatureDate, 
            rank,
            securityAccessLevel
        FROM employee
        ORDER BY employeeCode ASC";

$result = mysqli_query($conn, $sql);

if (!$result) 
{
    echo "<p style='color:red;'>SQL-fel: " . mysqli_error($conn) . "</p>";
}
?>

<!-- Lägg till-knapp -->
<div style="text-align: right; margin-bottom: 15px;">
    <a href="index.php?site=personnelregistry_new" style="color: #fff; text-decoration: none; font-weight: bold; font-size: 14px;">
        Add new employee 👤➕
    </a>
</div>

<!-- Tabell -->
<table style="width: 100%; border-collapse: collapse; color: #fff; font-family: Arial, sans-serif; border: 1px solid #444;">
    <thead>
        <tr style="border-bottom: 2px solid #fff; background-color: #222;">
            <th style="text-align: left; padding: 10px;">Employee Code</th>
            <th style="text-align: left; padding: 10px;">Name</th>
            <th style="text-align: left; padding: 10px;">Signature Date</th>
            <th style="text-align: center; padding: 10px;">Rank</th>
            <th style="text-align: left; padding: 10px;">Access Level</th>
            <th style="text-align: right; padding: 10px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if ($result && mysqli_num_rows($result) > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                ?>
                <tr style="border-bottom: 1px solid #333;">
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['employeeCode'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;">
                        <a href="index.php?site=personnelregistry_read_employee&code=<?php echo urlencode($row['employeeCode']); ?>" style="color: #fff; text-decoration: underline;">
                            <?php echo htmlspecialchars($row['name'] ?? ''); ?>
                        </a>
                    </td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['signatureDate'] ?? ''); ?></td>
                    <td style="padding: 8px 10px; text-align: center;"><?php echo htmlspecialchars($row['rank'] ?? ''); ?></td>
                    <td style="padding: 8px 10px; text-align: left;"><?php echo htmlspecialchars($row['securityAccessLevel'] ?? ''); ?></td>
                    <td style="text-align: right; padding: 8px 10px;">
                    
                        <!-- radera/editknappen securrity access level A -->
                        <?php if (strtoupper($currentUserAccessLevel) === 'A'): ?>
                            <a href="index.php?site=personnelregistry_edit&code=<?php echo urlencode($row['employeeCode']); ?>" title="Edit" style="text-decoration: none; margin-right: 5px;">🟢</a>
                            <a href="index.php?site=personnelregistry_delete_doit&code=<?php echo urlencode($row['employeeCode']); ?>" title="Delete" style="text-decoration: none;" onclick="return confirm('Ta bort den anställde?');">❌</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
            }
        } 
        else 
        {
            echo "<tr><td colspan='6' style='padding: 15px; text-align: center;'>Inga användare hittades i databasen.</td></tr>";
        }
        ?>
    </tbody>
</table>