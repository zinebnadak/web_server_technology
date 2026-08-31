<?php
function readtextfile($target)
{
    //Kolla att textfilen existerar (target)
    if(file_exists($target))
    {
        if(!($fp =fopen($target, "r"))) die ("Can not open $target");
        $textcontent = fread($fp, filesize($target));
        fclose($fp);
        echo $textcontent;
    }
}


?>