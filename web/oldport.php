<?php

if (isset($_GET["hub_challenge"]) && $_GET["hub_challenge"] != '') {
    print_r($_GET["hub_challenge"]);


}else{

  try {

function setup(){
      $data = file_get_contents("php://input");

      //$fb = json_decode($data);



      /// PAYLOAD Format
      ///{"object":"page","entry":[{"id":"763933067090623","time":1490364301410,"messaging":[{"recipient":{"id":"763933067090623"},"timestamp":1490364301410,"sender":{"id":"1486644564679609"},"postback":{"payload":"Payload"}}]}]}

      $fb = json_decode($data);

      $pid = $fb->entry[0]->id;
      $sid = $fb->entry[0]->messaging[0]->sender->id;
      $message = $fb->entry[0]->messaging[0]->message->text;
      $payload = $fb->entry[0]->messaging[0]->postback->payload;
      $dbTable = "jobsDBtest";

      $user_details = file_get_contents("https://graph.facebook.com/v2.6/$sid?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=$token", false, $context);

      $username = $user_details->first_name;


      //////////////////////////////////



            if (isset($message) && $message != '') {
              if (isset($payload) && $payload != '') {
                $message = "payload";
              }
            }
}
///get user details
/*
{
   "first_name": "Leo",
   "last_name": "Kamwathi",
   "profile_pic": "https://scontent.xx.fbcdn.net/v/t31.0-1/13575942_10157052626480484_7932057330509988985_o.jpg?_nc_eui2=v1\u00253AAeHelZNfNdVptzNSFmR5e48UhJkTA_WzMz9rmB1iU5ydsSm4hwrY6lDKe-nsBA95b_fg-2k2SOPUW0isL3-GdfKPLpMzt0Pt9ELXI-lapwR_Gg&oh=19b07b85b8599e72c116b35c4110820b&oe=59676BE9",
   "locale": "en_US",
   "timezone": 3,
   "gender": "male"
}
*/


//====================================================================================================================//
/////////////////Reply Messages
function getReplys(){
$status_new = '
{"recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Hi '.$username.', Welcome to the job bot. I will help you find a job or find job applicants. What do you want to do?",
    "quick_replies":[
      {
        "content_type":"text",
        "title":"Find Job",
        "payload":"new_findJob"
      },
      {
        "content_type":"text",
        "title":"Post Job",
        "payload":"new_postJob"
      }
    ]
   }
  }';

$status_location = '
  "{recipient":{
    "id":"'.$sid.'"
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
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Hi, What kind of job are you looking for (Just one Job)? eg. Part time, Accountant, Web Designer,Chef, Sales Person, Programmer, House Help."
   }
  }';

$status_exp = '
{"recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"How many years have you worked at this job?",
    "quick_replies":[
	   {
        "content_type":"text",
        "title":"First Time",
        "payload":"exp_firsttime"
      },
      {
        "content_type":"text",
        "title":"Under 1 year",
        "payload":"exp_under-1"
      },
      {
        "content_type":"text",
        "title":"1 to 3 years",
        "payload":"exp_1-to-3"
      },
      {
        "content_type":"text",
        "title":"4 to 8 years",
        "payload":"exp_4-to-8"
      },
      {
        "content_type":"text",
        "title":"9 years and above",
        "payload":"exp_Over-9"
      }
    ]
   }
  }';

$status_qualifications = '
{"recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"What is your job qualification Level?",
    "quick_replies":[
      {
        "content_type":"text",
        "title":"Self Taught",
        "payload":"qualification_selfTaught"
      },
      {
        "content_type":"text",
        "title":"Certificate",
        "payload":"qualification_certificate"
      },
      {
        "content_type":"text",
        "title":"Collage Diploma",
        "payload":"qualification_collage-diploma"
      },
      {
        "content_type":"text",
        "title":"University Degree",
        "payload":"qualification_university-degree"
      },
      {
        "content_type":"text",
        "title":"Masters Degree",
        "payload":"qualification_masters-degree"
      }
    ]
   }
  }';

$status_about = '
{"recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Hi, Tell us about yourself and the job you are looking for?"
   }
 }';


  $status_test = '{"recipient": {
      "id": "'.$sid.'"
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


  //====================================================================================================

  switch ($message) {
      case "new":
          $reply = $status_new;
          break;
      case "location":
          $reply = $status_location;
          break;
      case "job":
          $reply = $status_job;
          break;
      case "exp":
          $reply = $status_exp;
          break;
      case "qualification":
          $reply = $status_qualifications;
          break;
      case "about":
          $reply = $status_about;
          break;
      case "payload":
      $data = array(
          'recipient' => array('id'=> $sid),
          'message' => array('text'=> "Payload => ".$payload)
      );
        $reply = json_encode($data);
        break;
      default:
          $reply = $status_test;
          break;
          }

          return $reply;
};
//====================================================================================================================//
//////Hub Challenge
/*
$ch = curl_init('http://fbbot.synax-solutions.com/bot.aspx');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
*/



    //================================================================================================================
    /////// Read Input set fb variables




        //===================================================================================================
        // database connections

        function pg_connection_string_from_database_url() {
            extract(parse_url($_ENV["DATABASE_URL"]));
            return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
        }

        function getField($field){
        # Here we establish the connection. Yes, that's all.
        $pg_conn = pg_connect(pg_connection_string_from_database_url());

        $Query = "SELECT $field from $dbTable where pageID ='$pid' and userID='$sid'";

        //$insertQuery = "INSERT INTO Json_Messages (json) VALUES ('$data')";

        $rows = pg_query($pg_conn, $Query );
        $fielddata = "";

        if (!pg_num_rows($result)) {
            $fielddata = "";
            //Add intial Data
        } else {
            //print "Json Messages in your database:\n";
            //while ($row = pg_fetch_row($result)) { print("- $row[0]\n"); }
            while ($row = pg_fetch_row($rows)) {
                //$row = pg_fetch_row($result)
                $fielddata  = $row[0];
            }
        }
        return $fielddata;
      }

      function addField($field,$value){
      # Here we establish the connection. Yes, that's all.
      $pg_conn = pg_connect(pg_connection_string_from_database_url());

      $Query = "UPDATE $field from $dbTable where pageID ='$pid' and userID='$sid'";

      //$insertQuery = "INSERT INTO Json_Messages (json) VALUES ('$data')";

      $rows = pg_query($pg_conn, $checkStatusQuery );
      $fielddata = "";

      if (!pg_num_rows($result)) {
          $fielddata = "";
          //Add intial Data
      } else {
          //print "Json Messages in your database:\n";
          //while ($row = pg_fetch_row($result)) { print("- $row[0]\n"); }
          while ($row = pg_fetch_row($rows)) {
              //$row = pg_fetch_row($result)
              $fielddata  = $row[0];
          }
      }
      return $fielddata;
    }

    function insertNew(){
    # Here we establish the connection. Yes, that's all.
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $Query = "INSERT INTO $dbTable
    (pageID,userID,status)
    VALUES
    ('$pid','$sid','new')";

    //$insertQuery = "INSERT INTO Json_Messages (json) VALUES ('$data')";

    $rows = pg_query($pg_conn, $checkStatusQuery );
    $fielddata = "";

    if (!pg_num_rows($result)) {
        $fielddata = "";
        //Add intial Data
    } else {
        //print "Json Messages in your database:\n";
        //while ($row = pg_fetch_row($result)) { print("- $row[0]\n"); }
        while ($row = pg_fetch_row($rows)) {
            //$row = pg_fetch_row($result)
            $fielddata  = $row[0];
        }
    }
    return $fielddata;
  }



        //====================================================================================================

         if (isset($reply) && $reply != '') {

                    $token = $_ENV["techware_fb_token"];
/*
                    $typing_on = array(
                        'recipient' => array('id'=> $sid),
                        'sender_action' => 'typing_on'
                    );

  */

/*
                    $data = array(
                        'recipient' => array('id'=> $sid),
                        'message' => array('text'=> $reply)
                    );
*/
                    $options = array(
                        'http' => array(
                        'method' => 'POST',
                        'content' => $reply,
                        'header' => "Content-Type: application/json\n"
                    ));
                    $context = stream_context_create($options);
                    file_put_contents("php://stderr", "FB Context: = ".$context.PHP_EOL);
                    //$fbreply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token", false, $context);
                    $fbreply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token", false, $context);

                    file_put_contents("php://stderr", "FB reply: = ".$fbreply.PHP_EOL);

                }else{
    file_put_contents("php://stderr", "No reply!!! ".PHP_EOL);
                }
    } catch (Exception $e) {
        // Handle exception
        file_put_contents("php://stderr", "ERROR!!: = ".$e->getMessage().PHP_EOL);
    }












}
