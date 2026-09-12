<?php
function randomBreadcrumImage()
{
    $breadcrumImages = array();

    $images = scandir('images');

    $j = 0;
    foreach ($images as $i) {
        if (strstr($i, 'breadcrum')) {
            $breadcrumImages[$j] = $i;
            $j++;
        }
    }

    $randomnr = rand(0, $j - 1);

    return $breadcrumImages[$randomnr];
}
?>