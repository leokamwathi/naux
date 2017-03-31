<?php

if (isset($_GET["hub_challenge"]) && $_GET["hub_challenge"] != '') {
    print_r($_GET["hub_challenge"]);
} else {
    bot_setup();
}

function bot_setup()
{
    // get data stream
    $datastream = file_get_contents("php://input");
    //get fb data
    $fb         = json_decode($data);
    if (json_last_error() != "JSON_ERROR_NONE") {
        print_r(json_last_error());
    } else {
        $pid          = $fb->entry[0]->id;
        $sid          = $fb->entry[0]->messaging[0]->sender->id;
        // get message
        $message      = $fb->entry[0]->messaging[0]->message->text;
        // check if message is a fieldname

        //if messgae create payload and send back
        if(filter_var($message, FILTER_VALIDATE_URL))
{
        file_put_contents("Tmpfile.zip", file_get_contents("$message"));

    }  
/*

curl -X POST -H "Content-Type: application/json" -d '{
  "recipient":{
    "id":"USER_ID"
  },
  "message":{
    "attachment":{
      "type":"file",
      "payload":{
        "url":"https://petersapparel.com/bin/receipt.pdf"
      }
    }
  }
}' "https://graph.facebook.com/v2.6/me/messages?access_token=PAGE_ACCESS_TOKEN"
File upload

curl  \
  -F recipient='{"id":"USER_ID"}' \
  -F message='{"attachment":{"type":"file", "payload":{}}}' \
  -F filedata=@/tmp/receipt.pdf \
  "https://graph.facebook.com/v2.6/me/messages?access_token=PAGE_ACCESS_TOKEN"

*/

//else echo back message



    }
}



 ?>
