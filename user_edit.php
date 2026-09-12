<?php
// LOCALS
$pagename = "user_edit.php";
$_SESSION['pagename'] = $pagename;

if (!isset($conn)) 
{
    if (file_exists("_config.php")) 
    {
        include_once("_config.php");
    }
}

// Hämta den användare som ska redigeras via URL
$code = $_GET['code'] ?? '';

if (empty($code)) 
{
    echo "<div class='main' style='color: red;'>Ingen användare angiven.</div>";
    exit();
}

// Hämta befintlig data för vald användare
$stmt = $conn->prepare("SELECT u.employeeCode, u.email, u.lockout, e.name 
                        FROM users u 
                        LEFT JOIN employee e ON u.employeeCode = e.employeeCode 
                        WHERE u.employeeCode = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();

if (!$currentUser) 
{
    echo "<div class='main' style='color: red;'>Användaren hittades inte i databasen.</div>";
    exit();
}

// Hämta alla employees för dropdown-menyn
$empResult = mysqli_query($conn, "SELECT employeeCode, name FROM employee ORDER BY employeeCode ASC");
$employees = [];
if ($empResult) 
{
    while ($r = mysqli_fetch_assoc($empResult)) 
    {
        $employees[] = $r;
    }
}
?>


<div class="main" style="max-width: 550px;">

    <h2 style="color: #ffffff;">Edit User</h2>

    <form name="userForm" action="user_edit_doit.php" method="post" onsubmit="return validateForm();">
        
        <input type="hidden" name="originalCode" value="<?php echo htmlspecialchars($currentUser['employeeCode']); ?>">

        <table style="width: 100%; border-spacing: 10px;">
            <tr>
                <td style="width: 160px; color: #ffffff;"><label for="employeeCodeSelect" style="color: #ffffff;">Employee Code:</label></td>
                <td>
                    <select id="employeeCodeSelect" name="employeeCodeSelect" onchange="handleSelectChange(this.value)" style="padding: 4px; font-size: 10px;">
                        <?php 
                        $isOther = true;
                        foreach ($employees as $emp): 
                            $selected = ($emp['employeeCode'] === $currentUser['employeeCode']) ? 'selected' : '';
                            if ($selected) $isOther = false;
                        ?>
                            <option value="<?php echo htmlspecialchars($emp['employeeCode']); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($emp['employeeCode']); ?>
                            </option>
                        <?php endforeach; ?>
                        
                        <option value="other" <?php echo $isOther ? 'selected' : ''; ?>>or other user name</option>
                    </select>

                    <input type="text" id="otherCodeInput" name="otherCodeInput" 
                           value="<?php echo $isOther ? htmlspecialchars($currentUser['employeeCode']) : ''; ?>" 
                           style="width: 130px; display: <?php echo $isOther ? 'inline-block' : 'none'; ?>;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="realName" style="color: #ffffff;">Real Name:</label></td>
                <td>
                    <input type="text" id="realName" name="realName" 
                           value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" 
                           style="width: 90%;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="lockout" style="color: #ffffff;">Locked out:</label></td>
                <td>
                    <input type="checkbox" id="lockout" name="lockout" value="X" <?php echo (strtoupper($currentUser['lockout'] ?? '') === 'X') ? 'checked' : ''; ?>>
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="email" style="color: #ffffff;">Email address:</label></td>
                <td>
                    <input type="text" id="email" name="email" 
                           value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" 
                           required style="width: 90%;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="password" style="color: #ffffff;">Password:</label></td>
                <td>
                    <input type="password" id="password" name="password" style="width: 90%;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="retypePassword" style="color: #ffffff;">Retype Password:</label></td>
                <td>
                    <input type="password" id="retypePassword" name="retypePassword" style="width: 90%;">
                </td>
            </tr>
        </table>

        <br>
        <div style="text-align: center;">
            <input type="submit" class="button" value="Update" style="padding: 5px 20px; cursor: pointer;">
            <a href="index.php?site=user_read" style="margin-left: 15px; color: #ffffff;">Cancel</a>
        </div>

    </form>
</div>

<script>
const employeeData = <?php echo json_encode(array_column($employees, 'name', 'employeeCode')); ?>;

function handleSelectChange(val) 
{
    const otherInput = document.getElementById('otherCodeInput');
    const realNameField = document.getElementById('realName');

    if (val === 'other') 
    {
        otherInput.style.display = 'inline-block';
        otherInput.required = true;
        realNameField.value = '';
    } 
    else 
    {
        otherInput.style.display = 'none';
        otherInput.required = false;
        realNameField.value = employeeData[val] || '';
    }
}

function validateForm() 
{
    const email = document.getElementById('email').value;
    const pwd = document.getElementById('password').value;
    const rePwd = document.getElementById('retypePassword').value;
    const selectVal = document.getElementById('employeeCodeSelect').value;
    const otherVal = document.getElementById('otherCodeInput').value;

    if (!email.includes('@')) 
    {
        alert('E-postadressen måste innehålla ett @-tecken.');
        return false;
    }

    if (selectVal === 'other' && otherVal.trim() === '') 
    {
        alert('Ange ett giltigt användarnamn.');
        return false;
    }

    if (pwd.length > 0) 
    {
        const hasUpperCase = /[A-Z]/.test(pwd);
        const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pwd);

        if (pwd.length < 8) 
        {
            alert('Lösenordet måste vara minst 8 tecken långt.');
            return false;
        }
        if (!hasUpperCase) 
        {
            alert('Lösenordet måste innehålla minst en stor bokstav (versal).');
            return false;
        }
        if (!hasSpecialChar) 
        {
            alert('Lösenordet måste innehålla minst ett specialtecken.');
            return false;
        }
        if (pwd !== rePwd) 
        {
            alert('Lösenorden matchar inte.');
            return false;
        }
    }

    return true;
}
</script>