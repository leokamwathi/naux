<?php

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
        if(getField('isfindlocation')=='YES' || (getField('findgeolocation')=='' && getField('findlocation')=='')){
            addField('findlocation',paypara[1]);
            addField('isfindlocation','NO');
        }
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

    if(getField('isfindlocation')=='YES' || (getField('findgeolocation')=='' && getField('findlocation')=='')){
        addField('findlocation',$GLOBALS['message']);
        $geodata = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.getField('findlocation'));
        $jsondata=json_decode($geodata);
        //$google_places->location = array($jsondata->results[0]->geometry->location->lat,$jsondata->results[0]->geometry->location->lng);
        $geocodestr = $jsondata->results[0]->geometry->location->lat.",".$jsondata->results[0]->geometry->location->lng;
        addField('findgeolocation',$geocodestr);
        addField('geolocation',$geocodestr);
        addField('isfindlocation','NO');
        sendReply(getField('status'));
        //exit("");
    }

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
        sendMessage(basicReply(getReply('email error')));
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
        sendMessage(basicReply(getReply('phone error')));
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


        function isStr($str)
        {
            return(isset($GLOBALS['message']) && $GLOBALS['message'] != '');
        }

        function searchJobs($page)
        {
            $hasRows = false;
            $GLOBALS['status_search_results'] = basicReply(getReply('no jobs found error'));
            if(!(is_numeric($page) && $page > 0)){
                $page = 0;
            }

            //$GLOBALS['sid'] = "1360046804041611";
            //$GLOBALS["pid"] = "1292677864114230";

            $searchJobQuery = "";
            $searchQualQuery= "";
            $searchExpQuery = "";
            $searchlocQuery = "";

            $searchJobQuery = " AND LOWER(companyjob) = '".strtolower(getField('job'))."' ";
            $searchQualQuery = " AND ". getSearchQualification(getField('qualification'));
            $searchExpQuery = " AND ". getSearchExperience(getField('experience'));
            $searchlocQuery = " AND LOWER(companylocation)= '".strtolower(getField('location'))."'";

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
                                    if($count==8){
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
                                    if(isset($row["companyphone"]) && $row["companyphone"] !=''){
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
                                    }
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
                            return ("(LOWER(companyqualification)= 'self-taught' OR LOWER(companyqualification)= 'certificate' OR LOWER(companyqualification)= 'collage-diploma' OR LOWER(companyqualification)= 'university-degree' OR LOWER(companyqualification)=  'masters-degree')");
                            break;
                            case "university-degree":
                            return ("(LOWER(companyqualification)= 'self-taught' OR LOWER(companyqualification)= 'certificate' OR LOWER(companyqualification)= 'collage-diploma' OR LOWER(companyqualification)= 'university-degree')");
                            break;
                            case "collage-diploma":
                            return ("(LOWER(companyqualification)= 'self-taught' OR LOWER(companyqualification)= 'certificate' OR LOWER(companyqualification)= 'collage-diploma')");
                            break;
                            case "certificate":
                                return ("(LOWER(companyqualification)= 'self-taught' OR LOWER(companyqualification)= 'certificate')");
                                break;
                                case "self-taught":
                                return ("(LOWER(companyqualification)=  'self-taught')");
                                break;
                            }
                            return("companyqualification !=  ''");
                        }

                        function getSearchExperience($experience){

                            switch (strtolower($experience)) {

                                case "first-job":
                                return ("(LOWER(companyexperience) = 'none')");
                                break;
                                case "some":
                                return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'some' )");
                                break;
                                case "1-to-3-years":
                                return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'some' OR LOWER(companyexperience) = '1-year-and-over')");
                                break;
                                case "4-to-8-years":
                                return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'some' OR LOWER(companyexperience) = '1-year-and-over' OR LOWER(companyexperience) = '4-years-and-over')");
                                break;
                                case "9-years-and-over":
                                return ("(LOWER(companyexperience) = 'none' OR LOWER(companyexperience) = 'some' OR LOWER(companyexperience) = '1-year-and-over' OR LOWER(companyexperience) = '4-years-and-over' OR LOWER(companyexperience)=  '9-years-and-over')");
                                break;
                            }
                            return("companyexperience != ''");
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
                            function sendJson($msg){
                                $GLOBALS['smsg'] = $msg;
                                //$msg = trim(preg_replace('/\s+/', ' ', $msg));
                                //$msg  = str_replace('-', ' ', trim($msg));
                                //$msg  = str_replace("'", '', trim($msg));
                                //$msg = jsonFixer($msg);
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

                                $marco = json_decode($GLOBALS['fbreply']);

                                if (json_last_error() != "JSON_ERROR_NONE") {
                                    sendMessage($GLOBALS['smsg']);
                                }else{
                                    addField('lastReplyJson',$msg);
                                    addField("fbreply",$GLOBALS['fbreply']);
                                    addField("mylog",$GLOBALS['log']);
                                }
                            }
                            function sendMessage($msg){
                                $GLOBALS['smsg'] = $msg;
                                $msg = trim(preg_replace('/\s+/', ' ', $msg));
                                $msg  = str_replace('-', ' ', trim($msg));
                                $msg  = str_replace("'", '', trim($msg));
                                //$msg = jsonFixer($msg);
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

                                $marco = json_decode($GLOBALS['fbreply']);

                                if (json_last_error() != "JSON_ERROR_NONE") {
                                    sendGoogleMessage($GLOBALS['smsg']);
                                }else{
                                    addField('lastReplyJson',$msg);
                                    addField("fbreply",$GLOBALS['fbreply']);
                                    addField("mylog",$GLOBALS['log']);
                                }
                            }

                            function sendGoogleMessage($msg){
                                $GLOBALS['smsg'] = $msg;
                                $msg = trim(preg_replace('/\s+/', ' ', $msg));
                                $msg  = str_replace("\n", "?*?#*^n", $msg);
                                $msg  = str_replace("\\","\\\\",$msg );
                                //$msg  = addslashes($msg );
                                //$msg  = str_replace("\\\\","\\\\\\",$msg );
                            	//$msg  = str_replace("\\", "\\\\\\", $msg);
                                //$msg  = str_replace("\\\\", "\\\\\\", $msg);
                                //$msg  = str_replace("\\\\\\\\", "\\\\\\", $msg);
                                $msg  = str_replace("?*?#*^n", "\n", $msg);
                                //$msg = jsonFixer($msg);
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

                                $marco = json_decode($GLOBALS['fbreply']);

                                if (json_last_error() != "JSON_ERROR_NONE") {
                                    $msg = $GLOBALS['smsg'];
                                    $options = array(
                                        'http' => array(
                                            'method' => 'POST',
                                            'content' => $msg,
                                            'header' => "Content-Type: application/json\n"
                                        )
                                    );
                                    $context = stream_context_create($options);

                                    $GLOBALS['fbreply'] = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=".$GLOBALS['token'], false, $context);

                                    $marco = json_decode($GLOBALS['fbreply']);

                                    if (json_last_error() != "JSON_ERROR_NONE") {
                                        logx("{FB REPLY ERROR!!!!! MSG NOT SEND}");
                                        sendMessage(basicReply(getReply('send error')));
                                    }
                                }

                                addField('lastReplyJson',$msg);
                                addField("fbreply",$GLOBALS['fbreply']);
                                addField("mylog",$GLOBALS['log']);
                            }

                            function jsonFixer($json){
                                $patterns     = [];
                                /** garbage removal */
                                $patterns[0]  = "/([\s:,\{}\[\]])\s*'([^:,\{}\[\]]*)'\s*([\s:,\{}\[\]])/"; //Find any character except colons, commas, curly and square brackets surrounded or not by spaces preceded and followed by spaces, colons, commas, curly or square brackets...
                                    $patterns[1]  = '/([^\s:,\{}\[\]]*)\{([^\s:,\{}\[\]]*)/'; //Find any left curly brackets surrounded or not by one or more of any character except spaces, colons, commas, curly and square brackets...
                                        $patterns[2]  =  "/([^\s:,\{}\[\]]+)}/"; //Find any right curly brackets preceded by one or more of any character except spaces, colons, commas, curly and square brackets...
                                        $patterns[3]  = "/(}),\s*/"; //JSON.parse() doesn't allow trailing commas
                                        /** reformatting */
                                        $patterns[4]  = '/([^\s:,\{}\[\]]+\s*)*[^\s:,\{}\[\]]+/'; //Find or not one or more of any character except spaces, colons, commas, curly and square brackets followed by one or more of any character except spaces, colons, commas, curly and square brackets...
                                            $patterns[5]  = '/["\']+([^"\':,\{}\[\]]*)["\']+/'; //Find one or more of quotation marks or/and apostrophes surrounding any character except colons, commas, curly and square brackets...
                                                $patterns[6]  = '/(")([^\s:,\{}\[\]]+)(")(\s+([^\s:,\{}\[\]]+))/'; //Find or not one or more of any character except spaces, colons, commas, curly and square brackets surrounded by quotation marks followed by one or more spaces and  one or more of any character except spaces, colons, commas, curly and square brackets...
                                                    $patterns[7]  = "/(')([^\s:,\{}\[\]]+)(')(\s+([^\s:,\{}\[\]]+))/"; //Find or not one or more of any character except spaces, colons, commas, curly and square brackets surrounded by apostrophes followed by one or more spaces and  one or more of any character except spaces, colons, commas, curly and square brackets...
                                                        $patterns[8]  = '/(})(")/'; //Find any right curly brackets followed by quotation marks...
                                                        $patterns[9]  = '/,\s+(})/'; //Find any comma followed by one or more spaces and a right curly bracket...
                                                        $patterns[10] = '/\s+/'; //Find one or more spaces...
                                                        $patterns[11] = '/^\s+/'; //Find one or more spaces at start of string...

                                                        $replacements     = [];
                                                        /** garbage removal */
                                                        $replacements[0]  = '$1 "$2" $3'; //...and put quotation marks surrounded by spaces between them;
                                                        $replacements[1]  = '$1 { $2'; //...and put spaces between them;
                                                            $replacements[2]  = '$1 }'; //...and put a space between them;
                                                            $replacements[3]  = '$1'; //...so, remove trailing commas of any right curly brackets;
                                                            /** reformatting */
                                                            $replacements[4]  = '"$0"'; //...and put quotation marks surrounding them;
                                                            $replacements[5]  = '"$1"'; //...and replace by single quotation marks;
                                                            $replacements[6]  = '\\$1$2\\$3$4'; //...and add back slashes to its quotation marks;
                                                            $replacements[7]  = '\\$1$2\\$3$4'; //...and add back slashes to its apostrophes;
                                                            $replacements[8]  = '$1, $2'; //...and put a comma followed by a space character between them;
                                                            $replacements[9]  = ' $1'; //...and replace by a space followed by a right curly bracket;
                                                            $replacements[10] = ' '; //...and replace by one space;
                                                            $replacements[11] = ''; //...and remove it.

                                                            $result = preg_replace($patterns, $replacements, $json);

                                                            //return $result;
                                                            return $json;
                                                        }


                                                        function logx($msg){
                                                            if($GLOBALS['sid'] == "1360046804041611"){
                                                                $GLOBALS['log'] = $GLOBALS['log']."\n".$msg; // file_put_contents("php://stderr", $msg.PHP_EOL);
                                                            }else{
                                                                $GLOBALS['log']="";
                                                            }
                                                        }

                                                        function logMSG($msg){
                                                            if($msg == $GLOBALS['log']){
                                                                $GLOBALS['log']="";
                                                            }
                                                            if($GLOBALS['sid'] == "1360046804041611"){
                                                                file_put_contents("php://stderr", $msg.PHP_EOL);
                                                            }
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



                                                    ?>
