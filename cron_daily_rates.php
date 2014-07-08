<?php
include('Connections/fiber.php');
include('common.php');
//doing from 1st May to 7th.
$counter = 0;
for($i = 1; $i < 3; $i++){
    //for($j = 1; $j < 12; $j++){
	$curr = 1;
	$month = 7;
	$start_date = '2014-'. $month .'-' . $i;
	$stop_date = '2014-'. $month .'-' . ($i + 1);
	$query = "SELECT buying, selling, bureaus_id
		    FROM rates_archive
		    WHERE dated > '$start_date' AND dated < '$stop_date'
		    AND currency_id = ". $curr;
    
	$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
	$num_rates = mysql_num_rows($result);
	$rates = array();
	while ($row = mysql_fetch_assoc($result)) {
	    $rates[] = $row;
    
	}
    
	$buying = array_map(function($rates) {
	    return $rates['buying'];
	}, $rates);
    
	$selling = array_map(function($rates) {
	    return $rates['selling'];
	}, $rates);
    
    
	//buying: customers want the highest value. (most shillings for their dollars)
	//selling: customers want the lowest value (cheap dollars)
	$best_buying = 0;
	$best_selling = 0;
	if((!empty($buying)) && (!empty($selling))){
	    $best_selling = min($selling);
	    $best_buying = max($buying);
	}
    
    
	if ((!empty($best_buying)) && (!empty($best_selling))) {
	    $insertSQL = sprintf("INSERT INTO daily_rates (currency_id, buying, selling, dated) VALUES (%s, %s, %s, %s)",
		GetSQLValueString($curr, "int"),
		GetSQLValueString($best_buying, "int"),
		GetSQLValueString($best_selling, "int"),
		GetSQLValueString($start_date, "text"));
    
	    $Result1 = mysql_query($insertSQL) or die("Error Adding daily rate:" . mysql_error());
	    $counter++;
	}
	
	//echo "<hr> Best buying: $start_date: " . $best_buying; echo "<hr>";
	//echo "Best Selling: $stop_date: " . $best_selling; echo "<hr>";
    //}
    
}
echo "Rates updated: " . $counter;