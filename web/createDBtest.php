<?php
$dbTable = "jobsDBtest";
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
# Here we establish the connection. Yes, that's all.
$pg_conn = pg_connect(pg_connection_string_from_database_url());
$dbTable = "jobsDBtest";

$result = pg_query($pg_conn, "SELECT * FROM jobsDBtest");
print_r("Last connection Error read table<br>");
print_r(pg_last_error($pg_conn));
print_r("==========================<br>");
if(!$result){
    print_r("Last result read tabke<br>");
    print_r(pg_result_error($result));
    print_r("==========================<br>");
    createDB();
}else{
if (!pg_num_rows($result)) {
  print("Your database is currently empty.<br>");
  print_r("==========================<br>");
  print_r($result);
  print_r("==========================<br>");
  print_r(pg_result_error($result));
  print_r("==========================<br>");
  print_r(pg_last_error($pg_conn));
  print_r("==========================<br>");
  print_r(pg_query($pg_conn, "SELECT * FROM jobsDBtest")."<br>");
} else {
  print "Your Database Data:<br>";
  print ("<table>");


  $i = pg_num_fields($result);
  for ($j = 0; $j < $i; $j++) {
      $fieldname = pg_field_name($res, $j);
      print("<td><b>".$fieldname."</b><td/>");
  }
  while ($row = pg_fetch_row($result)) {
    print ("<tr>");
    foreach($row as $cell) {
  print("<td>".$value."<td/>");
}
print ("</tr>");
    // print("- $row[0]\n");
   }
     print ("</table><br>");
      print_r($result);
}
}

function createDB(){
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $dbTable = "jobsDBtest";
$createTable = "CREATE TABLE IF NOT EXISTS ".$dbTable." (
                    pageID text NOT NULL,
                    userID text NOT NULL ,
                    userType text NOT NULL ,
                    Name text NOT NULL ,
                    status text NOT NULL ,
                    email text NOT NULL ,
                    location text NOT NULL ,
                    geoLocation text NOT NULL ,
                    job text NOT NULL ,
                    about text NOT NULL ,
                    experience text NOT NULL ,
                    qualification text NOT NULL ,
                    companyName text NOT NULL ,
                    companyEmail text NOT NULL ,
                    companyUrl text NOT NULL ,
                    companyAbout text NOT NULL ,
                    CompanyQualification text NOT NULL ,
                    CompanyJob text NOT NULL ,
                    compnayExperience text NOT NULL ,
                    compnayExpireDays text NOT NULL ,
                    compnayExpiryDate text NOT NULL ,
                    expireDays text NOT NULL ,
                    expiryDate text NOT NULL ,
                    lastNotification text NOT NULL ,
                    companiesNotification text NOT NULL ,
                    companiesViewed text NOT NULL ,
                    active text NOT NULL ,
                    joinDate text NOT NULL ,
                    paid text NOT NULL ,
                    amountPaid text NOT NULL ,
                    paymentID text NOT NULL
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
            pageID text NOT NULL,  #currently just this page but more can be added
						userID text NOT NULL ,  # the senderID or Page Scope User ID (Unique for each different user)
						userType text NOT NULL , #can be Post Job or Find Job
						Name text NOT NULL , #Automatically gotten from Fb if its Find Job. Post Job can add a company name
						status text NOT NULL , #What is the status of the last message sent (Keeps track of where we are)
						email text NOT NULL , #Not sure i will need it
						location text NOT NULL , #Gotten from user or if Fb payload. We pass it to google and get closest city and country
						geoLocation text NOT NULL , #the geo locaion info we got from fb
						job text NOT NULL , #What Job are you looking for
						about text NOT NULL , #about your Self
						experience text NOT NULL , #Your job  experience
						qualification text NOT NULL , #Your job qualification
            companyName text NOT NULL ,
            companyEmail text NOT NULL ,
            companyUrl text NOT NULL ,
            companyAbout text NOT NULL ,
            CompanyQualification text NOT NULL ,
            CompanyJob text NOT NULL ,
            companyAbout text NOT NULL ,
            compnayExperience text NOT NULL ,
            compnayExpireDays text NOT NULL ,
            compnayExpiryDate text NOT NULL ,
            expireDays text NOT NULL ,
						expiryDate text NOT NULL ,
						lastNotification text NOT NULL ,
            companyNotification text NOT NULL , #CVS of Company userID that you have been send notifications of
            companyViewed text NOT NULL , #CVS of Company userID that you have seen job posting
						active text NOT NULL ,
						joinDate text NOT NULL ,
						paid text NOT NULL ,
						amountPaid text NOT NULL ,
						paymentID text NOT NULL
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
