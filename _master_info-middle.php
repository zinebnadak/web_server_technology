<?php
echo "Navigation: <a href=\"http://" . $siteaddress . "\">" .$siteaddress."</a> \ " . $site;
readtextfile($target);
if(isset($_GET["site"]))
{
    $site = $_GET["site"];
    if($site=="")
    {
        $target = "text/index.html";
    }
    else
    {
        $target = "text/".$site.".html";
    }
    readtextfile($target);
}
else
{
    $target = "text/index.html";
    readtextfile($target);
}


?>