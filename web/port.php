<?php


//====================================================================================================================//
/////////////////Messages

$status_new = '
"{recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Hi, Welcome to the job bot. I will help you find a job or find job applicants. What do you want to do?",
    "quick_replies":[
      {
        "content_type":"text",
        "title":"Find Job",
        "payload":"findJob"
      },
      {
        "content_type":"text",
        "title":"Post Job",
        "payload":"postJob"
      }
    ]
   }
  }';

$status_location = '{
  "recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Please enter your job location : (city,country) eg. Nairobi, Kenya or use your current location.",
    "quick_replies":[
      {
        "content_type":"location",
      }
    ]
  }
}';

$status_job = '
"{recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Hi, What kind of job are you looking for (Just one Job)? eg. Part time, Accountant, Web Designer,Chef, Sales Person, Programmer, House Help.",
   }
  }';

$status_exp = '
"{recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"How many years have you worked at this job?",
    "quick_replies":[
	   {
        "content_type":"text",
        "title":"First Time",
        "payload":"first"
      },
      {
        "content_type":"text",
        "title":"Under 1 year",
        "payload":"under_1"
      },
      {
        "content_type":"text",
        "title":"1 to 3 years",
        "payload":"1_to_3"
      },
      {
        "content_type":"text",
        "title":"4 to 8 years",
        "payload":"4_to_8"
      },
      {
        "content_type":"text",
        "title":"9 years and above",
        "payload":"Over_9"
      }
    ]
   }
  }';

$status_qualifications = '
"{recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"What is your job qualification Level?",
    "quick_replies":[
      {
        "content_type":"text",
        "title":"Self Taught",
        "payload":"selfTaught"
      },
      {
        "content_type":"text",
        "title":"Certificate",
        "payload":"certificate"
      },
      {
        "content_type":"text",
        "title":"Collage Diploma",
        "payload":"collage_diploma"
      },
      {
        "content_type":"text",
        "title":"University Degree",
        "payload":"university_degree"
      },
      {
        "content_type":"text",
        "title":"Masters Degree",
        "payload":"masters_degree"
      }
    ]
   }
  }';

$status_about = '
"{recipient":{
    "id":"'.$sid.'"
  },
  "message":{
    "text":"Hi, Tell us about yourself and the job you are looking for?",
   }
  }';

//====================================================================================================================//
//////Hub Challenge
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
    //================================================================================================================
    /////// Read Input set fb variables

    try {

        $data = file_get_contents("php://input");

        //$fb = json_decode($data);



        /// PAYLOAD Format
        ///{"object":"page","entry":[{"id":"763933067090623","time":1490364301410,"messaging":[{"recipient":{"id":"763933067090623"},"timestamp":1490364301410,"sender":{"id":"1486644564679609"},"postback":{"payload":"Payload"}}]}]}

        $fb = json_decode($data);

        $pid = $fb->entry[0]->id;
        $sid = $fb->entry[0]->messaging[0]->sender->id;
        $message = $fb->entry[0]->messaging[0]->message->text;
        $payload = $fb->entry[0]->messaging[0]->postback->payload;

        if (isset($message) && $message != '') {
          if (isset($payload) && $payload != '') {
            $message = "Payload ".$payload;
          }
        }
        //===================================================================================================
        // database connections
        /*
        function pg_connection_string_from_database_url() {
            extract(parse_url($_ENV["DATABASE_URL"]));
            return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
        }
        # Here we establish the connection. Yes, that's all.
        $pg_conn = pg_connect(pg_connection_string_from_database_url());

        $checkStatusQuery = "SELECT * from jobsDBtest where pageID ='$pid' and userID='$sid'";

        //$insertQuery = "INSERT INTO Json_Messages (json) VALUES ('$data')";

        $status = pg_query($pg_conn, $checkStatusQuery );
        $isNewUser = true;

        if (!pg_num_rows($result)) {
            $status = "New";
            //Add intial Data
        } else {
            //print "Json Messages in your database:\n";
            //while ($row = pg_fetch_row($result)) { print("- $row[0]\n"); }
            while ($row = pg_fetch_row($result)) {
                //$row = pg_fetch_row($result)
                $status = $row['status'];
            }
        }
        */
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
                $reply = $status_qualification;
                break;
            case "about":
                $reply = $status_about;
                break;
            default:
                $reply = $message;
                }

        //====================================================================================================

         if (isset($reply) && $reply != '') {

                    $token = "EAAN5JK8Gx7sBAGCZB5YulfJl4eoUCXGZABOm1oGRFH4kHubnxeANv8ZCVRQymrxqm0BEpzdULKWKhaBi5qXSbxZBrWhKud2U3ZAsBi1e8y3xCuKUMz9UF5XWRM8O9moGoIidAsUyCr3FLKjlXd0Q2WC70x6vmIZBwajPKXbxKU7AZDZD";
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
                    )

                    );
                    $context = stream_context_create($options);

                    $reply = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token", false, $context);
                    file_put_contents("php://stderr", "FB reply: = ".$reply.PHP_EOL);

                }
    } catch (Exception $e) {
        // Handle exception
        file_put_contents("php://stderr", "ERROR: = ".$e->getMessage().PHP_EOL);
    }












}
