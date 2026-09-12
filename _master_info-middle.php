
<div class="main">

<?php
if(isset($_GET['site']))
{
    $site = $_GET["site"];
    if($site=="")
    {
        $target="text/index.html";
        $site="";
    }
    else
    {
	    $target="text/".$site.".html";
    }
}
else
{
    $target="text/index.html";
    $site="";
}   // Letar upp text fil eller php fil
echo "Navigation: <a href=\"http://". $siteaddress . "\" style=\"color: #45aeeb;\">". $siteaddress." </a> / " . $site ;
if (file_exists($site . ".php") && $site != "") 
{
    include($site . ".php");
} 
else 
{
    readtextfile($target);
}
?>
  
</div>