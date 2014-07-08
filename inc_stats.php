<?php
mysql_select_db($database_fiber, $fiber);
if(!empty($_POST['active_curr'])){
    $connection = mysqli_connect(
        $hostname_fiber,
        $username_fiber,
        $password_fiber,
        $database_fiber
    );

    $stmt2 = mysqli_prepare($connection, "UPDATE bureaus SET active_curr=?  WHERE bureaus_id=?");
    mysqli_stmt_bind_param($stmt2, 'ii', $_POST['active_curr'], $_SESSION['MM_ID']);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_free_result($stmt2);

    $stmt3 = mysqli_prepare($connection, "select currency_id, currency_abbr, currency_name from currencies where currency_id=?");
    mysqli_stmt_bind_param($stmt3, 'i', $_POST['active_curr']);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_bind_result($stmt3, $curr_id, $curr_abbr, $curr_name);
    mysqli_stmt_fetch($stmt3);
    mysqli_stmt_free_result($stmt3);
    mysqli_close($connection);

    $_SESSION['MM_activeCurrencyID'] = $curr_id;
    $_SESSION['MM_activeCurrencyAbbr'] = $curr_abbr;
    $_SESSION['MM_activeCurrencyName'] = $curr_name;
}
$currency = $_SESSION['MM_activeCurrencyID'];
//best selling
$query_sell = sprintf("SELECT bureaus_id, selling
		FROM rates_archive
		where currency_id=%s
		and dated > '".date('Y-m-d 00:00:00')."'
		order by selling asc limit 1",
    GetSQLValueString($currency, "text"));
$best_sell = mysql_query($query_sell, $fiber) or die(mysql_error());
$row_best_sell = mysql_fetch_assoc($best_sell);

//best buy
$query_buy = sprintf("SELECT bureaus_id, buying
		FROM rates_archive
		where currency_id=%s
		and dated > '".date('Y-m-d 00:00:00')."'
		order by buying desc limit 1",
    GetSQLValueString($currency, "text"));
$best_buy = mysql_query($query_buy, $fiber) or die(mysql_error());
$row_best_buy = mysql_fetch_assoc($best_buy);

//bank  buying
$query_bank_buy = sprintf("SELECT bank_id, buying
		FROM bank_rates
		where currency_id=%s
		and dated > '".date('Y-m-d 00:00:00')."'
		order by buying asc limit 1",
    GetSQLValueString($currency, "text"));
$bank_buy = mysql_query($query_bank_buy, $fiber) or die(mysql_error());
$row_bank_buy = mysql_fetch_assoc($bank_buy);

$query_currencies = "SELECT currency_id, currency_name FROM currencies";
$currencies = mysql_query($query_currencies, $fiber) or die(mysql_error());
$row_currencies = mysql_fetch_assoc($currencies);
$totalRows_currencies = mysql_num_rows($currencies);
?>
            <div class="mws-stat-container clearfix">

<!--Dismas>>>> changed hrefs to "bureau_details.php" ***********************************************************************-->

                    <!-- Statistic Item -->
                	<a class="mws-stat" href="bureau_details.php?id=<?php echo $row_best_buy['bureaus_id']; ?>">
                    	<!-- Statistic Icon (edit to change icon) -->
                    	<span class="mws-stat-icon icol32-money-<?php echo $_SESSION['MM_activeCurrencyAbbr']; ?>"></span>
                        
                        <!-- Statistic Content -->
                        <span class="mws-stat-content">
                        	<span class="mws-stat-title">Best Bid (<?php echo $_SESSION['MM_activeCurrencyAbbr']; ?> -> UGX)</span>
                            <span class="mws-stat-value"><?php echo number_format($row_best_buy['buying']); ?><span class="small_currency">UGX</span></span>
                        </span>
                    </a>

                	<a class="mws-stat" href="bureau_details.php?id=<?php echo $row_best_sell['bureaus_id']; ?>">
                    	<!-- Statistic Icon (edit to change icon) -->
                    	<span class="mws-stat-icon icol32-money-<?php echo $_SESSION['MM_activeCurrencyAbbr']; ?>"></span>
                        
                        <!-- Statistic Content -->
                        <span class="mws-stat-content">
                        	<span class="mws-stat-title">Best Offer (UGX -> <?php echo $_SESSION['MM_activeCurrencyAbbr']; ?>)</span>
                            <span class="mws-stat-value down"><?php echo number_format($row_best_sell['selling']); ?><span class="small_currency">UGX</span></span>
                        </span>
                    </a>

                	<a class="mws-stat" href="#">
                    	<!-- Statistic Icon (edit to change icon) -->
                    	<span class="mws-stat-icon icol32-money-<?php echo $_SESSION['MM_activeCurrencyAbbr']; ?>"></span>
                        
                        <!-- Statistic Content -->
                        <span class="mws-stat-content">
                        	<span class="mws-stat-title">Banks Bid (<?php echo $_SESSION['MM_activeCurrencyAbbr']; ?> -> UGX)</span>
                            <span class="mws-stat-value"><?php echo number_format($row_bank_buy['buying']); ?><span class="small_currency">UGX</span></span>
                        </span>
                    </a>

                    <!-- Change currency -->
                    <a class="mws-stat" href="#">
                        <!-- Statistic Icon (edit to change icon) -->
                        <span class="mws-stat-icon icol32-money-<?php echo $_SESSION['MM_activeCurrencyAbbr']; ?>"></span>

                        <!-- Statistic Content -->
                                <span class="mws-stat-content">
                                    <span class="mws-stat-title">Active currency: <?php echo $_SESSION['MM_activeCurrencyAbbr']; ?> </span>
                                    <span class="mws-stat-value">
                                        <input type="button" id="mws-form-dialog-mdl-btn" class="btn" value="Change currency">

                                </span>
                    </a>
                    <div id="mws-form-dialog">
                        <form class="mws-form" action="dashboard.php" id="change_currency" method="post">
                            <div class="mws-form-inline">
                                <div class="mws-form-row">
                                    <label class="mws-form-label">Currency</label>
                                    <div class="mws-form-item">
                                        <select class="required" name="active_curr" id="active_curr">
                                            <?php
                                            do {
                                                ?>
                                                <option value="<?php echo $row_currencies['currency_id']?>" label="<?php echo $row_currencies['currency_name']?>" <?php if($row_currencies['currency_id'] == $_SESSION['MM_activeCurrency']) { ?> selected="selected" <?php }  ?>><?php echo $row_currencies['currency_name']?></option>
                                            <?php
                                            } while ($row_currencies = mysql_fetch_assoc($currencies));
                                            $rows = mysql_num_rows($currencies);
                                            if($rows > 0) {
                                                mysql_data_seek($currencies, 0);
                                                $row_currencies = mysql_fetch_assoc($currencies);
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>