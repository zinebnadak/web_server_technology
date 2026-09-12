<?php
session_start();
$pagename = "password_forgot.php";
$_SESSION['pagename'] = $pagename;
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 15px; }
        input[type="text"], input[type="email"] 
        { 
            width: 90%; 
            padding: 6px; 
            margin-top: 4px;
            margin-bottom: 12px; 
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button 
        { 
            padding: 7px 14px; 
            cursor: pointer; 
            border: none;
            border-radius: 4px;
            font-size: 13px;
        }
        .button-submit { background-color: #007bff; color: white; }
        .button-submit:hover { background-color: #0056b3; }
        .button-cancel { background-color: #6c757d; color: white; margin-left: 5px; }
        .button-cancel:hover { background-color: #5a6268; }
    </style>
</head>
<body>

    <h3>Återställ lösenord</h3>
    <p style="font-size: 12px; color: #666;">Fyll i dina uppgifter för att få ett nytt automatgenererat lösenord skickat till din e-post.</p>
    
    <form action="password_forgot_doit.php" method="post">
        <label>Employee Code:</label><br/>
        <input type="text" name="employeecode" required /><br/>

        <label>E-postadress:</label><br/>
        <input type="email" name="email" required /><br/>

        <div style="margin: 15px 0;">
            <label style="cursor: pointer;">
                <input type="checkbox" name="not_robot" id="not_robot" required>
                I am not a robot
            </label>
        </div>

        <input class="button button-submit" type="submit" value="Skicka nytt lösenord" />
        <input class="button button-cancel" type="button" value="Gå tillbaka" onclick="window.parent.closePasswordModal ? window.parent.closePasswordModal() : window.location.href='index.php';" />
    </form>

</body>
</html>