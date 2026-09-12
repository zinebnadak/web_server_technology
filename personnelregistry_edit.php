<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// Databasanslutning
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

// Hämta employeeCode från URL (stödjer både ?code=, ?employeeCode= och ?id=)
$employeeCode = $_GET['code'] ?? $_GET['employeeCode'] ?? $_GET['id'] ?? '';

if (empty($employeeCode)) 
{
    echo "<p style='color:red;'>Inget Employee Code angavs för redigering.</p>";
    exit();
}

// Hämta befintlig data för den anställde
$stmt = $conn->prepare("SELECT * FROM employee WHERE employeeCode = ?");
$stmt->bind_param("s", $employeeCode);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) 
{
    echo "<p style='color:red;'>Hittade ingen anställd med kod: " . htmlspecialchars($employeeCode) . "</p>";
    exit();
}

// --- BILDHANTERING ---
$photoDb = trim($row['photo'] ?? '');
$photosDir = __DIR__ . '/photos/';
$imageSrc = "photos/default.jpg"; 

if (!empty($photoDb) && file_exists($photosDir . $photoDb)) 
{
    $imageSrc = "photos/" . $photoDb;
} 
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

<h1>Edit Employee - <?php echo htmlspecialchars($row['employeeCode']); ?></h1>

<form name="editemployeeform" action="index.php?site=personnelregistry_edit_doit" onSubmit="return formCheckEditEmployee()" method="post" enctype="multipart/form-data">
    <input type="hidden" name="fsignaturedate" value="<?php echo htmlspecialchars($row['signatureDate'] ?? ''); ?>" />

    <!-- Mellan-tabell start -->
    <table id="infomiddle">
        <tr>
            <td width="166" valign="top">

                <!-- Inre-tabell Foto start -->
                <table id="photocol">
                    <tr>
                        <td id="photobox">
                            <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Employee Photo" width="164" /><br />
                            <label class="file-upload">
                                <input type="file" name="ffile" id="ffile" />
                            </label>
                        </td>
                    </tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td id="employeecode" style="color: #ffffff;">
                            EMPLOYEE CODE:<br />
                            <input type="text" name="femployeecode" id="femployeecode" size="7" maxlength="7" value="<?php echo htmlspecialchars($row['employeeCode']); ?>" readonly style="background-color: #333; color: #888;" />
                        </td>
                    </tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td id="securitylevel" style="color: #ffffff;">
                            SECURITY ACCESS LEVEL:<br />
                            <select class="select" name="fsecurityaccess" id="fsecurityaccess">
                                <option value="C" <?php echo ($row['securityAccessLevel'] === 'C') ? 'selected' : ''; ?>>C</option>
                                <option value="B" <?php echo ($row['securityAccessLevel'] === 'B') ? 'selected' : ''; ?>>B</option>
                                <option value="A" <?php echo ($row['securityAccessLevel'] === 'A') ? 'selected' : ''; ?>>A</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <!-- Inre-tabell Foto slut -->

            </td>
            <td width="135" valign="top">

                <!-- Inre-tabell Variabler start -->
                <table>
                    <tr><td class="variablecol" style="color: #ffffff;">NAME: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr><td class="variablecol" style="color: #ffffff;">DATE OF BIRTH: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr><td class="variablecol" style="color: #ffffff; padding-bottom: 5px;">SEX: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr><td class="variablecol" style="color: #ffffff; padding-bottom: 7px;">BLOOD TYPE: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr><td class="variablecol" style="color: #ffffff; padding-bottom: 10px;">HEIGHT: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr><td class="variablecol" style="color: #ffffff; padding-bottom: 10px;">WEIGHT: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr><td class="variablecol" style="color: #ffffff;">DEPARTMENT: &nbsp;</td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="variablecol" style="color: #ffffff;">
                            <div style="margin-top: -7px;">RANK: &nbsp;</div>
                        </td>
                    </tr>
                    <tr><td class="tablespacer"></td></tr>
                </table>
                <!-- Inre-tabell Variabler slut -->

            </td>
            <td width="245" valign="top">

                <!-- Inre-tabell Värden start -->
                <table>
                    <tr>
                        <td class="valuecol">
                            <input type="text" name="fname" id="fname" size="20" maxlength="255" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <input type="date" name="fdateofbirth" id="fdateofbirth" size="20" maxlength="11" value="<?php echo htmlspecialchars($row['dateOfBirth'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer" style="padding-bottom: 4px;"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <select class="select" name="fsex" id="fsex">
                                <option value="Male" <?php echo ($row['sex'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($row['sex'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($row['sex'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <input type="text" name="fbloodtype" id="fbloodtype" size="7" maxlength="4" value="<?php echo htmlspecialchars($row['bloodType'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <input type="text" name="fheight" id="fheight" size="7" maxlength="7" value="<?php echo htmlspecialchars($row['height'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <input type="text" name="fweight" id="fweight" size="7" maxlength="7" value="<?php echo htmlspecialchars($row['weight'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <input type="text" name="fdepartment" id="fdepartment" size="20" maxlength="255" value="<?php echo htmlspecialchars($row['department'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                    <tr>
                        <td class="valuecol">
                            <input type="text" name="frank" id="frank" size="20" maxlength="255" value="<?php echo htmlspecialchars($row['rank'] ?? ''); ?>" />
                        </td>
                    </tr>
                    <tr><td class="blackline"></td></tr>
                    <tr><td class="tablespacer"></td></tr>
                </table>
                <!-- Inre-tabell Värden slut -->

            </td>
            <td width="182" valign="top"></td>
        </tr>
    </table>
    <!-- Mellan-tabell slut -->

    <br />
    <h1>Background:</h1>
    <textarea rows="5" cols="45" name="fbackground" id="fbackground" wrap="off"><?php echo htmlspecialchars($row['background'] ?? ''); ?></textarea>
    
    <h1>Strengths:</h1>
    <textarea rows="5" cols="45" name="fstrengths" id="fstrengths" wrap="off"><?php echo htmlspecialchars($row['strengths'] ?? ''); ?></textarea>
    
    <h1>Weaknesses:</h1>
    <textarea rows="5" cols="45" name="fweaknesses" id="fweaknesses" wrap="off"><?php echo htmlspecialchars($row['weaknesses'] ?? ''); ?></textarea>
    
    <p />
    <input type="submit" class="button" value="Update Employee" />
    <a href="index.php?site=personnelregistry_read" class="button" style="text-decoration: none; padding: 3px 10px; background: #444; color: #fff;">Cancel</a>
</form>

<script>
function formCheckEditEmployee() {
    var name = document.getElementById("fname").value.trim();

    if (name === "") {
        alert("Name krävs!");
        document.getElementById("fname").focus();
        return false;
    }
    return true;
}
</script>