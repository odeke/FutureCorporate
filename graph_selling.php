<?php
include('Connections/fiber.php');
mysql_select_db($database_fiber, $fiber);
// get data and store in a json array
$today = date('Y-m-d');
$seven_days_ago = date('Y-m-d', (time() - 1209600));
$query = "SELECT dated, selling
		FROM daily_rates
		WHERE dated > '". $seven_days_ago ."' AND dated < '". $today ."'
		AND currency_id = " . $_SESSION['MM_activeCurrencyID'];

$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
$num_bureaus = mysql_num_rows($result);

$rows= array();
$row = array();
$i = 1;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $rows[] = array(strtotime($row['dated']) * 1000, intval($row['selling']));
}
echo json_encode($rows);