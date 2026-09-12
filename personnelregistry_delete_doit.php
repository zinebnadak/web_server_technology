<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// ----------------------------------------------------------------------
// HÄR SÄTTER DU DEN NYA BEHÖRIGHETSKONTROLLEN:
// ----------------------------------------------------------------------

// Hämta säkerhetsnivån på samma sätt som i read-filen
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');

// Kontrollera om användaren är inloggad (kollar både 'loggedin' och om accesslevel finns)
$isLoggedIn = ($_SESSION['loggedin'] ?? false) === true || !empty($currentUserAccessLevel);

if (!$isLoggedIn) 
{
    die("Du har inte behörighet att utföra denna åtgärd (inte inloggad).");
}

// Kontrollera Security Access Level A
if (strtoupper($currentUserAccessLevel) !== 'A') 
{
    die("Åtkomst nekad: Det krävs säkerhetsnivå A för att radera personal.");
}

// ----------------------------------------------------------------------


// Anslut till databasen
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

// Hämta employeeCode från url:en (t.ex. personnelregistry_delete.php?code=ITXX00)
$employeeCode = $_GET['code'] ?? $_GET['employeeCode'] ?? '';

if (empty($employeeCode)) 
{
    die("Fel: Inget Employee Code angavs.");
}

// Ta reda på bildens filnamn innan vi raderar ur databasen
$stmt = $conn->prepare("SELECT photo FROM employee WHERE employeeCode = ?");
$stmt->bind_param("s", $employeeCode);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) 
{
    $photo = $row['photo'];

    // Radera bildfilen från disken om det inte är standardbilden (default.jpg)
    if (!empty($photo) && $photo !== 'default.jpg') 
    {
        $photoPath = __DIR__ . '/photos/' . $photo;
        if (file_exists($photoPath)) 
        {
            unlink($photoPath); // Ta bort filen från servern
        }
    }

    $stmt->close();

    // Radera användaren ur databasen
    $deleteStmt = $conn->prepare("DELETE FROM employee WHERE employeeCode = ?");
    $deleteStmt->bind_param("s", $employeeCode);

    if ($deleteStmt->execute()) 
    {
        $deleteStmt->close();
        // Skicka tillbaka till listan med ett meddelande
        echo "<script>window.location.href='index.php?site=personnelregistry_read&msg=deleted';</script>";
        exit();
    } 
    else 
    {
        echo "<p style='color:red;'>Fel vid radering: " . htmlspecialchars($deleteStmt->error) . "</p>";
    }
} 
else 
{
    echo "<p style='color:red;'>Anställd hittades inte.</p>";
}
?>