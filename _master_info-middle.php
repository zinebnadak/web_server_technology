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
}
echo "Navigation: <a href=\"http://". $siteaddress . "\">". $siteaddress."</a> / " . $site ;
readtextfile($target);
?>
  
</div>