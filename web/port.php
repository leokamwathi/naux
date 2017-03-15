<?php
/*
$ch = curl_init('http://fbbot.synax-solutions.com/bot.aspx');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
*/


if (isset($_GET["hub_challenge"]) && $_GET["hub_challenge"] != '') {
	print_r($_GET["hub_challenge"]);
}else{
$content = file_get_contents("php://input");
$fb = json_decode($content);
	$result = file_get_contents("http://fbbot.synax-solutions.com/bot.aspx?result=$fb", false, $content);
	print_r($result);
}