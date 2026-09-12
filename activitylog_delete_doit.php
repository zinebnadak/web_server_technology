// sidan man kommer till när man raderar aktivitet i aktivitetsloggen

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// Endast Security Access Level A
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');
if (strtoupper($currentUserAccessLevel) !== 'A') 
{
    die("<p style='color:red;'>Åtkomst nekad: Det krävs säkerhetsnivå A för att radera loggposter.</p>");
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

// Hämta id från URL
$id = $_GET['id'] ?? '';

if (empty($id) || !is_numeric($id)) 
{
    header("Location: index.php?site=activitylog_read");
    exit();
}

// Radera posten
$stmt = $conn->prepare("DELETE FROM activitylog WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Skicka tillbaka till aktivitetsloggen
header("Location: index.php?site=activitylog_read");
exit();
?>