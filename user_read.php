<?php
// Slå på felmeddelanden för att se exakt vad som går fel
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "user_read.php";
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
            u.employeeCode, 
            u.email, 
            u.lockout, 
            e.name 
        FROM users u 
        LEFT JOIN employee e ON u.employeeCode = e.employeeCode 
        ORDER BY u.employeeCode ASC";

$result = mysqli_query($conn, $sql);

if (!$result) 
{
    echo "<p style='color:red;'>SQL-fel: " . mysqli_error($conn) . "</p>";
}
?>

<!-- Lägg till-knapp -->
<div style="text-align: right; margin-bottom: 15px;">
    <a href="index.php?site=user_new" style="color: #fff; text-decoration: none; font-weight: bold; font-size: 14px;">
        Add new users 👤➕
    </a>
</div>

<!-- Tabell -->
<table style="width: 100%; border-collapse: collapse; color: #fff; font-family: Arial, sans-serif; border: 1px solid #444;">
    <thead>
        <tr style="border-bottom: 2px solid #fff; background-color: #222;">
            <th style="text-align: left; padding: 10px;">Username</th>
            <th style="text-align: left; padding: 10px;">Name</th>
            <th style="text-align: left; padding: 10px;">Email address</th>
            <th style="text-align: center; padding: 10px;">Locked out</th>
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
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                    <td style="padding: 8px 10px;"><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                    <td style="padding: 8px 10px; text-align: center;"><?php echo (strtoupper($row['lockout'] ?? '') === 'X') ? 'X' : ''; ?></td>
                    <td style="text-align: right; padding: 8px 10px;">
                        <a href="index.php?site=user_edit&code=<?php echo urlencode($row['employeeCode']); ?>" title="Edit" style="text-decoration: none; margin-right: 5px;">🟢</a>
                        <a href="user_delete_doit.php?code=<?php echo urlencode($row['employeeCode']); ?>" title="Delete" style="text-decoration: none;" onclick="return confirm('Ta bort användaren?');">❌</a>
                    </td>
                </tr>
                <?php
            }
        } 
        else 
        {
            echo "<tr><td colspan='5' style='padding: 15px; text-align: center;'>Inga användare hittades i databasen.</td></tr>";
        }
        ?>
    </tbody>
</table>