<?php
try{
require_once 'GooglePlaces.php';
require_once 'GooglePlacesClient.php';

if (isset($_GET["google_places_key"]) && $_GET["google_places_key"] != '') {
    $google_places = new joshtronic\GooglePlaces($_GET["google_places_key"]);
}else{
    $google_places = new joshtronic\GooglePlaces($_ENV["google_places_key"]);
}
if (isset($_GET["location"]) && $_GET["location"] != '') {
    $geoloc = explode(',', $_GET["location"]);
    $google_places->location = array($geoloc[0],$geoloc[1]);
}else{
$google_places->location = array(-33.86820, 151.1945860);
}
if (isset($_GET["rankby"]) && $_GET["rankby"] != '') {
$google_places->rankby   = $_GET["rankby"];
}else{
    $google_places->rankby   = 'distance';
}

if (isset($_GET["type"]) && $_GET["type"] != '') {
$google_places->types    =  array($_GET["type"]);
}else{
$google_places->types    = 'restaurant';
}

if (isset($_GET["radius"]) && $_GET["radius"] != '') {
$google_places->radius   = array($_GET["radius"]);
}else{
$google_places->radius   = 800;
}

// Requires keyword, name or types
$results                 = $google_places->nearbySearch();

print_r($results);
} catch (Exception $e) {
    print_r($e->getMessage());
}
?>
