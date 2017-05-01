<?php

header("HTTP/1.1 200 OK");

//webhook response
// For 4.3.0 <= PHP <= 5.4.0
/*
if (!function_exists('http_response_code'))
{
    function http_response_code($newcode = NULL)
    {
        static $code = 200;
        if($newcode !== NULL)
        {
            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
            if(!headers_sent())
                $code = $newcode;
        }
        return $code;
    }
}
*/
function KaziBot(){
try{
$user_details = file_get_contents("https://graph.facebook.com/v2.6/".$GLOBALS['sid']."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=".$GLOBALS['token']);
$user_details =  json_decode($user_details);
$GLOBALS['username'] = $user_details->first_name;
//@databaseCore $GLOBALS['pg_conn'] = pg_connect(pg_connection_string_from_database_url());
setReplys();

//Payload processing
addField("fbjsondata",$datastream);
if (isset($GLOBALS['locationGeoLat']) && $GLOBALS['locationGeoLat'] != '' && isset($GLOBALS['locationGeoLong']) && $GLOBALS['locationGeoLong'] != '') {
    //GET LOCATION FROM GOOGLE
    $GLOBALS['geoLoc'] = $GLOBALS['locationGeoLat'].",".$GLOBALS['locationGeoLong'];
    $cityCountry = GetCityCountry($GLOBALS['geoLoc']);
    if($cityCountry != 'false'){
        if(getField('isfindlocation')=='YES' || getField('findgeolocation')==''){
            addField('findgeolocation',$GLOBALS['geoLoc']);
            addField('findlocation',$cityCountry);
            if(getField('isfindlocation')=='YES'){
                sendMessage(basicReply("Hi ".$GLOBALS['username'].",\nYour current location has been set. I can now help you find places around that location.\nJust type the command  find [place]. (e.g.find hospital,find hotel or even find 5 star hotel).\nSee the help menu for more commands."));
                addField('isfindlocation','NO');
                exit("");
            }else{
                addField('geolocation',$GLOBALS['geoLoc'] );
                addField($myStatus,$cityCountry);
            }
            addField('isfindlocation','NO');
            //sendMessage(basicReply(""));
        }else{
            addField('geolocation',$GLOBALS['geoLoc'] );
            addField($myStatus,$cityCountry);
        }
        $GLOBALS['message'] = $cityCountry;
        $GLOBALS['payload'] = null;
        $GLOBALS['quickReply'] = null;
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
            sendMessage(basicReply( "Help Info: This app will help you find a job or post a job opening for other users to apply.\n\nI can also help you find places in a locations.\n\nI can also get you directions from one place to another."));
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
        addField('isfindlocation','NO');
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
        }elseif($payldPara[0]=='get'){
            sendMessage(basicReply(getReply('get directions')));
        }elseif($payldPara[0]=='find'){
            logx('{FINDING....}');
            if ($payldPara[1]=='location') {
                addField('isfindlocation','YES');
                sendMessage($GLOBALS['find_location']);
            }elseif ($payldPara[1]=='place') {
                $myLoc = getField('findlocation');
                if($myLoc==''){
                    sendMessage($GLOBALS['find_location_place']);
                }else{
                    sendMessage(basicReply(getReply('find places')));
                }
            }

            logx($GLOBALS['smsg']);
            logMSG($GLOBALS['log']);

        }elseif($payldPara[0]=='toggle'){
            //if(getField('userType')=="Find-Job"){
                if(getField('isNotification')=="YES"){
                    addField('isNotification',"NO");
                    sendMessage(basicReply("Daily notifications have been 🔕 disabled."));
                }else{
                    addField('isNotification',"YES");
                    sendMessage(basicReply("Daily notifications have been 🔔 enabled."));
                }
            //}else{
            //    sendMessage(basicReply("Only users finding jobs can toggle daily notifications."));
            //}
        }elseif($payldPara[0]=='view'){
            sendReply('info');
        }elseif($payldPara[0]=='help'){
            if ($payldPara[1]=='find-job') {
                sendMessage(basicReply(getReply('about find job')));
            }elseif ($payldPara[1]=='post-job') {
                sendMessage(basicReply(getReply('about post job')));
            }elseif ($payldPara[1]=='find') {
                sendMessage(basicReply(getReply('about find place')));
            }
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
        }elseif($payldPara[0]=='photo'){
            $photoref=  $GLOBALS['payload'];
            $photoref = str_replace($payldPara[0].'_','', $photoref);
             getPhoto($photoref);
             sendGoogleMessage($GLOBALS['status_places_photo']);
            //"payload":"photo_'.payloadFix($photoref).'"
        }elseif($payldPara[0]=='directions'){
            $dirURL= $GLOBALS['payload'];
            $dirURL = str_replace($payldPara[0].'_','', $dirURL);
            getURLDirection($dirURL);
            sendGoogleMessage($GLOBALS['status_places_directions']);
        }elseif($payldPara[0]=='instructions'){
            $dirURL= $GLOBALS['payload'];
            $dirURL = str_replace($payldPara[0].'_','', $dirURL);
            getURLDirectionSteps($dirURL);
            foreach ($GLOBALS['status_places_instructions'] as $steps) {
                sendGoogleMessage($steps);
            }
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

            if(getField('isfindlocation')=='YES'){
                addField('findlocation',$GLOBALS['message']);
                $geodata = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.getField('findlocation'));
                $jsondata=json_decode($geodata);
                //$google_places->location = array($jsondata->results[0]->geometry->location->lat,$jsondata->results[0]->geometry->location->lng);
                $geocodestr = $jsondata->results[0]->geometry->location->lat.",".$jsondata->results[0]->geometry->location->lng;
                addField('findgeolocation',$geocodestr);
                addField('isfindlocation','NO');
                sendReply(getField('status'));
                //exit("");
            }

            logx("{IS MESSAGE}".$GLOBALS['message']);
            if(strpos(strtolower($GLOBALS['message']),'find')===0){
                $place = $GLOBALS['message'];
                //$place = trim(str_replace('find', '', $place));
                //TODO:
                //sdfsdf jojo
                logx('{FINDING....}');
                logx($GLOBALS['message']);
                if(findPlace($place)){
                    logx("{PLACES REPLY}".$GLOBALS['status_places']);
                    sendGoogleMessage($GLOBALS['status_places']);
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
            if((strpos(strtolower($GLOBALS['message']),'directions')===0) || (strpos(strtolower($GLOBALS['message']),'get directions')===0)){
                $place = $GLOBALS['message'];
                //$place = trim(str_replace('find', '', $place));
                //TODO:
                //sdfsdf jojo
                logx('{DIRECTIONS....}');
                logx($GLOBALS['message']);
                if(getDirections($place)){
                    logx("{DIRECTIONS REPLY}".$GLOBALS['status_places_directions']);
                    sendMessage($GLOBALS['status_places_directions']);
                }else{
                    sendMessage($GLOBALS['status_places_directions']);
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

logx("Waiting for user reply");
//}

logMSG($GLOBALS['log']);
} catch (Exception $e) {
logx("{TRY ERROR}".$e->getMessage());
// Handle exception
//file_put_contents("php://stderr", "ERROR!!: = ".$e->getMessage().PHP_EOL);
}
}

 ?>
