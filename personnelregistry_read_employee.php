<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// Säkerställ databasanslutning
if (!isset($conn)) 
{
    if (file_exists("_config.php")) 
    {
        include_once("_config.php");
    }
}

if (!isset($conn)) 
{
    die("<p style='color:red;'>Kunde inte ansluta till databasen.</p>");
}

// Hämta employeeCode/id från URL:en
$employeeCode = $_GET['code'] ?? $_GET['employeeCode'] ?? '';

if (empty($employeeCode)) 
{
    echo "<p style='color:red;'>Inget Employee Code angavs.</p>";
    exit();
}

// Hämta specifika detaljer för den anställde
$stmt = $conn->prepare("SELECT employeeCode, name, signatureDate, dateOfBirth, sex, bloodType, height, weight, rank, department, securityAccessLevel, background, strengths, weaknesses, photo FROM employee WHERE employeeCode = ?");
$stmt->bind_param("s", $employeeCode);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) 
{
    // --- BILDHANTERING ---
    $employeeCode = $row['employeeCode'];
    $photoDb = trim($row['photo'] ?? '');
    
    // Sökväg till mappen i samma katalog
    $photosDir = __DIR__ . '/photos/';
    
    // Standardbild om inget hittas
    $imageSrc = "photos/default.jpg"; 

    // 1. Kolla om databasens filnamn finns i mappen 
    if (!empty($photoDb) && file_exists($photosDir . $photoDb)) 
    {
        $imageSrc = "photos/" . $photoDb;
    }
    // 2. Om databasfältet var tomt eller filen inte fanns där, sök efter koden (.jpg, .png, etc.)
    else 
    {
        $possibleFiles = [
            $employeeCode . '.jpg',
            $employeeCode . '.JPG',
            $employeeCode . '.png',
            $employeeCode . '.jpeg'
        ];

        foreach ($possibleFiles as $file) 
        {
            if (file_exists($photosDir . $file)) 
            {
                $imageSrc = "photos/" . $file;
                break;
            }
        }
    }

?>
    // Inkludera CSS för den enskilda anställde 
    <link rel="stylesheet" href="css/personnel_registry_employee.css" />

    <!-- Container med svart bakgrund och vit text -->
    <div style="background-color: #000000; color: #ffffff; padding: 20px; box-sizing: border-box; min-height: 100vh;">

        <h1 style="color: #ffffff; margin-top: 0;">Personnel Registry - <?php echo htmlspecialchars($row['employeeCode']); ?></h1>

        <table id="infomiddle" style="width: 100%; color: #fff; background-color: #000000;">
            <tr>
                <!-- Vänster spalt: Foto och koder -->
                <td width="166" valign="top">
                    <table id="photocol">
                        <tr>
                            <td id="photobox" style="border: 1px solid #333; background: #111;">
                                <img src="<?php echo $imageSrc; ?>" alt="Foto" width="164" style="display: block;" />
                            </td>
                        </tr>
                        <tr><td class="tablespacer"></td></tr>
                        <tr>
                            <td id="employeecode" style="color: #fff;">
                                EMPLOYEE CODE:<br />
                                <b><?php echo htmlspecialchars($row['employeeCode']); ?></b>
                            </td>
                        </tr>
                        <tr><td class="tablespacer"></td></tr>
                        <tr>
                            <td id="securitylevel" style="color: #fff;">
                                SECURITY CLEARANCE LEVEL:<br />
                                <span style="font-size: 1.8em; font-weight: bold; color: #ff3333;">
                                    <?php echo htmlspecialchars($row['securityAccessLevel']); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Mitten spalt: Fältnamn -->
                <td width="135" valign="top">
                    <table style="color: #aaa;">
                        <tr><td class="variablecol">NAME:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">DATE OF BIRTH:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">SEX:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">BLOOD TYPE:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">HEIGHT:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">WEIGHT:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">DEPARTMENT:</td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="variablecol">RANK:</td></tr><tr><td class="tablespacer"></td></tr>
                    </table>
                </td>

                <!-- Höger spalt: Värden från databasen -->
                <td width="245" valign="top">
                    <table style="color: #fff;">
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['name'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['dateOfBirth'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['sex'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['bloodType'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['height'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['weight'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['department'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                        <tr><td class="valuecol"><?php echo htmlspecialchars($row['rank'] ?? ''); ?></td></tr><tr><td class="blackline"></td></tr><tr><td class="tablespacer"></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr style="border-color: #333; margin-top: 20px;" />

        <h2 style="color: #fff;">Background</h2>
        <p style="color: #ccc;"><?php echo nl2br(htmlspecialchars($row['background'] ?? 'N/A')); ?></p>

        <h2 style="color: #fff;">Strengths</h2>
        <p style="color: #ccc;"><?php echo nl2br(htmlspecialchars($row['strengths'] ?? 'N/A')); ?></p>

        <h2 style="color: #fff;">Weaknesses</h2>
        <p style="color: #ccc;"><?php echo nl2br(htmlspecialchars($row['weaknesses'] ?? 'N/A')); ?></p>

        <br />
        <a href="index.php?site=personnelregistry_read" style="color: #6699ff; text-decoration: underline;">&laquo; Tillbaka till listan</a>

    </div>

    <?php
} 
else 
{
    echo "<p style='color:red;'>Anställd med kod " . htmlspecialchars($employeeCode) . " hittades inte.</p>";
}

$stmt->close();
?>