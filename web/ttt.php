
<?php

$GLOBALS['sid'] = "1486644564679609x";
$GLOBALS["pid"] = "763933067090623";
$GLOBALS['dbTable'] = "jobsDBtest";
$GLOBALS['pg_conn'] = pg_connect(pg_connection_string_from_database_url());

logx("{START}");
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


 $searchQuery = "
	AND companyjob = '".getField('job')."'
	AND ". getSearchQualification(getField('qualification')) ."
	AND ". getSearchExperience(getField('experience')) ."
	AND companylocation= '".getField('location')."'
";

$Query     = "SELECT $userID from ".$GLOBALS['dbTable']." where pageID ='".$GLOBALS["pid"]."' and userID='".$GLOBALS["sid"]."'".$searchQuery;

print_r($Query);


    $rows  = pg_query($GLOBALS['pg_conn'], $Query);

    if(!$rows){
        logx(pg_result_error($rows));
        //Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
    }else{
    if (!pg_num_rows($rows)) {
        //no rows = no data
        //Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
    } else {
		$count = 0;
		while ($row = pg_fetch_row($rows)) {
            print_r($count." - ".$row[0]."\n");
			$count = $count + 1;
		}
	}
}


//searchJobs(0);

logx("{END}");

function searchJobs($page)
{
    if(!(is_numeric($page) && $page > 0)){
        $page = 0;
    }

    $fielddata = "";

    $searchQuery = "
        AND companyjob = '".getField('job')."'
        AND ". getSearchQualification(getField('qualification')) ."
        AND ". getSearchExperience(getField('experience')) ."
        AND companylocation= '".getField('')."'
    ";

    $Query     = "SELECT $userID from ".$GLOBALS['dbTable']." where pageID ='".$GLOBALS["pid"]."' and userID='".$GLOBALS["sid"]."'".$searchQuery;

	print_t($Query);

    $rows      = pg_query($GLOBALS['pg_conn'], $Query);

    if(!$rows){
        logx(pg_result_error($rows));
        //Send sorry we could not find and jobs matching your requirements (Please review your profile or try again later.)
    }else{
    if (!pg_num_rows($rows)) {
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
                $count = 1 ;
                while ($row = pg_fetch_row($rows) && $count < 12) {
                    $element = '
                    {
                        "title": "'.$rows['companyname'].'",
                        "subtitle": "Job Description:- '.$rows('companydescription').'\n Job:- '.$rows('companyjob').'\n Location:- '.$rows('companyLocation').'\n Experience:- '.$rows('companyexperience').'\n Qualification:- '.$rows('companyqualification').' ",
                        "buttons": [
                            {
                                  "type":"phone_number",
                                  "title":"Call '.$rows('companyname').'",
                                  "payload":"'.$rows("companyphone").'"
                            },
                            {
                                "type":"element_share"
                            }
                        ]
                    },
                    ';
                    $GLOBALS['status_search_results'] = $GLOBALS['status_search_results'].$element;
                    $count = $count + 1 ;
                }
                $GLOBALS['status_search_results'] = $GLOBALS['status_search_results'].']}}}}';
    //sdfsdf
        //tail
        //sdfsdf
    }
}
    //return $fielddata;
}

function getSearchQualification($qualification)
{
    switch (strtolower($qualification)) {
        case "self-taught":
            return ("(companyqualification = 'Self-Taught' OR companyqualification = 'Certificate' OR companyqualification = 'Collage-Diploma' OR companyqualification = 'University Degree' OR companyqualification =  'Masters Degree')");
            break;
        case "certificate":
            return ("(companyqualification = 'Certificate' OR companyqualification = 'Collage Diploma' OR companyqualification = 'University Degree' OR companyqualification =  'Masters Degree')");
            break;
        case "collage-diploma":
            return ("(companyqualification = 'Collage Diploma' OR companyqualification = 'University Degree' OR companyqualification =  'Masters Degree')");
            break;
        case "university-degree":
            return ("(companyqualification = 'University Degree' OR companyqualification =  'Masters Degree')");
            break;
        case "masters-degree":
            return ("(companyqualification =  'Masters Degree')");
            break;
        }
}

function getSearchExperience($experience){

switch (strtolower($experience)) {

    case "first-job":
        return ("(companyqualification = 'First Job' OR companyqualification = 'Under 1 year' OR companyqualification = '1 to 3 years' OR companyqualification = '4 to 8 years' OR companyqualification =  '9 years and over')");
        break;
    case "under-1-year":
        return ("(companyqualification = 'Under 1 year' OR companyqualification = '1 to 3 years' OR companyqualification = '4 to 8 years' OR companyqualification =  '9 years and over')");
        break;
    case "1-to-3 years":
        return ("(companyqualification = '1 to 3 years' OR companyqualification = '4 to 8 years' OR companyqualification =  '9-years-and-over')");
        break;
    case "4-to-8 years":
        return ("(companyqualification = '4 to 8 years' OR companyqualification =  '9 years and over')");
        break;
    case "9-years-and-over":
        return ("(companyqualification =  '9 years and over')");
        break;
}

}


function pg_connection_string_from_database_url() {
  //extract(parse_url($_ENV["DATABASE_URL"]));
  //$dbOptions = "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
  $dbOptions = "user=bsgevrjwiebsmx password=ab2830989a17e4687378013a8fe933e311483e74373085ad86c278fc697bd521 host=ec2-54-225-230-243.compute-1.amazonaws.com dbname=d45idbnefqmtgd";
  print($dbOptions."<br>");
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
