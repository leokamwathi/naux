<?php














# This function reads your DATABASE_URL config var and returns a connection
# string suitable for pg_connect. Put this in your app.
function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));
  return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
}
# Here we establish the connection. Yes, that's all.
$pg_conn = pg_connect(pg_connection_string_from_database_url());


$createTable = 'CREATE TABLE IF NOT EXISTS jobsDBtest (
                        pageID text NOT NULL ,
						userID text NOT NULL ,
						userType text NOT NULL ,
						Name text NOT NULL ,
						status text NOT NULL ,
						waitingFor text NOT NULL ,
						email text NOT NULL ,
						location text NOT NULL ,
						city text NOT NULL ,
						country text NOT NULL ,
						geo_location text NOT NULL ,
						job text NOT NULL ,
						description text NOT NULL ,
						experience text NOT NULL ,
						qualification text NOT NULL ,
						expiry_date text NOT NULL ,
						last_notification text NOT NULL ,
						active text NOT NULL ,
						join_date text NOT NULL ,
						paid text NOT NULL ,
						amountPaid text NOT NULL ,
						paymentID text NOT NULL
                     )';
$result = pg_query($pg_conn, $createTable );
print_r($result);

/*
$insertData = '{"object":"page","entry":[{"id":"763933067090623","time":1489656298161,"messaging":[{"sender":{"id":"1486644564679609"},"recipient":{"id":"763933067090623"},"timestamp":1489656298087,"message":{"mid":"mid.$cAAK2yxk7oTRhB7SCZ1a1m5n8K6Fr","seq":4271,"text":"rift"}}]}]}';

$insertQuery = "INSERT INTO Json_Messages (json)
    VALUES ('$insertData');";
$result = pg_query($pg_conn, $insertQuery );
*/

# Now let's use the connection for something silly just to prove it works:
//$result = pg_query($pg_conn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

$result = pg_query($pg_conn, "SELECT * FROM jobsDBtest");

print "<pre>\n";
if (!pg_num_rows($result)) {
  print("Your database is empty.");
} else {
  print "Json Messages in your database:\n";
  while ($row = pg_fetch_row($result)) {
     print("- $row[0]\n"); 
   }
}
print "\n";






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
