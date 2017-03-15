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

$data = array(
'recipient' => array('id'=> $rid),
'message' => array('text'=>'I am alive')
);

$data = file_get_contents("php://input");

$options = array(
'http' => array(
'method' => 'POST',
'content' => json_encode(json_decode($data)),
'header' => "Content-Type: application/json\n"
)

);
$context = stream_context_create($options);



$fb = json_decode($data);
	$result = file_get_contents("http://fbbot.synax-solutions.com/bot.aspx?result=$fb", false, $context);
	print_r($result);
}