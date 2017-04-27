<?php
$GLOBALS['pg_conn'] = pg_connect(pg_connection_string_from_database_url());
$GLOBALS['dbTable'] = "jobsDBtest";

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


function excuteQuery($Query)
{
    //$Query     = "SELECT $field from ".$GLOBALS['dbTable']." where pageID ='".$GLOBALS["pid"]."' and userID='".$GLOBALS["sid"]."'";
    $rows      = pg_query($GLOBALS['pg_conn'], $Query);
    return $rows;
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
            addField('isNotification','YES');
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}




 ?>
