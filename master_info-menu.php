<form name="loginform" action="login_doit.php" onSubmit="return loginFormChek()"
        onSubmit="return formChek()" enctype="multipart/form-data" method="post">

    Employee Code:
    <input type="text" name="employeecode" id="employeecode" size="7" maxlength="7" />
    <p />
    Password:
    <input type="password" name="password" id="password" size="10" maxlength="10" />
    <p />
    <input type="submit" class="button" value="Login" onClick="javascript:hashing()" />
    <input class="button" type="reset" value="Reset" />
    <p />
</form>