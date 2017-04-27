<?php
//More to come
require_once 'replymessagesCore.php';
require_once 'commonCore.php';
require_once 'KaziBotCore.php';
require_once 'findCore.php';
require_once 'databaseCore.php';

/*
THINGS TO add
-Call button  https://developers.facebook.com/docs/messenger-platform/send-api-reference/call-button
-Share button. https://developers.facebook.com/docs/messenger-platform/send-api-reference/share-button
-MENUS MAN WTH!!!!!! - SOCIAL NEWS!!!! https://developers.facebook.com/docs/messenger-platform/messenger-profile/persistent-menu
-dont do shenzhen until post job is done!!!!!

// => ✖ ✔️ 🆗 🔘 ❤ 🤖 📲 📞 📱
*/

if (isset($_GET["hub_challenge"]) && $_GET["hub_challenge"] != '') {
    print_r($_GET["hub_challenge"]);
} else {
        logx("{SETUP}");
        $datastream = file_get_contents("php://input");
        if(!(isset($datastream))){
            file_put_contents("php://stderr", "!!!!!!!!-----FORCED EXIT-----!!!!!!!!!".PHP_EOL);
            exit("");
        }
        //get fb data
        logx($datastream);
         $fb = json_decode($datastream);
        if (json_last_error() != "JSON_ERROR_NONE") {
            //print_r(json_last_error());
            file_put_contents("php://stderr", json_last_error().PHP_EOL);
        } else {
            foreach ($fb->entry as $entry) {
                $GLOBALS['pid'] = $entry->id;
                $GLOBALS['sid'] = $entry->messaging[0]->sender->id;
                $GLOBALS['token'] = $_ENV["techware_fb_token"];
                $GLOBALS['isTyping'] = '{"recipient":{"id":"'.$GLOBALS['sid'].'"},"sender_action":"typing_on"}';
                sendMessage($GLOBALS['isTyping']);
                //send the is typing message
                // get message
                $GLOBALS['message'] = $entry->messaging[0]->message->text;
                //get payload
                $GLOBALS['quickReply'] = $entry->messaging[0]->message->quick_reply->payload;
                $GLOBALS['payload'] = $entry->messaging[0]->postback->payload;
                $GLOBALS['locationGeoLat'] = $entry->messaging[0]->message->attachments[0]->payload->coordinates->lat;
                $GLOBALS['locationGeoLong'] = $entry->messaging[0]->message->attachments[0]->payload->coordinates->long;
                $GLOBALS['locationTitle'] = $entry->messaging[0]->message->attachments[0]->title;
                $GLOBALS['mid'] = $entry->messaging[0]->message->mid;
                $GLOBALS['dbTable'] = $_ENV["main_db_table"];
                KaziBot();
            }
        }
    }

?>
