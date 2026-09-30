<?php
function randomBreadcrumImage()
{
    $breadcrumImages = array();

    // Kolla om användaren är inloggad
    $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === 'ok';

    // Sök efter 'bcrum' om inloggad, annars 'breadcrum'
    $searchTerm = $isLoggedIn ? 'bcrum' : 'breadcrum';

    $images = scandir('images');

    foreach ($images as $img) {
        // stristr söker efter ordet (oberoende av stora/små bokstäver)
        if (stristr($img, $searchTerm)) {
            $breadcrumImages[] = $img;
        }
    }

    // Fallback om inga bilder hittas i mappen
    if (empty($breadcrumImages)) {
        return $isLoggedIn ? 'bcrum_loggedIn01.jpg' : 'breadcrum_01.webp';
    }

    // Slumpa ett index ur listan
    $randomnr = array_rand($breadcrumImages);

    return $breadcrumImages[$randomnr];
}
?>