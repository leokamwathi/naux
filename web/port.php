<?php
/*
THINGS TO add
-Call button  https://developers.facebook.com/docs/messenger-platform/send-api-reference/call-button
-Share button. https://developers.facebook.com/docs/messenger-platform/send-api-reference/share-button
-MENUS MAN WTH!!!!!! - SOCIAL NEWS!!!! https://developers.facebook.com/docs/messenger-platform/messenger-profile/persistent-menu
-dont do shenzhen until post job is done!!!!!
*/

//GLOBAL variables
/*
global $fb;
global $piid;
global $GLOBALS['sid'];
global $message;
global $GLOBALS['payload'];
global $dbTable;
global $username;
global $datastream;
global $user_details;
*/
try{
//Check for hub Challenge
if (isset($_GET["hub_challenge"]) && $_GET["hub_challenge"] != '') {
    print_r($_GET["hub_challenge"]);
} else {
    //put bot setup here boy..

    // bot_setup();

    //function bot_setup()
    //{
        // get data stream

        logx("{SETUP}");
        $datastream = file_get_contents("php://input");
        //get fb data
        logx($datastream);
         $fb = json_decode($datastream);
        if (json_last_error() != "JSON_ERROR_NONE") {
            //print_r(json_last_error());
            file_put_contents("php://stderr", json_last_error().PHP_EOL);
        } else {
            $GLOBALS['pid']          = $fb->entry[0]->id;
            $GLOBALS['sid']          = $fb->entry[0]->messaging[0]->sender->id;
            // get message
            $GLOBALS['message']      = $fb->entry[0]->messaging[0]->message->text;
            //get payload
            $GLOBALS['quickReply']      = $fb->entry[0]->messaging[0]->message->quick_reply->payload;
            $GLOBALS['payload']      = $fb->entry[0]->messaging[0]->postback->payload;
            $GLOBALS['mid'] = $fb->entry[0]->messaging[0]->message->mid;
            $GLOBALS['dbTable']      = "jobsDBtest";

            //get username
            $GLOBALS['token']   = $_ENV["techware_fb_token"];
            $user_details = file_get_contents("https://graph.facebook.com/v2.6/".$GLOBALS['sid']."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=".$GLOBALS['token']);
            $user_details =  json_decode($user_details);
            $GLOBALS['username'] = $user_details->first_name;
            $GLOBALS['pg_conn'] = pg_connect(pg_connection_string_from_database_url());
            setReplys();

            //Payload processing
            if (isset($GLOBALS['payload']) && $GLOBALS['payload'] != '') {
                $GLOBALS['message'] = null;
                $GLOBALS['quickReply'] = null;
            }else{
                if (isset($GLOBALS['quickReply']) && $GLOBALS['quickReply'] != '') {
                    $GLOBALS['message'] = null;
                    $GLOBALS['payload'] = $GLOBALS['quickReply'];
                }
            }

            //chcek if new user
            sendMessage($GLOBALS['isTyping']);
            if (isNewUser()) {
                logx("{NEW USER..CREATING USER}");
                if(addNewUser()){
                    sendReply('userType');
                }else{
                    logx("{FAILED TO CREATE USER}");
                    //sendReply('new'); #failed to add user.. really what to do????
                }
            } else {
                logx("{CURRENT STATUS}".getField('status'));
                logx("{READING REPLY....}".$GLOBALS['message']);
                if (isset($GLOBALS['payload']) && $GLOBALS['payload'] != '') {
                    logx("{ISPAYLOAD}");
                    //job_findjob , qualification_collage-diploma
                    $payldPara = explode("_", $GLOBALS['payload']);
                    if($payldPara[0]=='search'){
                        //search_search-jobs
                        //search_job2
                        logx('{SEARCHING....}');
                        logx($GLOBALS['payload']);
                        sendMessage($GLOBALS["status_".$GLOBALS['payload']]);
                        logx($GLOBALS['smsg']);
                        logMSG($GLOBALS['log']);
                        //sendReply($payldPara[0]);
                    }else{
                    if(setPayload($payldPara))
                    {
                        $myNextStatus = nextStatus($payldPara[0]);
                        logx("{NEXT STATUS}".$myNextStatus);
                        sendReply($myNextStatus);
                    }else{
                        sendReply(getField('status'));
                    }
                }
                }else{
                    if (isset($GLOBALS['message']) && $GLOBALS['message'] != '') {
                        if($GLOBALS['mid'] == getField('lastNotification') ){
                            logx("{SAME MESSAGE AGAIN REALLY SUCKS}".$GLOBALS['message']);
                        }else{
                        addField('lastNotification',$GLOBALS['mid']);
                        logx("{IS MESSAGE}");
                        if(setStatus(getField('status'),$GLOBALS['message'])){
                            $myNextStatus = nextStatus(getField('status'));
                            logx("{NEXT STATUS}".$myNextStatus);
                            sendReply($myNextStatus);
                        }else{
                            if (is_string(getField('status'))){
                                sendReply(getField('status'));
                            }else{
                                logx("{SO THIS HAPPENS}");
                                sendReply('userType');
                            }
                        }
                    }
                    }else{
                        logx("{NOT PAYLOAD OR MESSAGE JUST SOME FB STUFF}".$GLOBALS['message']);
                        //sendReply('userType');
                    }

                }
            }

            /*
            @ check if is new user
            @ +if new creat startup data
            @ + status = new
            @ +ask new question

            @Check payload
            @+update payload
            @-send next/status message
            @Check current status
            @+If status needs message then check for message
            @+if message then add message to DB and ask next question


            WAIT for user input
            */
        }
        logx("Waiting for user reply");
    //}
}
} catch (Exception $e) {
    logx("{TRY ERROR}".$e->getMessage());
    // Handle exception
    //file_put_contents("php://stderr", "ERROR!!: = ".$e->getMessage().PHP_EOL);
}


function setPayload($paypara)
{
    logx("{CHECKING PAYLOAD}");
    $isSet = false;
    switch ($paypara[0]) {
        case "userType":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "job":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "location":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "experience":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "qualification":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "companyjob":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "companylocation":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "companyexperience":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "companyqualifications":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "edit":
            addField('status',$paypara[1]);
            addField('mode',$GLOBALS['payload']);
            //sendReply($paypara[1]);
            $isSet = true;
            break;
    }
    if ($paypara[0]!='edit' && $isSet == true){
        setMode();
    }
    return $isSet;
}
function setMode()
{
    logx("{SETTING MODE}");
    if(getField("mode")!=""){
        addField('mode','');
        if (getField("userType")=="Find-Job"){
            addField('status', 'info');
        }else{
            addField('status', 'companyinfo');
        }
    }
}
function setStatus($myStatus,$myMessage)
{
    logx("{UPDATING STATUS INFO}");
    $isSet = false;
    switch ($myStatus) {
        case "job":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "location":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "about":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companyname":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companyjob":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companylocation":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companydescription":
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companyemail":
            //TODO:test for valid email
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companyphone":
            //TODO:test valid phone
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "companywebsite":
            //TODO:test valid website
            addField($myStatus, $myMessage);
            $isSet = true;
            break;
    }
    if ($isSet == true){
        logx("{STATUS UPDATED}");
        setMode();
    }
    return $isSet;
}

function nextStatus($userStatus)
{

if(!is_string($userStatus)){
    $userStatus = getField('status');
}
logx("{GETTING NEXT STATUS CURRENT}".$userStatus);
$isMode = getField('mode');
    if(isset($isMode) && $isMode != ''){
        setMode();
        if (getField("userType")=="Find-Job"){
            return("info");
        }else{
            return("companyinfo");
        }
    }

    if ($userStatus == "info"){
        return($userStatus);
    }
    if ($userStatus == "companyinfo"){
        return($userStatus);
    }
    switch ($userStatus) {
        case "userType":
            if (getField("userType")=="Find-Job"){
                return("job");
            }else{
                return("companyname");
            }
        case "job":
            return("location");
        case "location":
            return("experience");
        case "experience":
            return("qualification");
        case "qualification":
            return("info");
        case "companyname":
            return("companydescription");
        case "companydescription":
            return("companyjob");
        case "companyjob":
            return("companylocation");
        case "companylocation":
            return("companyexperience");
        case "companyexperience":
            return("companyqualifications");
        case "companyqualifications":
            return("companywebsite");
        case "companywebsite":
            return("companyemail");
        case "companyemail":
            return("companyphone");
        case "companyphone":
            return("companyinfo");
        default:
            logx("{SETTING DEFAULT STATUS}");
            return("userType");
    }
}
function isStr($str)
{
     return(isset($GLOBALS['message']) && $GLOBALS['message'] != '');
}
function sendReply($status)
{

    switch ($status) {
        case "userType":
            $reply = $GLOBALS['status_userType'];
            break;
        case "location":
            $reply = $GLOBALS['status_location'];
            break;
        case "companylocation":
            $reply = $GLOBALS['status_companylocation'];
            break;
        case "job":
            $reply = $GLOBALS['status_job'];
            break;
        case "experience":
            $reply = $GLOBALS['status_experience'];
            break;
        case "qualification":
            $reply = $GLOBALS['status_qualifications'];
            break;
        case "about":
            $reply = $GLOBALS['status_about'];
            break;
        case "location":
            $reply = $GLOBALS['status_companylocation'];
            break;
        case "companyname":
            $reply = $GLOBALS['status_companyname'];
            break;
        case "companyjob":
            $reply = $GLOBALS['status_companyjob'];
            break;
        case "companyexperience":
            $reply = $GLOBALS['status_companyexperience'];
            break;
        case "companyqualifications":
            $reply = $GLOBALS['status_companyqualifications'];
            break;
        case "companydescription":
            $reply = $GLOBALS['status_companydescription'];
            break;
        case "companyemail":
            $reply = basicReply("Enter the email where applicants can send job appications.");
            break;
        case "companywebsite":
            $reply = basicReply("Enter the website/page where applicants can go to send job appications.");
            break;
        case "companyphone":
            $reply = basicReply("Enter the phone number where applicants can call to inquire about the job posting.");
            break;
        case "companyinfo":
            $reply = $GLOBALS['status_companyinfo'];
            break;
        case "info":
            $reply = $GLOBALS['status_info'];
            break;
        case "search":
            $reply = $GLOBALS[$GLOBALS['payload']];
            break;
        case "payload":
            $data  = array(
                'recipient' => array(
                    'id' => $GLOBALS['sid']
                ),
                'message' => array(
                    'text' => "Payload => " . $GLOBALS['payload']
                )
            );
            $reply = json_encode($data);
            break;
        default:
            $status = 'info';
            $reply = $GLOBALS['status_info'];
            break;
    }

/*
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => $reply,
            'header' => "Content-Type: application/json\n"
        )
    );
    $context = stream_context_create($options);
    //file_put_contents("php://stderr", "FB Context: = ".$context.PHP_EOL);
    $fbreply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=".$GLOBALS['token'], false, $context);
    //file_put_contents("php://stderr", "FB reply: = ".$fbreply.PHP_EOL);
*/
    sendMessage($reply);
    addField('status',$status);
    logx("{STATUS}.$status");
    logx("{REPLY}".$reply);
    logx("{FBREPLY}".$GLOBALS['fbreply']);
    logMSG($GLOBALS['log']);
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



function setup_database_connection()
{
    extract(parse_url($_ENV["DATABASE_URL"]));
    return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
}

function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));
  $dbOptions = "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
  logx("{DATABASE CONNECTION}".$dbOptions);
  return $dbOptions;
}



function pg_conx()
{
    return pg_connect(setup_database_connection());
}
function getField($field)
{
    $fielddata = "";
    $Query     = "SELECT $field from ".$GLOBALS['dbTable']." where pageID ='".$GLOBALS["pid"]."' and userID='".$GLOBALS["sid"]."'";
    $rows      = pg_query($GLOBALS['pg_conn'], $Query);

    if(!$rows){
        logx(pg_result_error($rows));
    }else{
    if (!pg_num_rows($rows)) {
        //no rows = no data
    } else {
        while ($row = pg_fetch_row($rows)) {
            $fielddata = $row[0];
        }
    }
}
    return $fielddata;
}

function addField($field, $value)
{

    $Query="UPDATE ".$GLOBALS['dbTable']." SET ($field) = ('$value') where pageID ='".$GLOBALS['pid']."' and userID='".$GLOBALS['sid']."'";
    $rows  = pg_query($GLOBALS['pg_conn'], $Query);
    if(!$rows){
        logx(pg_result_error($rows));
        return false;
    }else{
        return true;
    }
}

function isNewUser()
{
logx("{isNEWUSER}(".$GLOBALS['sid'].") = (".getField("userID").")");
    if($GLOBALS['sid'] == getField("userID")){
        return false;
    }else{
        return true;
    }
}

function insertUser()
{

    $Query = "INSERT INTO ".$GLOBALS['dbTable']." (userID,pageID) VALUES ('".$GLOBALS['sid']."','".$GLOBALS['pid']."')";
    $rows  = pg_query($GLOBALS['pg_conn'], $Query);
    if(!$rows){
        logx("{FAILED TO CREATE USER}");
        logx($Query);
        logx(pg_result_error($rows));
        logx(pg_last_error($GLOBALS['pg_conn']));
        return false;
    }else{
        logx("{NEW USER CREATED}");
        return true;
    }
}

function addNewUser()
{
    if (isNewUser()){
        if(insertUser()){
            addField("status","userType");
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function logx($msg){
    $GLOBALS['log'] = $GLOBALS['log']."\n".$msg; // file_put_contents("php://stderr", $msg.PHP_EOL);
}

function logMSG($msg){
    file_put_contents("php://stderr", $msg.PHP_EOL);
}

function getCompanyButtons(){
$buttons = "";
//'.getCompanyButtons($GLOBALS['sid']).'
//TODO: add share button first.
if(getField("companywebsite")!=''){
    $button = $button + '{
        "type": "web_url",
        "url": "'.getField("companywebsite").'",
        "title": "Job Website"
    },';
}
if(getField("companyemail")!=''){
    $button = $button + '{
        "type": "web_url",
        "url": "mailto:'.getField("companyemail").'",
        "title": "Email Company"
    },';
}
if(getField("companyphone")!=''){
    $button = $button + '{
          "type":"phone_number",
          "title":"Call Company",
          "payload":"'.getField("companyphone").'"
      },';
}
$button = $button + '{
        "type":"element_share"
      }';
    return $button;
}

function basicReply($msg){
    $myReply = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"'.$msg.'"
    }
}';
return $myReply;
}


function setReplys()
{
    logx("{SETTING REPLIES}");


    $GLOBALS['isTyping'] = '
                {"recipient":{
                    "id":"'.$GLOBALS['sid'].'"
                },
                "sender_action":"typing_on"
                }';

    $GLOBALS['status_info'] = '
                {"recipient":{
                    "id":"'.$GLOBALS['sid'].'"
                },
                "message":{
                    "text":"Hi '.$GLOBALS['username'].', \n
                    This is the info we have from you.\n
                    Location:' . getField('location') . '\n
                    Job:' . getField('job') . '\n
                    Qualification:' . getField('qualification') . '\n
                    Experience:' . getField('experience') . '\n
                    About:' . getField('about') . '\n",
                    "quick_replies":[
                        {
                            "content_type":"text",
                            "title":"Edit Location",
                            "payload":"edit_location"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Job",
                            "payload":"edit_job"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Qualification",
                            "payload":"edit_qualification"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Experience",
                            "payload":"edit_experience"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit About",
                            "payload":"edit_about"
                        },
                        {
                            "content_type":"text",
                            "title":"Search Jobs",
                            "payload":"search_job2"
                        }
                    ]
                }
            }';

    $GLOBALS['status_userType']  = '
            {"recipient":{
                "id":"' .$GLOBALS['sid']. '"
            },
            "message":{
                "text":"Hi ' . $GLOBALS['username'] . ',\n
                My name is Job Bot (job for short :-p ). \n
                I can help you find a job or find job applicants. \n
                What do you want to do?",
                "quick_replies":[
                    {
                        "content_type":"text",
                        "title":"Find Job",
                        "payload":"userType_Find-Job"
                    },
                    {
                        "content_type":"text",
                        "title":"Post Job",
                        "payload":"userType_Post-Job"
                    }
                ]
            }
        }';

    $GLOBALS['status_location'] = '
        {"recipient":{
            "id":"' . $GLOBALS['sid'] . '"
        },
        "message":{
            "text":"Please enter your job location : (city,country) \n(Nairobi, Kenya) or use your current location from fbmessager.",
            "quick_replies":[
                {"content_type":"location"}
            ]
        }
    }';

    $GLOBALS['status_job'] = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"What kind of job are you looking for (Just one Job)?\n eg. Part time, Accountant, Web Designer,Chef, Sales Person, Programmer, House Help)"
    }
}';

    $GLOBALS['status_experience'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"How many years have you worked at this job?",
    "quick_replies":[
        {
            "content_type":"text",
            "title":"First Job",
            "payload":"experience_First-Job"
        },
        {
            "content_type":"text",
            "title":"Under 1 year",
            "payload":"experience_Under-1-Year"
        },
        {
            "content_type":"text",
            "title":"1 to 3 years",
            "payload":"experience_1-to-3-years"
        },
        {
            "content_type":"text",
            "title":"4 to 8 years",
            "payload":"experience_4-to-8-years"
        },
        {
            "content_type":"text",
            "title":"9 years and over",
            "payload":"expexperience_9-years-and-over"
        }
    ]
}
}';

    $GLOBALS['status_qualifications'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is your job qualification Level?",
    "quick_replies":[
        {
            "content_type":"text",
            "title":"Self Taught",
            "payload":"qualification_Self-Taught"
        },
        {
            "content_type":"text",
            "title":"Certificate",
            "payload":"qualification_Certificate"
        },
        {
            "content_type":"text",
            "title":"Collage Diploma",
            "payload":"qualification_Collage-Diploma"
        },
        {
            "content_type":"text",
            "title":"University Degree",
            "payload":"qualification_University-Degree"
        },
        {
            "content_type":"text",
            "title":"Masters Degree",
            "payload":"qualification_Masters-Degree"
        }
    ]
}
}';

    $GLOBALS['status_about'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"Tell us a bit about yourself and the job you are looking for.\n
    eg.\n
    Hi,\n
    My name is job. \n
    I am 1 year old and I and very passionate about helping people find jobs and employees.\n
    I like challenges and will raise to any challenge i meet or atleat try my hardest."
}
}';

    //payload with links and images
    $GLOBALS['status_search_job1'] = '{"recipient": {
    "id": "' . $GLOBALS['sid'] . '"
},
"message": {
    "attachment": {
        "type": "template",
        "payload": {
            "template_type": "generic",
            "elements": [{
                "title": "SuperJob Test Ltd.",
                "subtitle": "We have a job opening for a '. getField('job') .'",
                "buttons": [{
                    "type": "web_url",
                    "url": "https://www.oculus.com/en-us/rift/",
                    "title": "See Job 1"
                }, {
                    "type": "postback",
                    "title": "Search Again",
                    "payload": "search_job2"
                }, {
                    "type": "postback",
                    "title": "Edit Profile",
                    "payload": "edit_info"
                }]
            }]
        }
    }
}
}';

$GLOBALS['status_search_job2'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "SuperJob Test Ltd.",
            "subtitle": "We have a job opening for a '. getField('job') .'",
            "buttons": [{
                "type": "web_url",
                "url": "https://www.oculus.com/en-us/rift/",
                "title": "See Job 2"
            }, {
                "type": "postback",
                "title": "Search Again",
                "payload": "search_job1"
            }, {
                "type": "postback",
                "title": "Edit Profile",
                "payload": "edit_info"
            }]
        }]
    }
}
}
}';

$GLOBALS['status_test'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "SuperJob Test Ltd.",
            "subtitle": "We have a job opening for a '. getField('job') .' ",
            "item_url": "https://www.oculus.com/en-us/rift/",
            "image_url": "http://messengerdemo.parseapp.com/img/rift.png",
            "buttons": [{
                "type": "web_url",
                "url": "https://www.oculus.com/en-us/rift/",
                "title": "Open Web URL"
            }, {
                "type": "postback",
                "title": "Callback",
                "payload": "PayloadTest"
            }]
        }]
    }
}
}
}';



// _________{COMPANY INFO}_________
/*
status_companyname
status_companydescription
status_companyjob
status_companyexperience
status_companyqualifications
*/

$GLOBALS['status_companyname'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is the name of your company."
}
}';

$GLOBALS['status_companyinfo'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "'.getField('companyname').'",
            "subtitle": "
            Job Description:- '.getField('companydescription').'\n
            Job:- '.getField('companyjob').'\n
            Location:- '.getField('companyLocation').'\n
            Experience:- '.getField('companyexperience').'\n
            Qualification:- '.getField('companyqualifications').'\n
            ",
            "buttons": [
            {
                "type": "postback",
                "title": "Edit Name",
                "payload": "edit_companyname"
            },
            {
                "type": "postback",
                "title": "Edit Description",
                "payload": "edit_companydescription"
            },
            {
                "type": "postback",
                "title": "Edit Location",
                "payload": "edit_companylocation"
            },
            {
                "type": "postback",
                "title": "Edit Experience",
                "payload": "edit_companyexperience"
            },
            {
                "type": "postback",
                "title": "Edit Qualification",
                "payload": "edit_companyqualification"
            },
            {
                "type": "postback",
                "title": "Exetend Time",
                "payload": "edit_companyjobtime"
            },
            {
                "type": "postback",
                "title": "Delete Job Posting",
                "payload": "edit_companydelete"
            }]
        }]
    }
}
}
}';

$GLOBALS['status_companyinfo2'] = '
{"recipient":{
    "id":"'.$GLOBALS['sid'].'"
},
"message":{
    "text":"Hi '.$GLOBALS['companyname'].', \n
    This is the info we have from you.\n
    Location:' . getField('location') . '\n
    Job:' . getField('job') . '\n
    Qualification:' . getField('qualification') . '\n
    Experience:' . getField('experience') . '\n
    About:' . getField('about') . '\n",
    "quick_replies":[
        {
            "content_type":"text",
            "title":"Edit Location",
            "payload":"edit_location"
        },
        {
            "content_type":"text",
            "title":"Edit Job",
            "payload":"edit_job"
        },
        {
            "content_type":"text",
            "title":"Edit Qualification",
            "payload":"edit_qualification"
        },
        {
            "content_type":"text",
            "title":"Edit Experience",
            "payload":"edit_experience"
        },
        {
            "content_type":"text",
            "title":"Edit About",
            "payload":"edit_about"
        },
        {
            "content_type":"text",
            "title":"Search Jobs",
            "payload":"search_job2"
        }
    ]
}
}';

$GLOBALS['status_companylocation'] = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"Please enter your job location : (city,country) \n(Nairobi, Kenya) or use your current location from fbmessager.",
        "quick_replies":[
            {"content_type":"location"}
        ]
    }
}';

$GLOBALS['status_companydescription'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"Enter a short description about the job."
}
}';

$GLOBALS['status_companyjob'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is the job opening you are posting for? (e.g. Accountant, Web Designer, Chef, Sales)."
}
}';

$GLOBALS['status_companyexperience'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"How much experience should job applicants have for this job?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"None",
        "payload":"companyexperience_None"
    },
    {
        "content_type":"text",
        "title":"1 month and over",
        "payload":"companyexperience_1-month-and-over"
    },
    {
        "content_type":"text",
        "title":"1 year and over",
        "payload":"companyexperience_1-year-and-over"
    },
    {
        "content_type":"text",
        "title":"4 years and over",
        "payload":"companyexperience_4-years-and-over"
    },
    {
        "content_type":"text",
        "title":"9 years and over",
        "payload":"companyexpexperience_9-years-and-over"
    }
]
}
}';

$GLOBALS['status_companyqualifications'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"What is the minimum qualification Level needed for the job?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"Self Taught",
        "payload":"companyqualifications_Self-Taught"
    },
    {
        "content_type":"text",
        "title":"Certificate",
        "payload":"companyqualifications_Certificate"
    },
    {
        "content_type":"text",
        "title":"Collage Diploma",
        "payload":"companyqualifications_Collage-Diploma"
    },
    {
        "content_type":"text",
        "title":"University Degree",
        "payload":"companyqualifications_University-Degree"
    },
    {
        "content_type":"text",
        "title":"Masters Degree",
        "payload":"companyqualifications_Masters-Degree"
    }
]
}
}';




}
