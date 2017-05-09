<?php
require_once 'commonCore.php';
require_once 'KaziBotCore.php';
require_once 'replymessagesCore.php';
require_once 'findCore.php';
require_once 'databaseCore.php';

$GLOBALS['token'] = $_ENV["techware_fb_token"];

$query = "SELECT * from ".$GLOBALS['dbTable']." where userType = 'Find-Job' AND isNotification = 'YES'";
$results = excuteQuery($query);
$FindJobs = 0;
$PendingJobs = 0;
$PostJobs = 0;
$users = 0;

if (pg_num_rows($results)) {
    $rows = pg_fetch_all($results);
    foreach ($rows as $row) {
        //$users = $users +1 ;
        $GLOBALS['sid'] = $row['userid'];
        $GLOBALS['pid'] = $row['pageid'];
        $user_details = file_get_contents("https://graph.facebook.com/v2.6/".$GLOBALS['sid']."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=".$GLOBALS['token']);
        $user_details =  json_decode($user_details);
        $GLOBALS['username'] = $user_details->first_name;
        if(isset($GLOBALS['username']) && $GLOBALS['username']!= '' && $row['status'] = 'info'){
            $FindJobs = $FindJobs+1;
            //$users = $users +1 ;
            //searchJobs(0);
            //$GLOBALS['sid'] = $row['userid'];
            //$GLOBALS['sid'] = $_ENV['my_userID'];
            //sendMessage(basicReply('Hi '.$GLOBALS['username'].', Below are the jobs matching your description that I have found.'));
            //sendMessage($GLOBALS['status_search_results']);
        }else{
            $PendingJobs = $PendingJobs + 1 ;
            //$users = $users +1 ;
            //sendMessage($GLOBALS['status_options_find-job']);
            //if($GLOBALS['sid'] == $_ENV['my_userID']){
            //$GLOBALS['sid'] = $row['userid'];
            //$GLOBALS['sid'] = $_ENV['my_userID'];
            //sendMessage(basicReply('Hi '.$GLOBALS['username'].',You need to complete your job profile before you can get a job notification.'));
            //sendReply($row['status']);
            //}
        }
    }
}

$query = "SELECT * from ".$GLOBALS['dbTable']." where userType = 'Post-Job' AND isNotification = 'YES' AND status = 'companyinfo'";
$results = excuteQuery($query);

if (pg_num_rows($results)) {
    $rows = pg_fetch_all($results);
    foreach ($rows as $row) {
        $GLOBALS['sid'] = $row['userid'];
        $GLOBALS['pid'] = $row['pageid'];
        $user_details = file_get_contents("https://graph.facebook.com/v2.6/".$GLOBALS['sid']."?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=".$GLOBALS['token']);
        $user_details =  json_decode($user_details);
        $GLOBALS['username'] = $user_details->first_name;
        if(isset($GLOBALS['username']) && $GLOBALS['username']!= '')
        {
            $PostJobs = $PostJobs+1;
            //searchJobs(0);
            //sendMessage(bsaicReply('Hi '.$GLOBALS['username'].',\nIs your job posting still open? Please review your.'));
            //sendReply("companyInfo");
            //sendMessage($GLOBALS['status_options_post-job']);
        }
    }
}



$query = "SELECT * from ".$GLOBALS['dbTable'];
$results = excuteQuery($query);

if (pg_num_rows($results)) {
    $rows = pg_fetch_all($results);
    foreach ($rows as $row) {
$users = $users +1 ;
}
}


$GLOBALS['sid'] = $_ENV['my_userID'];
sendMessage(basicReply('Find Jobs = ('.$FindJobs.')\nPost Jobs = ('.$PostJobs.')\nPending Jobs = ('.$PendingJobs.')\nTotal Users = ('.$users.')'));
 ?>
