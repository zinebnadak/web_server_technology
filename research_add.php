// övning 13 forskningsdatabas, formulär
// besök http://thenest.umbrellacorp.top/index.php?site=research_add

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$pagename = "research_add.php";
$_SESSION['pagename'] = $pagename;

// Security Access Level B eller högre
$currentUserAccessLevel = (string)($_SESSION['securityAccessLevel'] ?? $_SESSION['user']['securityAccessLevel'] ?? '');
if (!in_array(strtoupper($currentUserAccessLevel), ['A', 'B'])) 
{
    die("<p style='color:red;'>Åtkomst nekad: Det krävs säkerhetsnivå B eller högre.</p>");
}
?>

<div style="color: #fff; font-family: Arial, sans-serif;">
    <h3>Add New Research Object</h3>

    <form action="research_add_doit.php" method="post" enctype="multipart/form-data">

        Number: <input type="text" name="fnumber" size="10" placeholder="TCL#12" /><p/>
        Name: <input type="text" name="fname" size="30" /><p/>
        Text: <br/>
        <textarea name="ftext" rows="5" cols="50"></textarea><p/>

        Status: 
        <select name="fstatus">
            <option value="Open">Open</option>
            <option value="Archived">Archived</option>
        </select><p/>

        Security Data Sheet (PDF): <input type="file" name="fdatasheet" /><p/>
        Security Presentation Video (URL): <input type="text" name="fpresentationvideo" size="40" placeholder="http://www.youtube.com/..." /><p/>
        Security Handling Video (URL): <input type="text" name="fhandlingvideo" size="40" placeholder="http://www.youtube.com/..." /><p/>

        <input class="button" type="submit" value="Create" />
        <input class="button" type="reset" value="Reset" />

    </form>
</div>