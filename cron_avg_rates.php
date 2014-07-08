<?php
include('Connections/fiber.php');
include('common.php');
$counter = 0;
for($i = 1; $i < 32; $i++){
	$curr = 1;
	$month = 6;
	$start_date = '2014-'. $month .'-' . $i;
	
	$query = "SELECT buying, selling
		    FROM rates_archive
		    WHERE dated > '$start_date' and
		    dated < '2014-". $month . "-" . ($i+1) ."'
		    AND currency_id = ". $curr;
    
	$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
	$num_rates = mysql_num_rows($result); //echo $query; echo $num_rates; die;
	$buying = array();
	$selling = array();
	$row = array();
	while ($row = mysql_fetch_assoc($result)) {
	    $buying[] = $row['buying'];
	    $selling[] = $row['selling'];
	}
	//print_r($buying);
 
	if ((count($buying) > 0) && (count($selling) > 0)) {
	    $avg_buying = array_sum($buying) / count($buying);
	    $avg_selling = array_sum($selling) / count($selling);
	
	    $insertSQL = sprintf("INSERT INTO avg_rates (currency_id, buying, selling, dated) VALUES (%s, %s, %s, %s)",
		GetSQLValueString($curr, "int"),
		GetSQLValueString($avg_buying, "int"),
		GetSQLValueString($avg_selling, "int"),
		GetSQLValueString($start_date, "text"));
    
	    $Result1 = mysql_query($insertSQL) or die("Error Adding daily rate:" . mysql_error());
	    $counter++;
	}
    
}
echo "Rates updated: " . $counter;