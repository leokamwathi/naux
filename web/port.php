<?php
//More to come
require_once 'GooglePlaces.php';
require_once 'GooglePlacesClient.php';

/*
THINGS TO add
-Call button  https://developers.facebook.com/docs/messenger-platform/send-api-reference/call-button
-Share button. https://developers.facebook.com/docs/messenger-platform/send-api-reference/share-button
-MENUS MAN WTH!!!!!! - SOCIAL NEWS!!!! https://developers.facebook.com/docs/messenger-platform/messenger-profile/persistent-menu
-dont do shenzhen until post job is done!!!!!

// => ✖ ✔️ 🆗 🔘 ❤ 🤖 📲 📞 📱

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
       //slibwslibd
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
            $GLOBALS['pid'] = $fb->entry[0]->id;
            $GLOBALS['sid'] = $fb->entry[0]->messaging[0]->sender->id;

            $GLOBALS['isTyping'] = '
                        {"recipient":{
                            "id":"'.$GLOBALS['sid'].'"
                        },
                        "sender_action":"typing_on"
                        }';
            sendMessage($GLOBALS['isTyping']);

            // get message
            $GLOBALS['message'] = $fb->entry[0]->messaging[0]->message->text;
            //get payload
            $GLOBALS['quickReply'] = $fb->entry[0]->messaging[0]->message->quick_reply->payload;
            $GLOBALS['payload'] = $fb->entry[0]->messaging[0]->postback->payload;
            $GLOBALS['locationGeoLat'] = $fb->entry[0]->messaging[0]->message->attachments[0]->payload->coordinates->lat;
            $GLOBALS['locationGeoLong'] = $fb->entry[0]->messaging[0]->message->attachments[0]->payload->coordinates->long;
            $GLOBALS['locationTitle'] = $fb->entry[0]->messaging[0]->message->attachments[0]->title;
            /*
            {
                "object":"page",
                "entry":[
                    {
                        "id":"1292677864114230",
                        "time":1492906358117,
                        "messaging":[
                            {
                                "recipient":{
                                    "id":"1292677864114230"
                                },
                                "timestamp":1492906358117,
                                "sender":{
                                    "id":"1360046804041611"
                                },
                                "postback":{
                                    "payload":"getting_started"
                                }
                            }
                        ]
                    }
                ]
            }
            */
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

                addField("fbjsondata",$datastream);

            if (isset($GLOBALS['locationGeoLat']) && $GLOBALS['locationGeoLat'] != '' && isset($GLOBALS['locationGeoLong']) && $GLOBALS['locationGeoLong'] != '') {
                //GET LOCATION FROM GOOGLE
                $GLOBALS['geoLoc'] = $GLOBALS['locationGeoLat'].",".$GLOBALS['locationGeoLong'];
                addField('geolocation',$GLOBALS['geoLoc'] );
                $cityCountry = GetCityCountry($GLOBALS['geoLoc']);
                if($cityCountry != 'false'){
                    addField($myStatus,$cityCountry);
                    $GLOBALS['message'] = $cityCountry;
                    $GLOBALS['payload'] = null;
                }
            }

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

            if (isNewUser()) {
                logx("{NEW USER..CREATING USER}");
                if(addNewUser()){
                    if(strtolower(trim($GLOBALS['message']))=='hello kazi'){
                        sendMessage(basicReply( "Hello ".$GLOBALS['username']."," ));
                    }elseif(strtolower(trim($GLOBALS['message']))=='hi kazi'){
                        sendMessage(basicReply( "Hi ".$GLOBALS['username']."," ));
                    }elseif(strtolower(trim($GLOBALS['message']))=='help me'){
                        sendMessage(basicReply( "Help Info: This app will help you find a job or post a job opening for other users to apply."));
                    }else{
                        sendReply('userType');
                    }
                }else{
                    logx("{FAILED TO CREATE USER}");
                    //sendReply('new'); #failed to add user.. really what to do????
                }
            } else {
                logx("{CURRENT STATUS}".getField('status'));
                logx("{READING REPLY....}".$GLOBALS['message']);
                if (isset($GLOBALS['payload']) && $GLOBALS['payload'] != '') {
                    logx("{ISPAYLOAD}=>".$GLOBALS['payload']);
                    //job_findjob , qualification_collage-diploma
                    $payldPara = explode("_", $GLOBALS['payload']);
                    if($payldPara[0]=='getting'){
                        if (isNewUser()) {
                            logx("{NEW USER..CREATING USER}");
                            if(addNewUser()){
                                sendReply('userType');
                            }else{
                                logx("{FAILED TO CREATE USER}");
                                //sendReply('new'); #failed to add user.. really what to do????
                            }
                        }else{
                            sendReply(getField('status'));
                        }

                    }elseif($payldPara[0]=='find'){
                        //search_job2jobs
                        //search_job2
                        //TODO: SEARCH - DONE
                        logx('{FINDING....}');
                        logx($GLOBALS['payload']);
                        if(findPlace($place)){
                        sendMessage($GLOBALS['status_places']);
                    }else{

                    }

                        //sendMessage($GLOBALS["status_".$GLOBALS['payload']]);
                        logx($GLOBALS['smsg']);
                        logMSG($GLOBALS['log']);
                        //=======================================//
                        sendReply(getField('status'));
                        //sendReply($payldPara[0]);
                    }elseif($payldPara[0]=='search'){
                        //search_job2jobs
                        //search_job2
                        //TODO: SEARCH - DONE
                        logx('{SEARCHING....}');
                        logx($GLOBALS['payload']);
                        searchJobs(0);
                        sendMessage($GLOBALS['status_search_results']);
                        //sendMessage($GLOBALS["status_".$GLOBALS['payload']]);
                        logx($GLOBALS['smsg']);
                        logMSG($GLOBALS['log']);
                        //=======================================//
                        sendReply(getField('status'));
                        //sendReply($payldPara[0]);
                    }elseif($payldPara[0]=='delete'){
                        logx('{DELETING....}');
                        logx($GLOBALS['payload']);
                        if($payldPara[1]=='profile'){
                            logx('{DELETE CONFIRMATION....}');
                            sendMessage($GLOBALS['status_delete']);
                        }elseif($payldPara[1]=='yes'){
                            logx('{YES DELETE....}');
                            if(deleteprofile()){
                                sendMessage(basicReply("Your Profile has been deleted.\nThank you, I hope I was able to help you out."));
                            }else{
                                sendMessage(basicReply("Your Profile was not deleted. Something went wrong. :-(\nPlease try again later."));
                                sendReply(getField('status'));
                            }
                        }elseif($payldPara[1]=='no'){
                            logx('{NO DELETE....}');
                            sendReply(getField('status'));
                        }
                        //sendMessage($GLOBALS["status_".$GLOBALS['payload']]);
                        logx($GLOBALS['smsg']);
                        logMSG($GLOBALS['log']);
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
                        logx("{IS MESSAGE}".$GLOBALS['message']);
                        if(strpos($GLOBALS['message'],'find')===0){
                            $place = $GLOBALS['message'];
                            $place = trim(str_replace('find', '', $place));
                            //TODO:
                            //sdfsdf jojo
                            logx('{FINDING....}');
                            logx($GLOBALS['message']);
                            if(findPlace($place)){
                                logx("{PLACES REPLY}".$GLOBALS['status_places']);
                                sendMessage($GLOBALS['status_places']);
                            }else{
                                sendMessage($GLOBALS['status_places']);
                                //$GLOBALS['status_places']
                                //sendMessage(basicReply( "Hi ".$GLOBALS['username'].", I could not find any nearby locations that match [".$place."] either change your location or what you are looking for." ));
                            }
                            //sendMessage($GLOBALS['status_places']);
                            //sendMessage($GLOBALS["status_".$GLOBALS['payload']]);
                            logx($GLOBALS['smsg']);
                            logMSG($GLOBALS['log']);
                            //=======================================//
                            //sendReply(getField('status'));
                            exit("");
                        }
                        if(strtolower(trim($GLOBALS['message']))=='hello kazi'){
                            sendMessage(basicReply( "Hello ".$GLOBALS['username']."," ));
                        }elseif(strtolower(trim($GLOBALS['message']))=='hi kazi'){
                            sendMessage(basicReply( "Hi ".$GLOBALS['username']."," ));
                        }elseif(strtolower(trim($GLOBALS['message']))=='help me'){
                            sendMessage(basicReply( "Help Info: This app will help you find a job or post a job opening for other users to apply."));
                        }elseif(strtolower(trim($GLOBALS['message']))=='help'){
                        //if(strpos($GLOBALS['message'],'help')!=false){
                            sendMessage(basicReply( "Help Info ❤: This app will help you find a job or post a job opening for other users to apply."));
                            sendReply(getField('status'));

                            //if(strpos($GLOBALS['message'],'help')!=false){
                            //sendMessage(basicReply( $GLOBALS['message']." ".$GLOBALS['username']."," ));
                            sendReply(getField('status'));
                        }else{
                        if($GLOBALS['mid'] == getField('lastNotification') ){
                            logx("{SAME MESSAGE AGAIN REALLY SUCKS}".$GLOBALS['message']);
                        }else{
                        addField('lastNotification',$GLOBALS['mid']);

                        if(setStatus(getField('status'),$GLOBALS['message'])){
                            $myNextStatus = nextStatus(getField('status'));
                            logx("{NEXT STATUS}".$myNextStatus);
                            sendReply($myNextStatus);
                        }else{
                            //TODO: Sometimes Error Message might be send first
                            if (is_string(getField('status'))){
                                sendReply(getField('status'));
                            }else{
                                logx("{SO THIS HAPPENS}");
                                sendReply('userType');
                            }
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
logMSG($GLOBALS['log']);
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
        //does thi happen? I wonder
        //this breaks the system
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
        case "companyqualification":
            addField($paypara[0], $paypara[1]);
            $isSet = true;
            break;
        case "edit":
            logx("{EDIT PAYLOAD}");
            addField('status',$paypara[1]);
            addField('mode',$GLOBALS['payload']);
            //sendReply($paypara[1]);
            $isSet = false;
            break;
    }
    if ($paypara[0]!='edit' && $isSet == true){
        logx("{SET PAYLOAD}=>".$paypara[0]." - ".$paypara[1]);
        //setMode();
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
/*
        if (isset($GLOBALS['locationTitle']) && $GLOBALS['locationTitle'] != ''&&isset($GLOBALS['locationGeoLat']) && $GLOBALS['locationGeoLat'] != ''&&isset($GLOBALS['locationGeoLong']) && $GLOBALS['locationGeoLong'] != '') {
    //GET LOCATION FROM GOOGLE
            $GLOBALS['geoLoc'] = $GLOBALS['locationGeoLat'].",".$GLOBALS['locationGeoLong'];
            addField('geolocation',$GLOBALS['geoLoc'] );
            $cityCountry = GetCityCountry($GLOBALS['geoLoc']);
            if($cityCountry != 'false'){
                addField($myStatus,$cityCountry);
                $isSet = true;
                break;
            }else{
                $isSet = false;
                break;
            }
        }else{
        //dsdsd
        */
        //TODO: Get from geolocation
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
            if (!filter_var($myMessage, FILTER_VALIDATE_EMAIL) === false) {
                addField($myStatus, $myMessage);
                $isSet = true;
                break;
            } else {
                sendMessage(basicReply( "(".$myMessage.") is not a valid email. Please enter a valid email address."));
                $isSet = false;
                break;
            }


        case "companyphone":
            //TODO:test valid phone
            $phone = $myMessage;
            $plus = "";
            if(strpos($phone,'+')===0){
                $plus = "+";
            }
                $phone = preg_replace('/\s+/', '', $phone);
                $phone = str_replace('+', '', $phone);
                $phone = str_replace(' ', '', $phone);
                $phone = str_replace('#', '', $phone);
                $phone = str_replace('.', '', $phone);
                $phone = str_replace('-', '', $phone);
                $phone = str_replace('*', '', $phone);
                $phone = str_replace('(', '', $phone);
                $phone = str_replace(')', '', $phone);
                $phone = preg_replace("/[^0-9,.]/", "", $phone );
                $phone = preg_replace('~\D~', '', $phone);

                if (ctype_digit ($phone) && strlen($phone) < 20 && strlen($phone) > 3){
                    addField($myStatus, $plus.$phone);
                    $isSet = true;
                    break;
                }else{
                    sendMessage(basicReply( "(".$myMessage.") is not a valid phone number. Please enter a valid phone number (+254 723456789 , 0723456789 , 020 123456)."));
                    $isSet = false;
                    break;
                }
        case "companywebsite":
            //TODO:test valid website
            if (!filter_var($myMessage, FILTER_VALIDATE_URL) === false) {
                addField($myStatus, $myMessage);
                $isSet = true;
                break;
            } else {
                sendMessage(basicReply( "(".$myMessage.") is not a valid website. Please enter a valid website address."));
                $isSet = false;
                break;
            }
        case "companyjobtime":
            //TODO:test valid day (Number)
            if (!filter_var($myMessage, FILTER_VALIDATE_INT) === false) {
                if ($myMessage > 30 || $myMessage < 1){
                    sendMessage(basicReply( "Please enter a number between 1 and 30."));
                    $isSet = false;
                    break;
                }
                addField($myStatus, $myMessage);
                $isSet = true;
                break;
            } else {
                sendMessage(basicReply( "(".$myMessage.") is not a valid number of days. Please enter a number of days."));
                $isSet = false;
                break;
            }
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
    //        return("companydescription");
    //    case "companydescription":
            return("companyjob");
        case "companyjob":
            return("companylocation");
        case "companylocation":
            return("companyexperience");
        case "companyexperience":
            return("companyqualification");
        case "companyqualification":
//            return("companywebsite");
//        case "companywebsite":
//            return("companyemail");
//        case "companyemail":
            return("companyphone");
        case "companyphone":
        //    return("companyjobtime");
        //case "companyjobtime":
            return("companyinfo");
        default:
            logx("{SETTING DEFAULT STATUS}");
            return("userType");
    }
}

function GetCityCountry($geoLoc){
try{
//$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$geoLoc."&sensor=true&key=".$_ENV["google_maps_key"];
//TODO:make this work with the googlemap key. ^^^^^^ see above link
$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$geoLoc."&sensor=true";
    //google_maps_key

    $data = @file_get_contents($url);
    $jsondata = json_decode($data,true);
  if(is_array($jsondata) && $jsondata['status'] == "OK")
    {
	$location = array();
	$results = $jsondata['results']['0'];
	//print_r($results);
  foreach ($results['address_components'] as $component) {
    switch ($component['types']) {
      case in_array('street_number', $component['types']):
        $location['street_number'] = $component['long_name'];
        break;
      case in_array('route', $component['types']):
        $location['street'] = $component['long_name'];
        break;
      case in_array('sublocality', $component['types']):
        $location['sublocality'] = $component['long_name'];
        break;
      case in_array('locality', $component['types']):
        $location['locality'] = $component['long_name'];
        break;
      case in_array('administrative_area_level_2', $component['types']):
        $location['admin_2'] = $component['long_name'];
        break;
      case in_array('administrative_area_level_1', $component['types']):
        $location['admin_1'] = $component['long_name'];
        break;
      case in_array('postal_code', $component['types']):
        $location['postal_code'] = $component['long_name'];
        break;
      case in_array('country', $component['types']):
        $location['country'] = $component['long_name'];
        break;
    }
	}

	 $city = "";
  $country = $location['country'];

  if($location['locality'] != ''){
  $city = $location['locality'];
  }else if($location['sublocality'] != ''){
    $city = $location['sublocality'];
  }else if($location['admin_1'] != ''){
    $city = $location['admin_1'];
  }else if($location['street'] != ''){
    $city = $location['street'];
  }else{
   $city = $location['country'];
  }
 return($city.",".$country);
//print_r( $location);
  }else{
  return("false");
  }
} catch (Exception $e) {
 return("false");
}
}

function isStr($str)
{
     return(isset($GLOBALS['message']) && $GLOBALS['message'] != '');
}

function findPlace($find){



    $google_places = new joshtronic\GooglePlaces('AIzaSyCICsrT6NnZb0JkS_bJdNRVHx-jtIsog6Q');

    //get geocode or reverse code
    $geocodestr = getField('geolocation');

    if(isset($geocodestr) && $geocodestr != ''){
        $geoarg = explode(',', $geocodestr);
        $google_places->location = array($geoarg[0],$geoarg [1]);
        $geolog= $geolog.'{GEOCODING LOC SET} '.$geocodestr.$find;
    }else{
        $geocodestr = getField('location');
        if(isset($geocodestr) && $geocodestr != ''){
            $geodata = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$geocodestr);
            $jsondata=json_decode($geodata);
            $google_places->location = array($jsondata->results[0]->geometry->location->lat,$jsondata->results[0]->geometry->location->lng);
                $geolog= $geolog.'{GEOCODING REVERSE SET} '.$geocodestr.$find;
        }else{
                $geolog= $geolog.'{GEOCODING ERROR} '.$geocodestr.$find;
        }
    }

    $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find." at ".$geocodestr);

    //$google_places->location = array($lat,$lng);



    $google_places->rankby   = 'distance';
    $google_places->types    = $find; // Requires keyword, name or types
    $results                 = $google_places->nearbySearch();

    $jsondata = json_decode($results );

      if($jsondata->status == "OK" && isset($geocodestr) && $geocodestr != '')
        {


            $GLOBALS['status_places'] =
            '{"recipient": {
            "id": "' . $GLOBALS['sid'] . '"
            },
            "message": {
            "attachment": {
                "type": "template",
                "payload": {
                    "template_type": "generic","elements": [';
           $count = 0;
    	   foreach ($jsondata->results as $component) {
    		$geolatx = $component->geometry->location->lat.",".$component->geometry->location->lng;
    		//markers=icon:https://maps.gstatic.com/mapfiles/place_api/icons/school-64.png%7Cshadow:true
    		$imgurl="https://maps.googleapis.com/maps/api/staticmap?center=".$geolatx."&size=500x260&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo".$marker="&markers=".$geolatx;

            $element = '
           {
               "title": "'.$component->name.'",
               "subtitle": "'.$component->vicinity.'",
               "image_url": "'.$imgurl.'",
               "buttons": [
                   {
                       "type":"element_share"
                   }
               ]
           }';
           if($count == 0){
               $hasRows = true;
               $GLOBALS['status_places'] = $GLOBALS['status_places'].$element;
           }else{
               $GLOBALS['status_places'] = $GLOBALS['status_places'].",".$element;
           }
           $count = $count + 1 ;
    	}
            $GLOBALS['status_places'] = $GLOBALS['status_places'].']}}}}';
            if($count == 0){
                $geolog= $geolog.'{ZERO COUNT-DO TEXT SEARCH}'.$geocodestr.$find;
                $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find.$geolog);
                   return false;
            }else{
                return true;
            }
    	}else{
            $geolog= $geolog.'{STATUS NOT OK} = [[['.$jsondata->status."]]]".$geocodestr.$find;
            $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find.$geolog);
    	    return false;
    	}
    }





function searchJobs($page)
{
$hasRows = false;
$GLOBALS['status_search_results'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any jobs matching your requirements (Please review your profile or try again later.)');
    if(!(is_numeric($page) && $page > 0)){
        $page = 0;
    }

//$GLOBALS['sid'] = "1360046804041611";
//$GLOBALS["pid"] = "1292677864114230";

	$searchJobQuery = "";
    $searchQualQuery= "";
    $searchExpQuery = "";
    $searchlocQuery = "";

    $searchJobQuery = " AND LOWER(companyjob) = '".getField('job')."' ";
    $searchQualQuery = " AND ". getSearchQualification(getField('qualification'));
    $searchExpQuery = " AND ". getSearchExperience(getField('experience'));
    $searchlocQuery = " AND LOWER(companylocation)= '".getField('location')."'";

	 $searchQuery = strtolower($searchJobQuery.$searchQualQuery.$searchExpQuery.$searchlocQuery);
//
$Query     = "SELECT * from ".$GLOBALS['dbTable']." where usertype = 'Post-Job' ".$searchQuery;

	//print_r($Query);

    $results      = pg_query($GLOBALS['pg_conn'], $Query);

    if(!$results){
        logx(pg_result_error($results));
        //$GLOBALS['status_search_results'] = basicReply('Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)');
    }else{
    if (!pg_num_rows($results)) {
        //no rows = no data
        //$GLOBALS['status_search_results'] = basicReply('Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)');
    } else {
        //Head
        $GLOBALS['status_search_results'] =
        '{"recipient": {
        "id": "' . $GLOBALS['sid'] . '"
        },
        "message": {
        "attachment": {
            "type": "template",
            "payload": {
                "template_type": "generic","elements": [';

				$count = 0;

				$rows = pg_fetch_all($results);

				foreach ($rows as $row) {
				if($count==11){
					break;
				}

					//print_r($count." - ".$row['companyname']." - ".$row['companylocation']." - ".$row['companyjob']." - ".$row['companyqualification']." - ".$row['companyexperience']."\n");
					/*
					$count = $count + 1;
					*/


                    $geolocation = $row['geolocation'];

					if(isset($geolocation) && $geolocation != ''){
					$imgurl="https://maps.googleapis.com/maps/api/staticmap?center=".$geolocation."&size=500x260&markers=".$geolocation."&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo";
					}else{
						$geolocation = $row['companylocation'];
						if(isset($geolocation) && $geolocation != ''){
						$imgurl="https://maps.googleapis.com/maps/api/staticmap?center=".$geolocation."&size=500x260&markers=".$geolocation."&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo";
						}else{
							$geolocation = getField('location');
							$imgurl="https://maps.googleapis.com/maps/api/staticmap?center=".$geolocation."&size=500x260&markers=".$geolocation."&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo";
						}
					}

					 $element = '
                    {
                        "title": "'.$row['companyname'].'",
                        "subtitle": "Job:'.$row['companyjob'].'|Loc:'.$row['companylocation'].'|Exp:'.$row['companyexperience'].'|Qualification:'.$row['companyqualification'].' ",
                        "image_url": "'.$imgurl.'",
                        "buttons": [
                            {
                                  "type":"phone_number",
                                  "title":"Call '.$row['companyname'].'",
                                  "payload":"'.$row["companyphone"].'"
                            },
                            {
                                "type":"element_share"
                            }
                        ]
                    }';
					if($count == 0){
						$hasRows = true;
						$GLOBALS['status_search_results'] = $GLOBALS['status_search_results'].$element;
					}else{
						$GLOBALS['status_search_results'] = $GLOBALS['status_search_results'].",".$element;
					}
                    $count = $count + 1 ;
				}

                $GLOBALS['status_search_results'] = $GLOBALS['status_search_results'].']}}}}';

    }
}
    return $hasRows;
}

function getSearchQualification($qualification)
{
    switch (strtolower($qualification)) {
        case "masters-degree":
            return ("(LOWER(companyqualification)= 'Self-Taught' OR LOWER(companyqualification)= 'Certificate' OR LOWER(companyqualification)= 'Collage-Diploma' OR LOWER(companyqualification)= 'university-degree' OR LOWER(companyqualification)=  'masters-degree')");
            break;
        case "university-degree":
            return ("(LOWER(companyqualification)= 'Self-Taught' OR LOWER(companyqualification)= 'Certificate' OR LOWER(companyqualification)= 'Collage-Diploma' OR LOWER(companyqualification)= 'university-degree')");
            break;
        case "collage-diploma":
            return ("(LOWER(companyqualification)= 'Self-Taught' OR LOWER(companyqualification)= 'Certificate' OR LOWER(companyqualification)= 'Collage-Diploma')");
            break;
        case "certificate":
            return ("(LOWER(companyqualification)= 'Self-Taught' OR LOWER(companyqualification)= 'Certificate')");
            break;
        case "self-taught":
            return ("(LOWER(companyqualification)=  'self-taught')");
            break;
        }
		return("false");
}

function getSearchExperience($experience){

switch (strtolower($experience)) {

    case "First-Job":
        return ("(LOWER(companyexperience) = 'none')");
        break;
    case "some":
         return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'Some' )");
        break;
    case "1-to-3-years":
        return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'Some' OR LOWER(companyexperience) = '1-year-and-over')");
        break;
    case "4-to-8-years":
       return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'Some' OR LOWER(companyexperience) = '1-year-and-over' OR LOWER(companyexperience) = '4-years-and-over')");
        break;
    case "9-years-and-over":
        return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'Some' OR LOWER(companyexperience) = '1-year-and-over' OR LOWER(companyexperience) = '4-years-and-over' OR LOWER(companyexperience)=  '9-years-and-over')");
        break;
}
    return("false");
}


function sendReply($status)
{
    setReplys();

    if($status == "info"){
        if(getField('usertype')=='Post-Job'){
            $status = 'companyinfo';
        }
    }

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
            $reply = $GLOBALS['status_qualification'];
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
        case "companyqualification":
            $reply = $GLOBALS['status_companyqualification'];
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
        case "companyjobtime":
            $reply = basicReply("Please enter the number of days you want to run the job posting. (30 days Maximum. You can extend the duration at any time.)");
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
            if(getField('usertype')=='Post-Job'){
                $status = 'companyinfo';
            }else{
                $status = 'info';
            }
            $reply = $GLOBALS['status_'.$status];
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
    logx("{REPLY JSON}see db");
    $tempjson = json_decode($GLOBALS['fbreply']);
    logx("{JSON ERROR}".json_last_error());
    if (json_last_error() != "JSON_ERROR_NONE") {
        logx("{FBREPLY - GOT ERROR}=>".trim(preg_replace('/\s+/', ' ', $GLOBALS['fbreply'])));
    }else{
        logx("{FBREPLY}see db");
    }
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
    addField('lastReplyJson',$msg);
    addField("fbreply",$GLOBALS['fbreply']);
    addField("mylog",$GLOBALS['log']);
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

function refreshDB()
{
    $totaladds = 3;
    $Query     = "SELECT * from ".$GLOBALS['dbTable'];
    for ($x = 0; $x < $totaladds; $x++) {
        $results      = pg_query($GLOBALS['pg_conn'], $Query);
        pg_free_result($results);
    }
}

function addField($field, $value)
{
    $value = addslashes($value);  // make it safe
    $Query="UPDATE ".$GLOBALS['dbTable']." SET ($field) = ('$value') where pageID ='".$GLOBALS['pid']."' and userID='".$GLOBALS['sid']."'";
    $rows  = pg_query($GLOBALS['pg_conn'], $Query);
    if(!$rows){
        logx(pg_result_error($rows));
        return false;
    }else{
        return true;
    }
}

function deleteprofile()
{
    logx("{DELETINGPROFILE-FUNCTION}");
    try{
    $Query     = "DELETE from ".$GLOBALS['dbTable']." where pageID ='".$GLOBALS["pid"]."' and userID='".$GLOBALS["sid"]."'";
    $rows      = pg_query($GLOBALS['pg_conn'], $Query);
    if(!$rows){
        logx(pg_result_error($rows));
        return false;
    }else{
        return true;
    }
    return false;
} catch (Exception $e) {
    logx("{DELETE ERROR}".$e->getMessage());
    return false;
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
    if($msg == $GLOBALS['log']){
        $GLOBALS['log']="";
    }
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
                    Your profile information.\n
                    Location:' . getField('location') . '\n
                    Job:' . getField('job') . '\n
                    Qualification:' . getField('qualification') . '\n
                    Experience:' . getField('experience') . '\n\n
                    I will send you daily notifications when I get job opennings matching your requirements",
                    "quick_replies":[
                        {
                            "content_type":"text",
                            "title":"Search Jobs",
                            "payload":"search_jobs"
                        },
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
                            "title":"Delete Profile",
                            "payload":"delete_profile"
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
                Welcome to the myKaziBot app. I am Kazibot. \n
                I can help you find a job or find job applicants for your job. \n
                What would you like to do?",
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

    $GLOBALS['status_qualification'] = '
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
    My is job. \n
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
//✖ ✔️
$GLOBALS['status_delete'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"Are you sure you want to delete your profile?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"Yes ✔️",
        "payload":"delete_yes"
    },
    {
        "content_type":"text",
        "title":"No ✖",
        "payload":"delete_no"
    }
]
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
status_companyqualification
*/

$GLOBALS['status_companyname'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is the name of the company or person posting the job."
}
}';

$GLOBALS['status_search_job'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "'.getField('companyname').'",
            "subtitle": "Job Description:- '.getField('companydescription').'\n Job:- '.getField('companyjob').'\n Location:- '.getField('companyLocation').'\n Experience:- '.getField('companyexperience').'\n Qualification:- '.getField('companyqualification').' ",
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
            }]
        }]
    }
}
}
}';
/*
,
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
}
*/
$GLOBALS['status_companyinfo'] = '
{"recipient":{
    "id":"'.$GLOBALS['sid'].'"
},
"message":{
    "text":"Welcome '.getField('companyname').', \n
    This is the information you have entered. \n
    Job applicants matching your requirements will be notified of your job posting.\n
                                                        \n
    Job:- '.getField('companyjob').'\n
    Location:- '.getField('companyLocation').'\n
    Experience:- '.getField('companyexperience').'\n
    Qualification:- '.getField('companyqualification').'\n
    Phone:- '.getField('companyphone').'\n",
    "quick_replies":[
        {
            "content_type":"text",
            "title": "Edit Name",
            "payload": "edit_companyname"
        },
        {
            "content_type":"text",
            "title": "Edit Job",
            "payload": "edit_companyjob"
        },
        {
            "content_type":"text",
            "title": "Edit Location",
            "payload": "edit_companylocation"
        },
        {
            "content_type":"text",
            "title": "Edit Experience",
            "payload": "edit_companyexperience"
        },
        {
            "content_type":"text",
            "title": "Edit Qualification",
            "payload": "edit_companyqualification"
        },
        {
            "content_type":"text",
            "title": "Delete Job Posting",
            "payload": "delete_profile"
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

$GLOBALS['status_companyqualification'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"What is the minimum qualification Level needed for the job?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"Self Taught",
        "payload":"companyqualification_Self-Taught"
    },
    {
        "content_type":"text",
        "title":"Certificate",
        "payload":"companyqualification_Certificate"
    },
    {
        "content_type":"text",
        "title":"Collage Diploma",
        "payload":"companyqualification_Collage-Diploma"
    },
    {
        "content_type":"text",
        "title":"University Degree",
        "payload":"companyqualification_University-Degree"
    },
    {
        "content_type":"text",
        "title":"Masters Degree",
        "payload":"companyqualification_Masters-Degree"
    }
]
}
}';




}
