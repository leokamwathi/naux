<?php

if (isset($_GET["hub_challenge"]) && $_GET["hub_challenge"] != '') {
    print_r($_GET["hub_challenge"]);
} else {

    $datastream = file_get_contents("php://input");
    if(!(isset($datastream))){
        file_put_contents("php://stderr", "!!!!!!!!-----WAPI BOT FORCED EXIT-----!!!!!!!!!".PHP_EOL);
        exit("");
    }
    $fb = json_decode($datastream);
   if (json_last_error() != "JSON_ERROR_NONE") {
       //print_r(json_last_error());
       file_put_contents("php://stderr", json_last_error().PHP_EOL);
   } else {


   foreach ($fb->entry as $entry) {
       $GLOBALS['pid'] = $entry->id;
       $GLOBALS['sid'] = $entry->messaging[0]->sender->id;
       $GLOBALS['token'] = $_ENV["wapibot_fb_token"];
       $GLOBALS['isTyping'] = '{"recipient":{"id":"'.$GLOBALS['sid'].'"},"sender_action":"typing_on"}';
       sendMessage($GLOBALS['isTyping']);

       // get message
       $GLOBALS['message'] = $entry->messaging[0]->message->text;
       //get payload
       $GLOBALS['quickReply'] = $entry->messaging[0]->message->quick_reply->payload;
       $GLOBALS['payload'] = $entry->messaging[0]->postback->payload;
       $GLOBALS['locationGeoLat'] = $entry->messaging[0]->message->attachments[0]->payload->coordinates->lat;
       $GLOBALS['locationGeoLong'] = $entry->messaging[0]->message->attachments[0]->payload->coordinates->long;
       $GLOBALS['locationTitle'] = $entry->messaging[0]->message->attachments[0]->title;

       $GLOBALS['mid'] = $entry->messaging[0]->message->mid;
       wapiBot();
   }

}
}

function wapiBot(){
    $GLOBALS['dbTable']      = "jobsDBtest";
    //get username
    $user_details = file_get_contents("https://graph.facebook.com/v2.6/".$GLOBALS['sid']."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=".$GLOBALS['token']);
    $user_details =  json_decode($user_details);
    $GLOBALS['username'] = $user_details->first_name;
    //$GLOBALS['pg_conn'] = pg_connect(pg_connection_string_from_database_url());
    $GLOBALS['message'] = preg_replace("/[^A-Za-z0-9 ]/", '', $GLOBALS['message']);
    $GLOBALS['message'] = substr($GLOBALS['message'], 0, 80);
    if(isset($GLOBALS['message']) && $GLOBALS['message'] != ''){
        sendMessage("Hello, My name is wapi bot. How can I help you today.");
    }

}

function sendMessage($msg){
    $GLOBALS['smsg'] = $msg;
    $msg = trim(preg_replace('/\s+/', ' ', $msg));
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => $msg,
            'header' => "Content-Type: application/json\n"
        )
    );
    $context = stream_context_create($options);
    //file_put_contents("php://stderr", "FB Context: = ".$context.PHP_EOL);
    $GLOBALS['fbreply'] = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=".$GLOBALS['token'], false, $context);
}

 ?>
