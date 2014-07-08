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
$query = "SELECT bureaus_name, bureaus_id, officephone, cellphone, email, street, city, premises
		FROM bureaus
		where status = 1";

$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
$rows= array(); $crows = array();
$row = array();
$i = 1;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $rows[] = array(
        'bureaus_id' => $row['bureaus_id'],
        'officephone' => $row['officephone'],
        'cellphone' => $row['cellphone'],
        'email' => $row['email'],
        'street' => $row['street'],
        'city' => $row['city'],
        'premises' => $row['premises']
    );
    //print_r($row['bureaus_id']["bureau"] .  "<br>" );
}

$arWrapper = array();
//$arWrapper['k'] = array_keys($rows);
//$arWrapper['v'] = array_values($rows);
//$json = json_encode($arWrapper);
//print_r($rows);
//print_r($arWrapper['v']);
echo json_encode(array_values($rows));
?>