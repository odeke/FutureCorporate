<?php
#Include the connect.php file
include('connect.php');
#Connect to the database
//connection String
$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
//select database
mysql_select_db($database, $connect);
//Select The database
$bool = mysql_select_db($database, $connect);
if ($bool === False){
	print "can't find database";
}
// get data and store in a json array
$query = "SELECT rates.bureaus_id, bureaus_name, currency_abbr, buying, selling
		FROM rates
		left join bureaus on rates.bureaus_id = bureaus.bureaus_id
		left join currencies on currencies.currency_id = rates.currency_id
		where latest = 1
		order by bureaus.last_update desc";

$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
$rows= array();
$row = array();
$i = 1;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i = $row['bureaus_id'] - 1;
    if($rows[$i]["bureaus_id"] == $row['bureaus_id']){
        $rows[$i]["buy_".$row['currency_abbr']] = $row['buying'];
        $rows[$i]["sell_".$row['currency_abbr']] = $row['selling'];
    } else {
        $rows[$i]["bureaus_id"] = $row['bureaus_id'];
        $rows[$i]["bureau"] = $row['bureaus_name'];
        $rows[$i]["buy_".$row['currency_abbr']] = $row['buying'];
        $rows[$i]["sell_".$row['currency_abbr']] = $row['selling'];

    }
}

echo json_encode(array_values($rows));
?>