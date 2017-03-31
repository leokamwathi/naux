<?php

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
            $GLOBALS['payload']      = $fb->entry[0]->messaging[0]->postback->payload;
            $GLOBALS['dbTable']      = "jobsDBtest";
            //get username
            $user_details = file_get_contents("https://graph.facebook.com/v2.6/".$GLOBALS['sid']."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=$token", false, $context);
            $GLOBALS['username']     = $user_details->first_name;
            $GLOBALS['pg_conn'] = pg_connect(setup_database_connection());
            setReplys();
            //chcek if new user

            if (isNewUser()) {
                logx("{NEW USER}");
                if(addNewUser()){
                    sendReply('new');
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
                    if(setPayload($payldPara))
                    {
                        sendReply(nextStatus($payldPara[0]));
                    }else{
                        sendReply(getField('status'));
                    }
                }else{
                    if (isset($GLOBALS['message']) && $GLOBALS['message'] != '') {
                        if($GLOBALS['message'] == getField('lastNotification') ){
                            logx("{SAME MESSAGE AGAIN REALLY SUCKS}".$GLOBALS['message']);
                        }else{
                        addField('lastNotification',$GLOBALS['message']);
                        logx("{IS MESSAGE}");
                        if(setStatus(getField('status'),$GLOBALS['message'])){
                            sendReply(nextStatus(getField('status')));
                        }else{
                            if (is_string(getField('status'))){
                                sendReply(getField('status'));
                            }else{
                                sendReply('new');
                            }
                        }
                    }
                    }else{
                        logx("{NOT PAYLOAD OR MESSAGE JUST SOME FB STUFF}".$GLOBALS['message']);
                        //sendReply('new');
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



function setPayload($paypara)
{
    $isSet = false;
    switch ($paypara[0]) {
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
        case "edit":
            sendReply($paypara[1]);
            $isSet = true;
            break;
    }
    return $isSet;
}

function setStatus($myStatus,$myMessage)
{
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
    }
    return $isSet;
}


function nextStatus($userStatus)
{
if(!is_string($userStatus)){
    $userStatus = getField('status');
}
    switch ($userStatus) {
        case "new":
            return("job");
        case "job":
            return("location");
        case "location":
            return("experience");
        case "experience":
            return("qualification");
        case "qualification":
            return("about");
        case "about":
            return("info");
        default:
            return("info");
    }

}
function isStr($str)
{
     return(isset($GLOBALS['message']) && $GLOBALS['message'] != '');
}
function sendReply($status)
{

    switch ($status) {
        case "new":
            $reply = $GLOBALS['status_new'];
            break;
        case "location":
            $reply = $GLOBALS['status_location'];
            break;
        case "job":
            $reply = $GLOBALS['status_job'];
            break;
        case "experience":
            $reply = $GLOBALS['status_exp'];
            break;
        case "qualification":
            $reply = $GLOBALS['status_qualifications'];
            break;
        case "about":
            $reply = $GLOBALS['status_about'];
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
            $status = 'new';
            $reply = $GLOBALS['status_new'];
            break;
    }

    $token   = $_ENV["techware_fb_token"];
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => $reply,
            'header' => "Content-Type: application/json\n"
        )
    );
    $context = stream_context_create($options);
    //file_put_contents("php://stderr", "FB Context: = ".$context.PHP_EOL);
    $fbreply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token", false, $context);
    //file_put_contents("php://stderr", "FB reply: = ".$fbreply.PHP_EOL);
    addField('status',$status);
    logx("{STATUS}.$status");
    logx("{REPLY}".$reply);
    logx("{FBREPLY}".$fbreply);
}

function setup_database_connection()
{
    extract(parse_url($_ENV["DATABASE_URL"]));
    return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
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

    if($GLOBALS['sid'] == getField("userID")){
        return true;
    }else{
        return false;
    }
}

function insertUser()
{

    $Query = "INSERT INTO ".$GLOBALS['dbTable']." (userID,pageID) VALUES ('".$GLOBALS['sid']."','".$GLOBALS['pid']."')";
    $rows  = pg_query($GLOBALS['pg_conn'], $Query);
    if(!$rows){
        logx("{FAILED TO CREATE USER}");
        logx(pg_result_error($rows));
        logx(pg_last_error($GLOBALS['pg_conn']));
        return false;
    }else{
        return true;
    }
}

function addNewUser()
{
    if(insertUser()){
        addField("status","new");
        return true;
    }else{
        return false;
    }
}

function logx($msg){
    file_put_contents("php://stderr", $msg.PHP_EOL);
}

function setReplys()
{
    logx("{SETTING REPLIES}");

    $GLOBALS['status_info'] = '
                {"recipient":{
                    "id":"'.$GLOBALS['sid'].'"
                },
                "message":{
                    "text":"Hi '.$GLOBALS['username'].', This is the info we have from you.\n
                    Location:' . getField('location') . '\n
                    Job:' . getField('job') . '\n
                    Qualification:' . getField('qualification') . '\n
                    Experience:' . getField('experience') . '\n
                    About:' . getField('about') . '\n
                    ",
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
                        }
                    ]
                }
            }';
    $GLOBALS['status_new']  = '
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
                        "payload":"new_Find-Job"
                    },
                    {
                        "content_type":"text",
                        "title":"Post Job",
                        "payload":"new_Post-Job"
                    }
                ]
            }
        }';

    $GLOBALS['$status_location'] = '
        "{recipient":{
            "id":"' . $GLOBALS['sid'] . '"
        },
        "message":{
            "text":"Please enter your job location : (city,country) \n
            eg. \n
            (Nairobi, Kenya) or use your current location from fbmessager.",
            "quick_replies":[
                {"content_type":"location",}
            ]
        }
    }';

    $GLOBALS['$status_job'] = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"What kind of job are you looking for (Just one Job)?\n
         eg. n\
         (Part time, Accountant, Web Designer,Chef, Sales Person, Programmer, House Help)"
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
    $GLOBALS['status_test'] = '{"recipient": {
    "id": "' . $GLOBALS['sid'] . '"
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
                    "payload": "PayloadTest"
                }]
            }]
        }
    }
}
}';
}
