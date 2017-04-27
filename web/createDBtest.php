<?php
$GLOBALS['dbTable'] = $_ENV["main_db_table"];
  print ("<html>");
  print ("<head></head>");
  print ("<body>");

# This function reads your DATABASE_URL config var and returns a connection
# string suitable for pg_connect. Put this in your app.
function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));
  $dbOptions = "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
  print($dbOptions."<br>");
  return $dbOptions;
}
try{
# Here we establish the connection. Yes, that's all.
$pg_conn = pg_connect(pg_connection_string_from_database_url());
$GLOBALS['dbTable'] = $_ENV["main_db_table"];

$result = pg_query($pg_conn, "SELECT * FROM ".$GLOBALS['dbTable'] );
print_r("Last connection Error read table<br>");
print_r(pg_last_error($pg_conn));
print_r("==========================<br>");
if(!$result){
    print_r("Last result read tabke<br>");
    print_r(pg_result_error($result));
    print_r("==========================<br>");
    createDB();
}else{

//if (!pg_num_rows($result)) {
//  print("Your database is currently empty.<br>");
//  print_r("==========================<br>");
  print("pg_num_rows =".pg_num_rows($result)."<br>");
  print_r("==========================<br>");
  print_r($result);
  print_r("==========================<br>");
  print_r(pg_result_error($result));
  print_r("==========================<br>");
  print_r(pg_last_error($pg_conn));
  print_r("==========================<br>");
  print_r(pg_query($pg_conn, "SELECT * FROM jobsDBtest")."<br>");
//} else {
  print "Your Database Data:<br>";
  print ("<table>");


  $i = pg_num_fields($result);
  for ($j = 0; $j < $i; $j++) {
      $fieldname = pg_field_name($result, $j);
      print("<td><b>".$fieldname."</b><td/>");
  }
  while ($row = pg_fetch_row($result)) {
    print ("<tr>");
    for ($j = 0; $j < $i; $j++) {
         print("<td>".$row[$j]."<td/>");
    }

print ("</tr>");
    // print("- $row[0]\n");
   }
     print ("</table><br>");
      print_r($result);
}
//}


} catch (Exception $e) {
    print_r($e->getMessage()."<br>");
    // Handle exception
    //file_put_contents("php://stderr", "ERROR!!: = ".$e->getMessage().PHP_EOL);
}

function createDB(){
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $GLOBALS['dbTable'] = $_ENV["main_db_table"];
$createTable = "CREATE TABLE IF NOT EXISTS ".$GLOBALS['dbTable']." (
                    pageID text  NOT NULL,
                    userID text  NOT NULL,
                    userType text ,
                    Name text,
                    status text,
                    email text ,
                    location text ,
                    geoLocation text ,
                    job text ,
                    about text ,
                    experience text ,
                    qualification text ,
                    companyName text ,
                    companyEmail text ,
                    companyUrl text ,
                    companyAbout text ,
                    CompanyQualification text ,
                    CompanyJob text ,
                    compnayExperience text ,
                    compnayExpireDays text ,
                    compnayExpiryDate text ,
                    expireDays text ,
                    expiryDate text ,
                    lastNotification text ,
                    companiesNotification text ,
                    companiesViewed text ,
                    active text ,
                    joinDate text ,
                    paid text ,
                    amountPaid text ,
                    paymentID text
                    )";
                    print_r($createTable);
$result = pg_query($pg_conn, $createTable );
print_r("Last connection Error Create table<br>");
print_r(pg_last_error($pg_conn));
print_r("==========================<br>");
if(!$result){
    print_r("Failed to create table.<br>");
    print_r(pg_result_error($result));
    $result = pg_query($pg_conn, "DROP TABLE Json_Messages");
    if(!$result){
        print_r("Failed to delete table.<br>");
        print_r(pg_result_error($result));
    }else{
        print_r("Deleted table from database.<br>");
            print_r("Refresh to re create the table.<br>");
            $result = pg_query($pg_conn, $createTable );
        //print_r($result);
    }
    print_r("Last connection Error Delete table<br>");
    print_r(pg_last_error($pg_conn));
    print_r("==========================<br>");
}else{
    print_r("Database created.<br>");

    //print_r($result);
}
}
/*
$insertData = '{"object":"page","entry":[{"id":"763933067090623","time":1489656298161,"messaging":[{"sender":{"id":"1486644564679609"},"recipient":{"id":"763933067090623"},"timestamp":1489656298087,"message":{"mid":"mid.$cAAK2yxk7oTRhB7SCZ1a1m5n8K6Fr","seq":4271,"text":"rift"}}]}]}';

$insertQuery = "INSERT INTO Json_Messages (json)
    VALUES ('$insertData');";
$result = pg_query($pg_conn, $insertQuery );
*/

# Now let's use the connection for something silly just to prove it works:
//$result = pg_query($pg_conn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

 print ("</body>");
  print ("</html>");

/*

$createTable = "CREATE TABLE IF NOT EXISTS jobsDBtest (
            pageID text,  #currently just this page but more can be added
						userID text ,  # the senderID or Page Scope User ID (Unique for each different user)
						userType text , #can be Post Job or Find Job
						Name text , #Automatically gotten from Fb if its Find Job. Post Job can add a company name
						status text , #What is the status of the last message sent (Keeps track of where we are)
						email text , #Not sure i will need it
						location text , #Gotten from user or if Fb payload. We pass it to google and get closest city and country
						geoLocation text , #the geo locaion info we got from fb
						job text , #What Job are you looking for
						about text , #about your Self
						experience text , #Your job  experience
						qualification text , #Your job qualification
            companyName text ,
            companyEmail text ,
            companyUrl text ,
            companyAbout text ,
            CompanyQualification text ,
            CompanyJob text ,
            companyAbout text ,
            compnayExperience text ,
            compnayExpireDays text ,
            compnayExpiryDate text ,
            expireDays text ,
						expiryDate text ,
						lastNotification text ,
            companyNotification text , #CVS of Company userID that you have been send notifications of
            companyViewed text , #CVS of Company userID that you have seen job posting
						active text ,
						joinDate text ,
						paid text ,
						amountPaid text ,
						paymentID text
                    )";

*/


/*

try {
$dbuser = 'postgres';
$dbpass = 'abc123';
$host = 'localhost';
$dbname='postgres';

$connec = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
}catch (PDOException $e) {
echo "Error : " . $e->getMessage() . "<br/>";
die();
}
$sql = 'SELECT fname, lname, country FROM user_details ORDER BY country';
foreach ($connec->query($sql) as $row)
{
print $row['fname'] . " ";
print $row['lname'] . "-->";
print $row['country'] . "<br>";
}


*/
















?>
