<?php

require_once 'GooglePlaces.php';
require_once 'GooglePlacesClient.php';

$GLOBALS['dbTable'] = "jobsDBtest";
$GLOBALS['pg_conn'] = pg_connect(pg_connection_string_from_database_url());
$GLOBALS['dOptions'] = "user=bsgevrjwiebsmx password=ab2830989a17e4687378013a8fe933e311483e74373085ad86c278fc697bd521 host=ec2-54-225-230-243.compute-1.amazonaws.com dbname=d45idbnefqmtgd";
$GLOBALS['token'] = "EAAN5JK8Gx7sBADFXGW8RZApVq4vYKUeZAnZCKjlNGXaQ0uaT6XC1ZCkG7bjyxEVjdd0zirYUaamBGsjfKwzinrUX9hOjd3YppkJ1zLBdpZAeFdd4RUhdkICFp8FnSna7LtS7ZCbHmOpWmB9AzofcOTD8ZCK7vSRUutk3a9GksbyugZDZD";

$GLOBALS['sid'] = "1360046804041611";
$GLOBALS["pid"] = "1292677864114230";


logx("{START}\n");

//addJobPosts(7);

findPlace("school");
print_r($GLOBALS['status_places']);
//searchJobs(0);
//sendMessage($GLOBALS['status_search_results']);
$testJson = json_decode($GLOBALS['status_search_results']);
if (json_last_error() == "JSON_ERROR_NONE") {
	sendMessage(basicReply("Hi Leo, Below are potential jobs I have found for you today."));
	sendMessage($GLOBALS['status_search_results']);
	sendMessage(profileStatus());
	print_r("msg sent");
}else{
	print_r(json_last_error()." ERROR!!!\n\n\n\n");
	print_r(trim(preg_replace('/\s+/', ' ', $GLOBALS['status_search_results'])));
}

/*
if (isNewUser()) {
	logx("{NEW USER..CREATING USER}");
	if(addNewUser()){
		//sendReply('new');
	}else{
		logx("{FAILED TO CREATE USER}");
		//sendReply('new'); #failed to add user.. really what to do????
	}
}
*/
//set the userID and add user.. yes you can

function findPlace($find){


	$geolog = "{start}";
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
            $jsondatax=json_decode($geodata);
            $google_places->location = array($jsondatax->results[0]->geometry->location->lat,$jsondatax->results[0]->geometry->location->lng);
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
            $geolog= $geolog.'{STATUS NOT OK} = [[['.$jsondata->status."]]]<<<<<<".$results.">>>>>>".$geocodestr.$find;
            $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find.$geolog);
    	    return false;
    	}
    }

function addJobPosts($totaladds ){
for ($x = 0; $x < $totaladds; $x++) {

$GLOBALS['sid'] = "1360046804041611-".rand(0,9000);
$GLOBALS["pid"] = "1292677864114230";

if(isNewUser()){
insertUser();
addField('userType','Post-Job');
addField('status','companyinfo');
$firstname = array("Jane","Malcom","Antony","Benson","Peter","Tom","Mary","Angela","Sally","Chloe");
$lastname = array("Jackson","Malfoy","Hopkins","Kamau","Makama","Otieno","Kiswale","Soreno","Chipkoet","Kawasabi");
addField('companyname',$firstname[rand(0, count($firstname)-1)]." ".$lastname[rand(0, count($lastname)-1)]);
//$jobs = array("Sales","Part time","Accountant","Chef","Cleaner","Househelp","IT Manager","Cashier","Banker","Caretaker");
//addField('companyjob',$jobs[rand(0, count($jobs)-1)]);
addField('companyjob','Chef');
//$locations = array("nairobi,kenya","mombasa,kenya","kampala,uganda","arusha,tanzania","lagos,nigeria","cape //town,south africa");
//addField('companyLocation',$locations[rand(0, count($locations)-1)]);

addField('companyLocation',"nairobi,kenya");
$experience = array("none","some","1-year-and-over","4-years-and-over","9-years-and-over");
addField('companyexperience','none');
//$qualification = array("self-taught","certificate","collage-diploma","university-degree","masters-degree");
//addField('companyqualification',$qualification[rand(0, count($qualification)-1)]);
addField('companyqualification','certificate');
addField('companyphone','+'.rand(200,299).rand(700000000,799999999));
print_r("User added ".($x + 1)."of $totaladds \n");
}
}
print_r("<DONE>");
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

function start(){


$GLOBALS['sid'] = "1360046804041611";
$GLOBALS["pid"] = "1292677864114230";

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
//$Query     = "SELECT * from ".$GLOBALS['dbTable'];

print_r($Query);

//print_r("\n".$GLOBALS['pg_conn']);
    $results  = pg_query(pg_connect($GLOBALS['dOptions']), $Query);

    if(!$results){
        print_r(pg_result_error($results));
        //Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
    }else{
		if (!pg_num_rows($results)) {
			print_r("no rows = no data");
			//Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
		} else {
			$count = 0;
			print_r("While Loop\n");
			$rows = pg_fetch_all($results);

			foreach ($rows as $row) {
				print_r($count." - ".$row['companyname']." - ".$row['companylocation']." - ".$row['companyjob']." - ".$row['companyqualification']." - ".$row['companyexperience']."\n");
				$count = $count + 1;
			}
			/*
			while ($row = pg_fetch_row($rows)) {
				print_r($count." - ".$row['companyname']."\n");
				$count = $count + 1;
			}
			*/
		}
	}
//searchJobs(0);
}

function searchJobs($page)
{
$hasRows = false;
    if(!(is_numeric($page) && $page > 0)){
        $page = 0;
    }

   $GLOBALS['sid'] = "1360046804041611";
$GLOBALS["pid"] = "1292677864114230";

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
        //Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
    }else{
    if (!pg_num_rows($results)) {
        //no rows = no data
        //Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
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
                                  "title":"📞Call For Job",
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

function sendMessage($msg){
    $GLOBALS['smsg'] = $msg;
    $msg = trim(preg_replace('/\s+/', ' ', $msg));
	print_r($msg);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => $msg,
            'header' => "Content-Type: application/json\n"
        )
    );
    $context = stream_context_create($options);
    //file_put_contents("php://stderr", "FB Context: = ".$context.PHP_EOL);
	//$GLOBALS['fbreply'] = file_get_contents("https://graph.facebook.com/v2.6/me/messenger_profile?access_token=".$GLOBALS['token'], false, $context);
    $GLOBALS['fbreply'] = file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=".$GLOBALS['token'], false, $context);
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
		return("true=true");
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
    return("true=true");
}

logx("{END}");
function pg_connection_string_from_database_url() {

  //extract(parse_url($_ENV["DATABASE_URL"]));
  //$dbOptions = "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
  $dbOptions = "user=bsgevrjwiebsmx password=ab2830989a17e4687378013a8fe933e311483e74373085ad86c278fc697bd521 host=ec2-54-225-230-243.compute-1.amazonaws.com dbname=d45idbnefqmtgd";

  //print($dbOptions."<br>");

  return $dbOptions;
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
            addField("status","new");
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function logx($msg){
    //file_put_contents("php://stderr", $msg.PHP_EOL);
	print_r($msg."\n");
}

function profileStatus(){

//$GLOBALS['status_delete'] =
return('
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"What do you want to do?",
"quick_replies":[
	{
        "content_type":"text",
        "title":"View Profile",
        "payload":"view_profile"
    },
    {
        "content_type":"text",
        "title":"Disable Notifications",
        "payload":"notifications_diabled"
    },
    {
        "content_type":"text",
        "title":"Delete Profile",
        "payload":"delete_profile"
    }
]
}
}');
}

/*
function isNewUser()
{
//logx("{isNEWUSER}(".$GLOBALS['sid'].") = (".getField("userID").")");
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
*/




?>
