<?php
$from_page = "auction_details.php";
//error handler that redirects back to page
//example url auction_new.php?error=Same currency selected
function goToPage($msg, $pg, $type="error"){
    $params = "?". $type . "=".$msg;
    header("Location: " . $pg . $params);
}



//Basic checks
if($_POST['accept_bid'] != "YES" || empty($_POST['bid_id'])){
    //throw new Exception('You must accept the Inforex terms and conditions.');
    goToPage("Refresh page and try again.", $from_page);
    exit;
}

include_once('Fx.php');

//Accepts selected bid on sale
function closeSaleSelected($auction, $auction_details, $company_details) {
    $auction->log->logInfo("SessID:". session_id());

    $stmt2 = mysqli_prepare($auction->connection, "UPDATE sales SET status='Closed', close_date=? WHERE auctions_id=? and bureaus_id=? and status != 'Closed'");
    $auction->throwExceptionOnError();

    $date_now = date('Y-m-d H:i:s');
    mysqli_stmt_bind_param($stmt2, 'sii', $date_now, $auction_details->auctions_id, $auction_details->company_id);
    $auction->throwExceptionOnError();

    if(mysqli_stmt_execute($stmt2)){
        $updated_auctions = mysqli_affected_rows($auction->connection);
        if($updated_auctions == 0){
            throw new Exception('Invalid Auction');
            exit;
        }
    }
    $auction->throwExceptionOnError();

    //update winning bid to won
    $stmt3 = mysqli_prepare($auction->connection, "UPDATE bids SET bids_status='Won', last_update='" . date('Y-m-d H:i:s') . "' WHERE auctions_id=? and bids_id=?");
    $auction->throwExceptionOnError();

    mysqli_stmt_bind_param($stmt3, 'ii', $auction_details->auctions_id, $auction_details->bids_id);
    $auction->throwExceptionOnError();

    mysqli_stmt_execute($stmt3);
    $auction->throwExceptionOnError();

    mysqli_stmt_free_result($stmt3);

    //update lost bids to LOST
    $stmt4 = mysqli_prepare($auction->connection, "UPDATE bids SET bids_status='Lost', last_update='" . date('Y-m-d H:i:s') . "' WHERE auctions_id=? and bids_status='Available' and bids_id != ?");
    $auction->throwExceptionOnError();
    mysqli_stmt_bind_param($stmt4, 'ii', $auction_details->auctions_id, $auction_details->bids_id);
    $auction->throwExceptionOnError();
    mysqli_stmt_execute($stmt4);
    $auction->throwExceptionOnError();
    mysqli_stmt_free_result($stmt4);

    //notify and congratulate the winner
    $auction->notifySaleWinner($auction_details, $company_details);

    //sms auction winner
    //get bureaus to blast
    $today_now = date('Y-m-d H:i:s');
    $alert_type = 4; //sale won is 4
    $bureausToBlast = $auction_details->alert_number;
    $auctionCurrency = $auction_details->from_currency;
    $blast_result = $auction->createAlert($alert_type, $bureausToBlast, $auctionCurrency, number_format($auction_details->amount));
    $auction->log->logInfo('SMS send(closeSaleSelected):' , $blast_result);

    //CreateAlert returns 900 if the SMS went ok.
    //@todo query delivery status reports.
    if($blast_result){
        $bureaus_count = count($bureausToBlast);
        $stmt2 = mysqli_prepare($auction->connection, "INSERT INTO alerts (alert_type, alert, total_alerted, alert_from, dated) VALUES (?, ?, ?, ?, ?)");
        $auction->throwExceptionOnError();
    } else {
        $bureaus_count = 0; //it was a failure no one got the alert.
        $stmt2 = mysqli_prepare($auction->connection, "INSERT INTO alerts (alert_type, alert, total_alerted, alert_from, dated) VALUES (?, ?, ?, ?, ?)");
        $auction->throwExceptionOnError();
    };

    mysqli_stmt_bind_param($stmt2, 'isiis', $alert_type, $blast_result, $bureaus_count, $auction_details->company_id, $today_now);
    $auction->throwExceptionOnError();
    mysqli_stmt_execute($stmt2);
    $auction->throwExceptionOnError();

    //notify auctioner
    $auction->notifySellerAboutWin($auction_details, $company_details);

    mysqli_close($auction->connection);
    return true;
}

function getAuctionDetails($auction, $bid_id) {
    $company_id = $auction->getBureausID(session_id());
    $stmt = mysqli_prepare($auction->connection, "SELECT bids_id, bids.auctions_id, bids_offer, bids_dated, bids_status, bids.bureaus_id AS bidder_id, bids_expiry, curr_to.currency_abbr to_currency, curr_from.currency_abbr as from_currency, amount, bureaus_name, created, expires, sales.status, officephone, cellphone, email, street, city, premises, bids, alert_number, sales.bureaus_id as company_id, order_id, bureaus_user
            FROM bids
            LEFT JOIN sales ON bids.auctions_id = sales.auctions_id
            left join currencies curr_from on curr_from.currency_id = from_currency
		    left join currencies curr_to on curr_to.currency_id = to_currency
            LEFT JOIN bureaus ON bids.bureaus_id = bureaus.bureaus_id
            WHERE sales.bureaus_id =? and bids.bids_id =?
            order by bids_dated desc limit 1");
    $auction->throwExceptionOnError();

    mysqli_stmt_bind_param($stmt, 'ii', $company_id, $bid_id);
    $auction->throwExceptionOnError();

    mysqli_stmt_execute($stmt);
    $auction->throwExceptionOnError();

    mysqli_stmt_bind_result($stmt, $row->bids_id, $row->auctions_id, $row->bids_offer, $row->bids_dated, $row->bids_status, $row->bidder_id, $row->bids_expiry, $row->to_currency,$row->from_currency, $row->amount,  $row->bureaus_name, $row->created, $row->expires, $row->status, $row->officephone, $row->cellphone, $row->email, $row->street, $row->city, $row->premises, $row->bids, $row->alert_number, $row->company_id, $row->order_id, $row->bureaus_user);

    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_bind_result($stmt, $row->bids_id, $row->auctions_id, $row->bids_offer, $row->bids_dated, $row->bids_status, $row->bidder_id, $row->bids_expiry, $row->to_currency,$row->from_currency, $row->amount,  $row->bureaus_name, $row->created, $row->expires, $row->status, $row->officephone, $row->cellphone, $row->email, $row->street, $row->city, $row->premises, $row->bids, $row->alert_number, $row->company_id, $row->order_id, $row->bureaus_user);
    } else {
        $row = false;
    }

    mysqli_stmt_free_result($stmt);
    return $row;
}

function getCompanyDetails($auction, $company_id) {
    $stmt = mysqli_prepare($auction->connection, "SELECT bureaus_id, bureaus_name, status, home_curr, officephone, cellphone, alert_number, alerts, email, email_admin,  street, city, country, premises, latitude, longtitude, num_users_rated
            FROM bureaus
            WHERE bureaus_id =?");
    $auction->throwExceptionOnError();

    mysqli_stmt_bind_param($stmt, 'i', $company_id);
    $auction->throwExceptionOnError();

    mysqli_stmt_execute($stmt);
    $auction->throwExceptionOnError();

    mysqli_stmt_bind_result($stmt, $row->bureaus_id, $row->bureaus_name, $row->status, $row->home_curr, $row->officephone, $row->cellphone, $row->alert_number, $row->alerts, $row->email, $row->email_admin,  $row->street, $row->city, $row->country, $row->premises, $row->latitude, $row->longtitude, $row->num_users_rated);

    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_bind_result($stmt, $row->bureaus_id, $row->bureaus_name, $row->status, $row->home_curr, $row->officephone, $row->cellphone, $row->alert_number, $row->alerts, $row->email, $row->email_admin,  $row->street, $row->city, $row->country, $row->premises, $row->latitude, $row->longtitude, $row->num_users_rated);
    } else {
        $row = false;
    }

    mysqli_stmt_free_result($stmt);
    return $row;
}

$auction = new Fx(); //shud check authentication on init.
set_exception_handler('Fx::exceptionHandler');
//POST vars
$selected_bid_id = $_POST['bid_id'];

//first grab all details about the auction and bid.
$auction_details = getAuctionDetails($auction, $selected_bid_id);
//$auction->log->logInfo('Auction_details:' , $auction_details);
$company_details = getCompanyDetails($auction, $auction_details->company_id);

if(!$auction_details){
    goToPage("Refresh page and try again.", $from_page, "error");
}
//check vars, if bid hasn't expired.
if($auction_details->bids_expiry < date('Y-m-d H:i:s')){
    goToPage("This offer has expired, please select another one.", $from_page, "error");
}

//see if auction has expired
if($auction_details->expires < date('Y-m-d H:i:s')){
    goToPage("Your auction has already expired, please create a new one.", $from_page, "error");
}
$auction->log->logInfo('**********************About to close sale Here:', session_id());
if(closeSaleSelected($auction, $auction_details, $company_details)){
    goToPage("Auction closed successfully.", "auction_success.php?id=".$auction_details->auctions_id, "success");
}
