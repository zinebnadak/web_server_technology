<?php
function randomBreadcrumImage()
{
    $breadcrumimages = array();

    //Läs in alla bilder i en array
    $images = scandir('images');

    //Loopa genom arrayen
    $j = 0;
    foreach($images as $i)
    {
        // Kollar om filnamnet innehåller "breadcrum
        if(strstr($i, 'breadcrum'))
        {
            // Läs in bilden i en breadcrum
            $breadcrumimages[$j] = $i;
            $j++;
        }   
    }
     // Skapa random- bilden
    $randomnr = rand(0,$j-1);

     // Returnera random- bilden
    return $breadcrumimages[$randomnr];
}

?>