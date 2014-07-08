<?php
$from_page = "auction_new.php";
//error handler that redirects back to page
//example url auction_new.php?error=Same currency selected
function goToPage($msg, $pg, $type="error"){
	$params = "?". $type . "=".$msg;
	header("Location: " . $pg . $params);
}

function exceptionHandler($exception, $error_pg = "auction_new.php"){
	$params = "?error=".$exception->getMessage();
	header("Location: " . $error_pg . $params);
}

set_exception_handler('exceptionHandler');

//Basic checks
if($_POST['tos'] != "on"){
	//throw new Exception('You must accept the Inforex terms and conditions.');
	goToPage("You must accept the Inforex terms and conditions.", $from_page);
    exit;
}

include_once('Fx.php');

//create new auction. createAuction($amount, $from_currency, $to_currency, $dated);
function createAuction($auction, $amount, $from_currency, $to_currency, $dated) {
	//bureaus_name is carrying the token instead,we generate date server side.
	$bureaus_id = $auction->getBureausID(session_id());
	$min_rate = 0;
	
	$stmt = mysqli_prepare($auction->connection, "INSERT INTO sales (bureaus_id, to_currency, from_currency, amount, min_rate, created, expires, order_id, trade_from) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$auction->throwExceptionOnError();
    $trade_from = 2; //sales from companies are 2
	mysqli_stmt_bind_param($stmt, 'issddssii', $bureaus_id, $to_currency, $from_currency, $amount, $min_rate, date('Y-m-d H:i:s'), $dated, $auction->getOrderId(), $trade_from);
	$auction->throwExceptionOnError();

	mysqli_stmt_execute($stmt);
	$auction->throwExceptionOnError();

	$autoid = mysqli_stmt_insert_id($stmt);

	mysqli_stmt_free_result($stmt);
	//mysqli_close($auction->connection);

    $alert_type = 2; //since we are in auctions, sales is 2
//    $bureausToBlast = "256772076530";
    $bureausToBlast = "256751077218,256772076530,256782905955,256777812324";
    $auctionCurrency = $auction->getACurrencyFromId($from_currency);
    $blast_result = $auction->createAlert($alert_type, $bureausToBlast, $auctionCurrency->currency_abbr, number_format($amount), date("h:iA, jS F", strtotime($dated)));
    $auction->log->logInfo('SmS blast result:', $blast_result);

	return true;
}

$auction = new Fx(); //shud check authentication on init.
//POST vars
$amount = $_POST['amount'];
$from_currency = $_POST['from_currency'];
$to_currency = $_POST['to_currency'];
$expiry_date = $_POST['expiry_date'];
$expiry_time = $_POST['expiry_time'];

//check vars
//amount should be above 1000
//in future convert it to UGX so amount shud be above 2,600,000UGX
if($amount < 1000){
	//throw new Exception('Amount minimum is 1000');
	goToPage("Amount minimum is 1000.", $from_page);
    exit;
}

//from and to currency shud be different duh
if($from_currency == $to_currency){
	//throw new Exception('The currency you are converting from should be different from your target currency');
	goToPage("The currency you are converting from should be different from your target currency.", $from_page);
    exit;
}

//prepare expiry date/time
$expiry_date_time = strtotime($expiry_date . " " . $expiry_time);
$dated = date("Y-m-d H:i:s", $expiry_date_time);
//check to see if it expires atleast 2 hours into the future
if(($expiry_date_time - time()) < 7200){
    //throw new Exception('Set the Expiry time atleast 2 hours ahead');
    goToPage("Set the Expiry time atleast 2 hours ahead", $from_page);
    exit;
}

if(createAuction($auction, $amount, $from_currency, $to_currency, $dated)){
	goToPage("Auction created successfully. Details available under Auction History.", $from_page, "success");
}
// try {
// 	createAuction($amount, $from_currency, $to_currency, $dated);
// } catch (Exception $e) {
// 	throw new Exception('Failed to create new auction, please contact our support team.');
// 	//goToPage("Failed to create new auction, please contact our support team. Error: " . $e , $from_page);
//     exit;
// }
