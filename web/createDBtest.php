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
                        pageID text NOT NULL UNIQUE,
						userID text NOT NULL UNIQUE,
						userType text NOT NULL UNIQUE,
						Name text NOT NULL UNIQUE,
						status text NOT NULL UNIQUE,
						waitingFor text NOT NULL UNIQUE,
						email text NOT NULL UNIQUE,
						location text NOT NULL UNIQUE,
						city text NOT NULL UNIQUE,
						country text NOT NULL UNIQUE,
						geo_location text NOT NULL UNIQUE,
						job text NOT NULL UNIQUE,
						description text NOT NULL UNIQUE,
						experience text NOT NULL UNIQUE,
						qualification text NOT NULL UNIQUE,
						expiry_date text NOT NULL UNIQUE,
						last_notification text NOT NULL UNIQUE,
						active text NOT NULL UNIQUE,
						join_date text NOT NULL UNIQUE,
						paid text NOT NULL UNIQUE,
						amountPaid text NOT NULL UNIQUE,
						paymentID text NOT NULL UNIQUE
                     )';
$result = pg_query($pg_conn, $createTable );

$insertData = '{"object":"page","entry":[{"id":"763933067090623","time":1489656298161,"messaging":[{"sender":{"id":"1486644564679609"},"recipient":{"id":"763933067090623"},"timestamp":1489656298087,"message":{"mid":"mid.$cAAK2yxk7oTRhB7SCZ1a1m5n8K6Fr","seq":4271,"text":"rift"}}]}]}';

$insertQuery = "INSERT INTO Json_Messages (json)
    VALUES ('$insertData');";
$result = pg_query($pg_conn, $insertQuery );


# Now let's use the connection for something silly just to prove it works:
//$result = pg_query($pg_conn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

$result = pg_query($pg_conn, "SELECT * FROM Json_Messages");

print "<pre>\n";
if (!pg_num_rows($result)) {
  print("Your database is empty.");
} else {
  print "Json Messages in your database:\n";
  while ($row = pg_fetch_row($result)) { print("- $row[0]\n"); }
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