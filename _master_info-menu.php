
<div class="rightloginform">

    <?php
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == "ok")
    {
    ?>
 <div style="font-family: Arial, sans-serif; font-size: 13px;">
        <span style="color: #45aeeb; font-weight: bold;">Welcome: <?php echo htmlspecialchars($_SESSION['employeename'] ?? ''); ?></span><br>
        <span style="color: #ffffff;">Employee Code: <?php echo htmlspecialchars($_SESSION['employeecode']); ?></span><br><br>
        
        <span style="color: #aaaaaa;">Login times: <?php echo htmlspecialchars($_SESSION['logintimes'] ?? ''); ?></span><br>
        <span style="color: #aaaaaa;">Last login date: <?php echo htmlspecialchars($_SESSION['lastlogin'] ?? ''); ?></span><br>
        <span style="color: #aaaaaa;">Last time: <?php echo htmlspecialchars($_SESSION['lastlogintimes'] ?? ''); ?></span><br>
        <span style="color: #2ecc71;">From host: <?php echo htmlspecialchars($_SESSION['fromhost'] ?? ''); ?></span><br>
    </div>
        <br/>
        <form>
            <input class="button" type="button" value="Logout" style='margin-right: 18px;' onClick="window.location.href='logout_doit.php';" />
            <input class="button" type="button" value="My Profile" onClick="window.location.href='index.php?site=personnelregistry_edit&code=<?php echo urlencode($_SESSION['employeecode']); ?>';" />
            <input class="button" style="margin-top: 18px;" type="button" value="Change Password" onClick="openModal('password_change.php')" />
            <p />
        </form>

    <?php 
    } 
    else 
    { 
        // "GLÖMT LÖSENORD?" OM INLOGGNINGEN MISSLYCKADES
        if (isset($_GET['error']) && $_GET['error'] == 'wrong_password') 
        {
            echo "<div style='color: red; margin-bottom: 10px; font-weight: bold;'>
                    Felaktigt användarnamn eller lösenord!
                  </div>";
            
            echo "<button type='button' class='button' style='background-color: #dc3545; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 3px; margin-bottom: 15px;' onclick=\"openModal('password_forgot.php')\">
                    Glömt lösenord?
                  </button><br/>";
        }
    ?>

        <form name="loginform" action="login_doit.php" onSubmit="return loginFormCheck()" method="post">
            Employee Code: <input type="text" name="employeecode" id="employeecode" size="7" maxlength="7" />
            <p />
            Password: <input type="password" name="password" id="password" size="10" maxlength="64" />
            <p />
            <input type="submit" class="button" value="Login" onClick="javascript:hashing()" />
            <input class="button" type="reset" value="Reset" />
            <p />
        </form>

    <?php } ?>

</div>

<!-- Pop-up rutan (dynamisk iframe så den kan visa både change.php och forgot.php) -->
<div id="passwordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 15px; border-radius: 5px; width: 320px;">
        <button style="float: right; cursor: pointer;" onClick="closePasswordModal()">X</button>
        <iframe id="modalIframe" src="" style="width: 100%; height: 300px; border: none;"></iframe>
    </div>
</div>

<script>
function openModal(pageUrl) 
{
    document.getElementById('modalIframe').src = pageUrl;
    document.getElementById('passwordModal').style.display = 'block';
}

function closePasswordModal() 
{
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('modalIframe').src = '';
}
</script>

<?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == "ok") 
    { ?>
    <div class="rightmenuitem">Right button 1</div>
    <a href="index.php?site=personnelregistry_read"class="rightmenuitem" style="text-decoration: none; color: inherit; display: inline-block;">Personnel Registry</a>
   <!---   Om man är level A  ---->
    <?php $accessLevel = $_SESSION['securityAccessLevel'] 
                ?? $_SESSION['securityaccesslevel'] 
                ?? $_SESSION['employee']['securityAccessLevel'] 
                ?? $_SESSION['employee']['securityaccesslevel'] 
                ?? '';

    // Jämför om nivån är 'A' (oavsett om det är litet eller stort 'a')
    if (strtoupper($accessLevel) === 'A') 
        { 
    ?>
        <a href="index.php?site=user_read" class="rightmenuitem" style="text-decoration: none; color: inherit; display: inline-block;">Users</a>
    <?php } ?>
<?php 
    } ?>