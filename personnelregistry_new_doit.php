<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Anslut till databasen
if (!isset($conn)) {
    if (file_exists("_config.php")) {
        include_once("_config.php");
    }
}

if (file_exists("_globals.php")) {
    include_once("_globals.php");
}

if (!isset($conn)) {
    die("<p style='color:red;'>Kunde inte ansluta till databasen (\$conn saknas).</p>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Hämta alla textfält från formuläret
    $employeeCode        = trim($_POST['femployeecode'] ?? '');
    $securityAccessLevel = trim($_POST['fsecurityaccess'] ?? 'C');
    $name                = trim($_POST['fname'] ?? '');
    $dateOfBirth         = trim($_POST['fdateofbirth'] ?? '');
    $sex                 = trim($_POST['fsex'] ?? '');
    $bloodType           = trim($_POST['fbloodtype'] ?? '');
    $height              = trim($_POST['fheight'] ?? '');
    $weight              = trim($_POST['fweight'] ?? '');
    $department          = trim($_POST['fdepartment'] ?? '');
    $rank                = trim($_POST['frank'] ?? '');
    $background          = trim($_POST['fbackground'] ?? '');
    $strengths           = trim($_POST['fstrengths'] ?? '');
    $weaknesses          = trim($_POST['fweaknesses'] ?? '');
    
    // Signaturdatum – sätts till idag om det saknas
    $signatureDate       = !empty($_POST['fsignaturedate']) ? $_POST['fsignaturedate'] : date('Y-m-d');

    // Validering: Kod och Namn måste finnas
    if (empty($employeeCode) || empty($name)) {
        echo "<script>alert('Employee Code och Name krävs!'); window.history.back();</script>";
        exit();
    }

    // 2. Hantera bilduppladdning (ffile)
    $photoFilename = 'default.jpg'; // Standardbild om ingen laddas upp

    if (isset($_FILES['ffile']) && $_FILES['ffile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['ffile']['tmp_name'];
        $fileName    = $_FILES['ffile']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Tillåtna bildformat
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Skapa ett unikt filnamn baserat på Employee Code
            $newFileName = 'emp_' . preg_replace('/[^a-zA-Z0-9]/', '', $employeeCode) . '.' . $fileExtension;
            $uploadFileDir = './photos/';

            // Skapa mappen 'photos' om den inte finns
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photoFilename = $newFileName;
            }
        }
    }

    // 3. Kontrollera om Employee Code redan finns
    $checkStmt = $conn->prepare("SELECT employeeCode FROM employee WHERE employeeCode = ?");
    $checkStmt->bind_param("s", $employeeCode);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo "<script>alert('Fel: En anställd med kod \"$employeeCode\" finns redan!'); window.history.back();</script>";
        $checkStmt->close();
        exit();
    }
    $checkStmt->close();

    // 4. Sätt in i databasen
    $sql = "INSERT INTO employee (
                employeeCode, name, signatureDate, rank, securityAccessLevel, 
                dateOfBirth, sex, bloodType, height, weight, department, 
                background, strengths, weaknesses, photo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param(
            "sssssssssssssss", 
            $employeeCode, $name, $signatureDate, $rank, $securityAccessLevel,
            $dateOfBirth, $sex, $bloodType, $height, $weight, $department,
            $background, $strengths, $weaknesses, $photoFilename
        );

        if ($stmt->execute()) {
            // övning 12, aktivitetslogg 
            logactivity($_SESSION['employeecode'] ?? 'SYSTEM', date("Y-m-d"), date("H:i:s"), "New employee added", $employeeCode, "New employee $name added to personnel registry", "Employees");
            // Omdirigera till listan efter sparande
            header("Location: index.php?site=personnelregistry_read&msg=success");
            exit();
        } else {
            echo "<p style='color:red;'>Databasfel: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>SQL Prepare-fel: " . htmlspecialchars($conn->error) . "</p>";
    }

} else {
    header("Location: index.php?site=personnelregistry_read");
    exit();
}
?>