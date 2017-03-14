<?php

// include your composer dependencies
//require_once 'vendor/autoload.php';
require('../vendor/autoload.php');

$client = new Google_Client();
$client->setApplicationName("Anime");
$client->setDeveloperKey("AIzaSyA9VECd6_keyzCsId7hxxU-bcVAijMv6SM");

$service = new Google_FusiontablesService($client);

$selectQuery = "SELECT 'title', 'anime_type', 'episodes', 'synopsis', 'mal_score', 'mal_image','mal_id' FROM 1409399 ORDER BY mal_score DESC LIMIT 100";
echo "<pre>";
print_r($service->query->sql($selectQuery));
echo "</pre>";











