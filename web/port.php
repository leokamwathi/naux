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


try {

$data = file_get_contents("php://input");

$options = array(
'http' => array(
'method' => 'POST',
'content' => $data,
'header' => "Content-Type: application/json\n"
)

);
$context = stream_context_create($options);

//$fb = json_decode($data);


//================================================================================================================

function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));
  return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
}
# Here we establish the connection. Yes, that's all.
$pg_conn = pg_connect(pg_connection_string_from_database_url());

$insertQuery = "INSERT INTO Json_Messages (json)
    VALUES ('$data')";
$result = pg_query($pg_conn, $insertQuery );



//================================================================================================================

$messageHost = file_get_contents("http://fbbot.synax-solutions.com/bot.aspx?result=$data", false, $context);
//print_r($result);


$fb = json_decode($data);

//$sid = $fb->entry[0]->id;
$rid = $fb->entry[0]->messaging[0]->sender->id;
$message = $fb->entry[0]->messaging[0]->message->text;


//if ($message > 0){
if (isset($message) && $message != '') {

if ($message == 'json') {
// work work work.... 
//$data = '{"recipient": {
//      "id": "'.$rid.'"
//    },
//    "message": {"text" : "I am alive people"}}';

$myJson = '{"recipient": {
      "id": "'.$rid.'"
    },
    "message": {
      "attachment": {
        "type": "template",
        "payload": {
          "template_type": "generic",
          "elements": [{
            "title": "rift",
            "subtitle": "Next-generation virtual reality",
            "item_url": "https://www.oculus.com/en-us/rift/",               
            "image_url": "http://messengerdemo.parseapp.com/img/rift.png",
            "buttons": [{
              "type": "web_url",
              "url": "https://www.oculus.com/en-us/rift/",
              "title": "Open Web URL"
            }, {
              "type": "postback",
              "title": "Callback",
             "payload": "Payload"
            }]
          }]
        }
      }
    }
  }';
  
  
  /*
  
  , {
            "title": "touch",
            "subtitle": "Your Hands, Now in VR",
            "item_url": "https://www.oculus.com/en-us/touch/",               
            "image_url": "http://messengerdemo.parseapp.com/img/touch.png",
            "buttons": [{
              "type": "web_url",
              "url": "https://www.oculus.com/en-us/touch/",
              "title": "Open Web URL"
            }, {
              "type": "postback",
              "title": "Call Postback",
              "payload": "Payload for second bubble"
            }]
          }
  
  */

  $token = "EAAN5JK8Gx7sBAGCZB5YulfJl4eoUCXGZABOm1oGRFH4kHubnxeANv8ZCVRQymrxqm0BEpzdULKWKhaBi5qXSbxZBrWhKud2U3ZAsBi1e8y3xCuKUMz9UF5XWRM8O9moGoIidAsUyCr3FLKjlXd0Q2WC70x6vmIZBwajPKXbxKU7AZDZD";

  //$data = json_decode($myJson);

$options = array(
'http' => array(
'method' => 'POST',
'content' => $myJson ,
'header' => "Content-Type: application/json\n"
)

);
$context = stream_context_create($options);


$reply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token", false, $context);
  
  }else{
	
	
	if (isset($messageHost) && $messageHost != '') {

$token = "EAAN5JK8Gx7sBAGCZB5YulfJl4eoUCXGZABOm1oGRFH4kHubnxeANv8ZCVRQymrxqm0BEpzdULKWKhaBi5qXSbxZBrWhKud2U3ZAsBi1e8y3xCuKUMz9UF5XWRM8O9moGoIidAsUyCr3FLKjlXd0Q2WC70x6vmIZBwajPKXbxKU7AZDZD";

$data = array(
'recipient' => array('id'=> $rid),
'message' => array('text'=> $messageHost)
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
}
}
} catch (Exception $e) {
    // Handle exception
}
	
	
	
	
	
	
	
	
	
	
	
	
}