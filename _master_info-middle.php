<div class="main">

<?php
if(isset($_GET['site']))
{
    $site = $_GET["site"];
    if($site=="")
    {
        $target="text/index.html";
    }
    else
    {
	    $target="text/".$site.".html";
    }
    readtextfile($target);
}
else
{
    $target="text/index.html";
    readtextfile($target);
}
?>
  
</div>