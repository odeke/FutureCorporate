<?php
error_reporting(E_ALL);
date_default_timezone_set('Africa/Kampala');
require("KLogger.php");
class Fx {
	var $username = "b9b8665ad20534";
	var $password = "2706ff47";
	var $server = "eu-cdbr-azure-north-b.cloudapp.net";
	var $port = "3306";
	var $databasename = "inforexed";
	var $connection;
    public $log;

	/**
	 * The constructor initializes the connection to database. 
	 */
	public function __construct() {
        session_start();
	  	$this->connection = mysqli_connect(
	  							$this->server,  
	  							$this->username,  
	  							$this->password, 
	  							$this->databasename,
	  							$this->port
	  						);

		$this->throwExceptionOnError($this->connection);
        //ini logger
        $this->log = KLogger::instance(dirname(__FILE__), KLogger::DEBUG);

	}

		/**
	 * Utility function to get userID based on auth token.
	 */
	public function getBureausID($bureaus_token) {
		//compare checking sess var auth_token against bureaus_token
		$stmt = mysqli_prepare($this->connection, "SELECT bureaus_id FROM bureaus where bureaus_token=? and account_type = ?");
		$this->throwExceptionOnError();

        $company_account_type = 3;
		mysqli_stmt_bind_param($stmt, 'si', $bureaus_token, $company_account_type);
		$this->throwExceptionOnError();
		mysqli_stmt_execute($stmt);
		$this->throwExceptionOnError();
		mysqli_stmt_bind_result($stmt, $row->bureaus_id);
		
		if(mysqli_stmt_fetch($stmt)) {
	      return $row->bureaus_id;
		} else {
          session_destroy();
	      throw new Exception('Session timeout: Please login again.');
	      return null;
		}
	}

    function notifySaleWinner($auction_details, $company_details){
        $winner_bid_dated = date('g:i A, jS F Y', strtotime($auction_details->bids_dated));
        $subject = "Your Bid has won Sale #' . $auction_details->order_id .' | Inforex Africa";
        $message = '
				<html><head>
				<style> * {font-family: Verdana, Sans-serif;} body, td, th { font-size: 12px; }</style>
				</head>
				<body style="font-family: Verdana, Sans-serif;font-size: 12px;">
				<div style="font-size: 12px; background-color: #f9f9f9; border: 1px solid #D0D0D0; color: #002166; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px;"> <small>This email was sent to '.$auction_details->bureaus_name .' ['. $auction_details->bureaus_user .']<br/> All emails from Inforex Africa contain the Company name and username registered with us. Please do not trust any email claiming to be from Inforex but does not include your Company name and username</small></div>

				<h1 style="color: #444; background-color: transparent; border-bottom: 1px solid #D0D0D0; font-size: 16px; font-weight: bold; margin: 24px 0 2px 0; padding: 5px 0 6px 0;">Congratulations, your Bid on Sale [ID:' . $auction_details->order_id .'] has WON.</h1>

				<div style="border-radius:8px; font-size: 12px; background-color: #FFFFDD; border: 1px solid #FFD700; color: #002166; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px;">
				<table border=0>
					<tr><th colspan="2"><strong>Sale details</strong></th></tr>
					<tr>
						<td align="right">Your Bid:</td>
						<td>'. $this->fnumber_format($auction_details->bids_offer) .' UGX at '. $winner_bid_dated .'</td>
					</tr><tr>
						<td align="right">Seller company:</td>
						<td>'. $company_details->bureaus_name .'</td>
					</tr><tr>
						<td align="right">Amount on Sale:</td>
						<td>'. $this->fnumber_format($auction_details->amount) .' '. $auction_details->from_currency .'</td>
					</tr><tr>
						<td align="right">Office phone:</td>
						<td>'. $company_details->officephone .'</td>
					</tr><tr>
						<td align="right">Cell phone:</td>
						<td>'. $company_details->cellphone .'</td>
					</tr><tr>
						<td align="right">Email Address:</td>
						<td>'. $company_details->email .'</td>
					</tr><tr>
						<td align="right">Location:</td>
						<td>'. $company_details->premises .', '. $company_details->street .', '. $company_details->city .', '. $company_details->country .'</td>
					</tr>
				</table>
				</div>

				<p>Please contact the Seller to proceed with the transaction, <br /> Thank you for using Inforex.</p>

				<p style="text-align:center"><small>Copyright &copy; '. date('Y') .' <a target="_blank" href="http://www.inforexafrica.com/">Inforex Africa</a> </small></p>
				</body></html>
            ';

        $sending_mail = $this->sendMail($subject, $message, $auction_details->email);
        if($sending_mail){
            $this->log->logInfo('email(notifyWinnerAboutWin-'.$auction_details->email.'):', $sending_mail);
        } else {
            //echo "Got lost in the mail<br />";
            $this->log->logError('Got lost in the mail(notifyWinnerAboutWin-'.$auction_details->email.'):', $sending_mail);
        }


        $this->sendInMessage($message, 4, $auction_details->bureaus_id, 3);
    }

    //make a record of activity, ip, timestamp, bureau
    public function logAccess($activity, $ip, $bureaus_id){
        $stmt = mysqli_prepare($this->connection, "INSERT INTO access (ip, dated, bureaus_id, activity) VALUES (?, ?, ?, ?)");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'ssis', $ip, date("Y-m-d H:i:s"), $bureaus_id, $activity);
        $this->throwExceptionOnError();

        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        mysqli_close($this->connection);
    }
	
	/**
	 * Utility function to throw an exception if an error occurs 
	 * while running a mysql command.
	 */
	public function throwExceptionOnError($link = null) {
		if($link == null) {
			$link = $this->connection;
		}
//		if(mysqli_error($link)) {
//			$msg = mysqli_errno($link) . ": " . mysqli_error($link);
//            $this->log->logError('Error :-( ', $msg);
//			throw new Exception('MySQL Error - '. $msg);
//            die;
//		}
	}

    public function exceptionHandler($exception, $error_pg = "auction_details.php"){
        $params = "?error=".$exception->getMessage();
        //$this->log->logError('Error :-( ', $params);
        header("Location: " . $error_pg . $params);
    }

    public function getACurrencyFromId($id) {

        $stmt = mysqli_prepare($this->connection, "SELECT currency_id, currency_name, currency_abbr, curr_weight FROM currencies where currency_id = ?");
        $this->throwExceptionOnError();

        mysqli_stmt_bind_param($stmt, 'i', $id);
        $this->throwExceptionOnError();

        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_bind_result($stmt, $row->currency_id, $row->currency_name, $row->currency_abbr, $row->curr_weight);
        if (mysqli_stmt_fetch($stmt)) {
            //$row = new stdClass();
            mysqli_stmt_bind_result($stmt, $row->currency_id, $row->currency_name, $row->currency_abbr, $row->curr_weight);
        }

        mysqli_stmt_free_result($stmt);
        //mysqli_close($this->connection);

        return $row;
    }

	//long way jus to get a silly unique random 5 digit number.
	public function getOrderId(){
        $stmt = mysqli_prepare($this->connection, "select count(id) from ids");
        $this->throwExceptionOnError();
        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();
        mysqli_stmt_bind_result($stmt, $countResult);
        $this->throwExceptionOnError();

        if(mysqli_stmt_fetch($stmt)) {
            $offset = mt_rand(1, $countResult);
        } else {
            throw new Exception('Failed to count', 5); exit;
        }
        mysqli_stmt_free_result($stmt);


        $sql = "select id from ids limit 1 offset $offset";
        $stmt2 = mysqli_prepare($this->connection, $sql);
        $this->throwExceptionOnError();
        mysqli_stmt_execute($stmt2);
        $this->throwExceptionOnError();
        mysqli_stmt_bind_result($stmt2, $id);
        $this->throwExceptionOnError();
        mysqli_stmt_fetch($stmt2);
        mysqli_stmt_free_result($stmt2);
        if($id){
            //echo $id;
            $sql2 = "delete from ids where id = ? limit 1";
            $stmt3 = mysqli_prepare($this->connection, $sql2);
            $this->throwExceptionOnError();
            mysqli_stmt_bind_param($stmt3, 'i', $id);
            $this->throwExceptionOnError();
            mysqli_stmt_execute($stmt3);
            $this->throwExceptionOnError();
            mysqli_stmt_free_result($stmt3);
            return $id;
        } else {
            throw new Exception('Failed to get an ID', 100); exit;
        }

    }



	/**
	 * Updates the passed item in the table.
	 *
	 * Add authorization or any logical checks for secure access to your data 
	 *
	 * @param stdClass $item
	 * @return void
	 */
	public function updateRates($item) {
	
		$stmt = mysqli_prepare($this->connection, "UPDATE $this->tablename SET buying=?,selling=?, dated=?, latest=1 WHERE rates_id=?");		
		$this->throwExceptionOnError();
		
		mysqli_stmt_bind_param($stmt, 'ddsi', $item->buying, $item->selling, date('Y-m-d H:i:s'), $item->rates_id);		
		$this->throwExceptionOnError();

		mysqli_stmt_execute($stmt);		
		$this->throwExceptionOnError();
				
		mysqli_stmt_free_result($stmt);	
		mysqli_close($this->connection);

		//return $autoid;
	}

    public function createAlert($alert_type, $numbers, $currency, $amount, $expires=""){
        $url = 'http://api.infobip.com/api/v3/sendsms/plain?';
        //http://api.infobip.com/api/v3/sendsms/plain?user=inforex&password=1nf0r3xAfr1c4&sender=Friend&SMSText=hellatext&GSM=256772076530
        if($alert_type ==1){ //auction
            $message = "Active buyer requesting ". $amount . " " . $currency . ", please use the BUY feature on Inforex to respond before ". $expires;
        } elseif($alert_type==2){ //sale
            $message = "Active sale of ". $amount . " " . $currency . ", please use the SELL feature on Inforex to respond before ". $expires;
        }
        elseif($alert_type==3){ //auction won
            $message = "You won a Request of ". $amount . " " . $currency . ", please use Inforex to proceed.";
        }
        elseif($alert_type==4){ //sale won
            $message = "You won a Sale of ". $amount . " " . $currency . ", please use Inforex to proceed.";
        }

        $post_data['username'] = 'inforex';
        $post_data['password'] = '1nf0r3xAfr1c4';
        $post_data['sender'] = 'Inforex';
        $post_data['SMSText'] = urlencode($message);
        $post_data['GSM'] = $numbers;

        foreach ( $post_data as $key => $value) {
            $post_items[] = $key . '=' . $value;
        }

        $post_string = implode ('&', $post_items);
        $curl_connection = curl_init($url . $post_string);

        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);

        try {
            $result = curl_exec($curl_connection);
        } catch (Exception $ex){
            curl_close($curl_connection);
            return $ex->getMessage();
        }
        if($result == ""){
            //assume the msgs didnt go
            return false;
        } else {
            return $result;
        }
        curl_close($curl_connection);
    }

    public function notifySellerAboutWin($auction_details, $company_details){
        $winner_bid_dated = date('g:i A, jS F Y', strtotime($auction_details->bids_dated));
        $subject = "You've got a winning bid on your Sale #' . $auction_details->order_id .' | Inforex Africa";
        $message = '
				<html><head>
				<style> * {font-family: Verdana, Sans-serif;} body, td, th { font-size: 12px; } </style>
				</head>
				<body style="font-family: Verdana, Sans-serif;font-size: 12px;">
				<div style="font-size: 12px; background-color: #f9f9f9; border: 1px solid #D0D0D0; color: #002166; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px;"> <small>This email was sent to '.$company_details->bureaus_name .' ['. $company_details->bureaus_user .']<br/> All emails from Inforex Africa contain the Company name and username registered with us. Please do not trust any email claiming to be from Inforex but does not include your Company name and username</small></div>

				<h1 style="color: #444; background-color: transparent; border-bottom: 1px solid #D0D0D0; font-size: 16px; font-weight: bold; margin: 24px 0 2px 0; padding: 5px 0 6px 0;">Congratulations, you have a Winning bid on your Sale [ID:' . $auction_details->order_id .'].</h1>

				<div style="border-radius:8px; font-size: 12px; background-color: #FFFFDD; border: 1px solid #FFD700; color: #002166; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px;">
				<table border=0>
					<tr><th colspan="2"><strong>Sale details</strong></th></tr>
					<tr>
						<td align="right">Winning Bid by:</td>
						<td>'. $auction_details->bureaus_name .'</td>
					</tr><tr>
						<td align="right">Offered rate:</td>
						<td>'. $this->fnumber_format($auction_details->bids_offer) .' UGX, made at '. $winner_bid_dated .'</td>
					</tr><tr>
						<td align="right">Your Sale amount:</td>
						<td>'. $this->fnumber_format($auction_details->amount) .' '. $auction_details->from_currency .'</td>
					</tr><tr>
						<td align="right">Amount you expect from '. $auction_details->bureaus_name .':</td>
						<td>'. $this->fnumber_format($auction_details->amount * $auction_details->bids_offer) .' UGX</td>
					</tr><tr>
						<td colspan="2" align="center"><strong>Winners details</strong></td>
					</tr><tr>
						<td align="right">Office phone:</td>
						<td>'. $auction_details->officephone .'</td>
					</tr><tr>
						<td align="right">Cell phone:</td>
						<td>'. $auction_details->cellphone .'</td>
					</tr><tr>
						<td align="right">Email Address:</td>
						<td>'. $auction_details->email .'</td>
					</tr><tr>
						<td align="right">Location:</td>
						<td>'. $auction_details->premises .', '. $auction_details->street .', '. $auction_details->city .', '. $auction_details->country .'</td>
					</tr>
				</table>
				</div>

				<p>Please arrange payment with '. $auction_details->bureaus_name .', <br /> Thank you for using Inforex.</p>

				<p style="text-align:center"><small>Copyright &copy; '. date('Y') .' <a target="_blank" href="http://www.inforexafrica.com/">Inforex Africa</a> </small></p>
				</body></html>
            ';

        $sending_mail = $this->sendMail($subject, $message, $company_details->email);
        if($sending_mail){
            $this->log->logInfo('email(notifySellerAboutWin-'.$company_details->email.'):', $sending_mail);
        } else {
            //echo "Got lost in the mail<br />";
            $this->log->logError('Got lost in the mail(notifySellerAboutWin-'.$company_details->email.'):', $sending_mail);
        }
        $this->sendInMessage($message, 0, $company_details->bureaus_id, 3);

    }

    private function sendInMessage($message, $sender, $recipient, $msg_type=1){
        $stmt = mysqli_prepare($this->connection, "INSERT INTO messages (user_id, from_user_id, message, dated, msg_type) VALUES (?, ?, ?, ?, ?)");
        $this->throwExceptionOnError();
        $today_now = date('Y-m-d H:i:s');
        mysqli_stmt_bind_param($stmt, 'iissi', $recipient, $sender, $message, $today_now, $msg_type);
        $this->throwExceptionOnError();

        mysqli_stmt_execute($stmt);
        $this->throwExceptionOnError();

        mysqli_stmt_free_result($stmt);
        //mysqli_close($this->connection);
    }

    public function sendMail($subject, $message, $mailto){
        $url = 'http://sendgrid.com/';
        $user = 'odeke';
        $pass = 'bekind247';

        $params = array(
            'api_user' => $user,
            'api_key' => $pass,
            'to' => $mailto,
            'subject' => $subject,
            'html' => $message,
            'from' => 'info@inforexafrica.com',
        );

        $request = $url.'api/mail.send.json';

        $session = curl_init($request);
        curl_setopt ($session, CURLOPT_POST, true);
        curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($session);
        curl_close($session);
        $this->log->logInfo('Email(notifySellerAboutWin):', $response);
        if($response != null){
            return $response;
        } else {
            return false;
        }
    }

    private function fnumber_format($number, $decimals=2, $sep1='.', $sep2=',') {
        if(is_numeric($number)){
            //if next not significant digit is 5
            if (($number * pow(10 , $decimals + 1) % 10 ) == 5) {
                $number -= pow(10 , -($decimals+1));
            }
            return number_format($number, $decimals, $sep1, $sep2);
        } else {
            return $number;
        }
    }

}
