// övning 13 forskningsdatabas
// GD bibliotek, bildhantering 

<?php
function createthumbnail($folder, $file, $nwidth, $nheight) 
{ 
    $filename = $file;
    $filepath = $folder . $file;
    $abc = imagecreatefromjpeg($filepath); 
    $def = imagecreatetruecolor($nwidth, $nheight); 
    $background_color = imagecolorallocate($def, 0, 0, 0);
    list($width, $height, $type, $attr) = getimagesize($filepath);
    imagecopyresized($def, $abc, 0, 0, 0, 0, $nwidth, $nheight, $width, $height); 
    imagejpeg($def, $folder . "thumb_" . $filename, 95); 
    imagedestroy($abc); 
    imagedestroy($def); 
}

function resizeimage($folder, $file, $nwidth, $nheight) 
{ 
    $filename = $folder . $file;
    list($width, $height, $type, $attr) = getimagesize($filename);

    // Kollar om bilden är stående eller liggande
    if ($width > $height)
    {
        $newwidth = $nwidth;
        $temp = $newwidth / $width;
        $newheight = $temp * $height;
    }
    else
    {
        $newheight = $nheight;
        $temp = $newheight / $height;
        $newwidth = $temp * $width;
    }

    // Skala om bilden om den är större än max-mått
    if ($width > $nwidth || $height > $nheight)
    {
        $abc = imagecreatefromjpeg($filename);
        $def = imagecreatetruecolor($newwidth, $newheight); 
        $background_color = imagecolorallocate($def, 0, 0, 0);
        imagecopyresized($def, $abc, 0, 0, 0, 0, $newwidth, $newheight, $width, $height); 
        imagejpeg($def, $filename, 95); 
        imagedestroy($abc); 
        imagedestroy($def);
    }
}
?>
