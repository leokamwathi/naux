<?php

//print_r($_GET["hub_challenge"]);
//file_put_contents("fb.txt",file_get_contents("php://input"));
try {
$fb = file_get_contents("php://input");
$fb = json_decode($fb);

//$sid = $fb->entry[0]->id;
$rid = $fb->entry[0]->messaging[0]->sender->id;

$message = $fb->entry[0]->messaging[0]->message->text;

//if ($message > 0){
if (isset($message) && $message != '') {

$token = "EAAN5JK8Gx7sBAGCZB5YulfJl4eoUCXGZABOm1oGRFH4kHubnxeANv8ZCVRQymrxqm0BEpzdULKWKhaBi5qXSbxZBrWhKud2U3ZAsBi1e8y3xCuKUMz9UF5XWRM8O9moGoIidAsUyCr3FLKjlXd0Q2WC70x6vmIZBwajPKXbxKU7AZDZD";

$data = array(
'recipient' => array('id'=> $rid),
'message' => array('text'=>'I am alive')
);

$options = array(
'http' => array(
'method' => 'POST',
'content' => json_encode($data),
'header' => "Content-Type: application/json\n"
)

);
$context = stream_context_create($options);


$reply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token", false, $context);
}
} catch (Exception $e) {
    // Handle exception
}
