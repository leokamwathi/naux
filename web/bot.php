<?php
//Check for hub Challenge
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

        $pg_conn = pg_connect(pg_connection_string_from_database_url());

        $pid          = $fb->entry[0]->id;
        $sid          = $fb->entry[0]->messaging[0]->sender->id;
        // get message
        $message      = $fb->entry[0]->messaging[0]->message->text;
        //get payload
        $payload      = $fb->entry[0]->messaging[0]->postback->payload;
        $dbTable      = "jobsDBtest";
        //get username
        $user_details = file_get_contents("https://graph.facebook.com/v2.6/$sid?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=$token", false, $context);
        $username     = $user_details->first_name;

        setReplys();
        //chcek if new user

        if (isNewUser()) {
            if(addNewUser()){
                sendReply('new');
            }else{
                sendReply('new'); #failed to add user.. really what to do????
            }
        } else {
            if (is($payload)) {
                //job_findjob , qualification_collage-diploma
                $payloadPara = explode("_", $payload);
                if(setPayload($payloadPara))
                {
                    sendReply(nextStatus($payloadPara[0]));
                }else{
                    sendReply(getField('status'));
                }
            }else{
                if(is($message)){
                    if(setStatus(getField('status'),$message)){
                        sendReply(nextStatus(getField('status')));
                    }else{
                        sendReply(getField('status'));
                    }
                }else{
                    sendReply(getField('status'));
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
}

function setPayload($paypara)
{
    $isSet = false;
    switch ($paypara[0]) {
        case "job":
            setField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "location":
            setField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "experience":
            setField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "qualification":
            setField($paypara[0], $paypara[1]);
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
            setField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "location":
            setField($myStatus, $myMessage);
            $isSet = true;
            break;
        case "about":
            setField($myStatus, $myMessage);
            $isSet = true;
            break;
    }
    return $isSet;
}


function nextStatus($userStatus)
{
if(!is($userStatus)){
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
function is($str)
{
    if (isset($reply) && $reply != '') {
        return true;
    } else {
        return false;
    }
}
function sendReply($status)
{
    switch ($status) {
        case "new":
            $reply = $status_new;
            break;
        case "location":
            $reply = $status_location;
            break;
        case "job":
            $reply = $status_job;
            break;
        case "experience":
            $reply = $status_exp;
            break;
        case "qualification":
            $reply = $status_qualifications;
            break;
        case "about":
            $reply = $status_about;
            break;
        case "payload":
            $data  = array(
                'recipient' => array(
                    'id' => $sid
                ),
                'message' => array(
                    'text' => "Payload => " . $payload
                )
            );
            $reply = json_encode($data);
            break;
        default:
            $reply = $status_test;
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
}

function setup_database_connection()
{
    extract(parse_url($_ENV["DATABASE_URL"]));
    return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
}

function getField($field)
{
    $Query     = "SELECT $field from $dbTable where pageID ='$pid' and userID='$sid'";
    $rows      = pg_query($pg_conn, $Query);
    $fielddata = "";
    if (!pg_num_rows($result)) {
        $fielddata = "";
    } else {
        while ($row = pg_fetch_row($rows)) {
            $fielddata = $row[0];
        }
    }
    return $fielddata;
}

function addField($field, $value)
{
    $Query="UPDATE $dbTable SET ($field) = ('$value') where pageID ='$pid' and userID='$sid'";
    $rows  = pg_query($pg_conn, $Query);
    if(!$rows){
        return false;
    }else{
        return true;
    }
}

function insertUser()
{
    $Query = "INSERT INTO $dbTable (userID,pageID) VALUES ('$sid','$pid')";
    $rows  = pg_query($pg_conn, $Query);
    if(!$rows){
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

function setReplys()
{
    $status_info = '
                {"recipient":{
                    "id":"' . $sid . '"
                },
                "message":{
                    "text":"Hi ' . $username . ', This is the info we have from you.\n
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
    $status_new  = '
            {"recipient":{
                "id":"' . $sid . '"
            },
            "message":{
                "text":"Hi ' . $username . ', Welcome to the job bot. I will help you find a job or find job applicants. What do you want to do?",
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

    $status_location = '
        "{recipient":{
            "id":"' . $sid . '"
        },
        "message":{
            "text":"Please enter your job location : (city,country) eg. Nairobi, Kenya or use your current location.",
            "quick_replies":[
                {"content_type":"location",}
            ]
        }
    }';

    $status_job = '
    {"recipient":{
        "id":"' . $sid . '"
    },
    "message":{
        "text":"Hi, What kind of job are you looking for (Just one Job)? eg. Part time, Accountant, Web Designer,Chef, Sales Person, Programmer, House Help."
    }
}';

    $status_exp = '
{"recipient":{
    "id":"' . $sid . '"
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

    $status_qualifications = '
{"recipient":{
    "id":"' . $sid . '"
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

    $status_about = '
{"recipient":{
    "id":"' . $sid . '"
},
"message":{
    "text":"Hi, Tell us about yourself and the job you are looking for?"
}
}';

    //payload with links and images
    $status_test = '{"recipient": {
    "id": "' . $sid . '"
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
