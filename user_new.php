
<?php
// LOCALS
$pagename = "user_new.php";
$_SESSION['pagename'] = $pagename;

if (!isset($conn)) 
{
    if (file_exists("_config.php")) 
    {
        include_once("_config.php");
    }
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

    <h2 style="color: #ffffff;">Create New User</h2>

    <form name="userForm" action="user_new_doit.php" method="post" onsubmit="return validateForm();">
        
        <table style="width: 100%; border-spacing: 10px;">
            <tr>
                <td style="color: #ffffff;"><label for="employeeCodeSelect" style="color: #ffffff;">Employee Code</label></td>
                <td>
                    <input type="text" id="employeeCodeSelect" name="employeeCodeSelect" style="width: 90%;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="realName" style="color: #ffffff;">Real Name:</label></td>
                <td>
                    <input type="text" id="realName" name="realName" style="width: 90%;">
                </td>
            </tr>


            <tr>
                <td style="color: #ffffff;"><label for="email" style="color: #ffffff;">Email address:</label></td>
                <td>
                    <input type="text" id="email" name="email" required style="width: 90%;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="password" style="color: #ffffff;">Password:</label></td>
                <td>
                    <input type="password" id="password" name="password" required style="width: 90%;">
                </td>
            </tr>

            <tr>
                <td style="color: #ffffff;"><label for="retypePassword" style="color: #ffffff;">Retype Password:</label></td>
                <td>
                    <input type="password" id="retypePassword" name="retypePassword" required style="width: 90%;">
                </td>
            </tr>
        </table>

        <br>
        <div style="text-align: center;">
            <input type="submit" class="button" value="Save" style="padding: 5px 20px; cursor: pointer;">
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

    if (selectVal === '') 
    {
        alert('Vänligen välj en Employee Code eller välj "or other user name".');
        return false;
    }

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

    // Vid ny användare MÅSTE lösenord anges
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

    return true;
}
</script>