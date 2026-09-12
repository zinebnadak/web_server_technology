<?php
session_start();
$pagename = "password_change.php";
$_SESSION['pagename'] = $pagename;
?>
<?php include("_security.php"); ?>
<!DOCTYPE html>
    
<html>
<head>
    
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 10px; }
        input[type="password"] { width: 90%; padding: 5px; margin-bottom: 10px; }
        .button { padding: 6px 12px; cursor: pointer; }
        .button-cancel { background-color: #6c757d; color: white; border: none; border-radius: 3px; }
    </style>
</head>
<body>

    <h3>Change Password</h3>
    
    <form action="password_change_doit.php" method="post">
        <label>Old Password:</label><br/>
        <input type="password" name="old_password" required /><br/>

        <label>New Password:</label><br/>
        <input type="password" name="new_password" required /><br/>

        <label>Confirm Password:</label><br/>
        <input type="password" name="confirm_password" required /><br/>

        <input class="button" type="submit" value="Save Password" />
        <input class="button button-cancel" type="button" value="Gå tillbaka" onclick="window.parent.closePasswordModal();" />
    </form>

</body>
</html>
