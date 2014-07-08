<!-- Themer (Remove if not needed) -->  
<?php /**
mysql_select_db($database_fiber, $fiber);
$currency = $_SESSION['MM_activeCurrencyID'];


$query_currencies = "SELECT currency_id, currency_name, currency_abbr FROM currencies";
$currencies = mysql_query($query_currencies, $fiber) or die(mysql_error());
$totalRows_currencies = mysql_num_rows($currencies);
$row_currencies = mysql_fetch_assoc($currencies);
?>
	<div id="mws-themer">
        <div id="mws-themer-content">
        	<div id="mws-themer-ribbon"></div>
            <div id="mws-themer-toggle">
                <i class="icon-bended-arrow-left"></i> 
                <i class="icon-bended-arrow-right"></i>
            </div>

        	<div id="convert-from" class="mws-themer-section">
	        	<label for="mws-theme-presets">Convert From</label>
                <select class="required convert_input" name="from_curr" id="from_curr">
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
            <div class="mws-themer-separator"></div>

        	<div id="convert-to" class="mws-themer-section">
	        	<label for="mws-theme-patterns">To</label>
                <select class="required convert_input" name="to_curr" id="to_curr">
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
            <div class="mws-themer-separator"></div>

            <div class="mws-themer-section">
	        	<label for="amount_to_convert">Amount to convert:</label><br>
                <input type="number" id="amount_from" class="required convert_input" style="width: 100%"/>            
            </div>
            <div class="mws-themer-separator"></div>

            <div class="mws-themer-section">
                <label style="font-size: 16px;width: 100%;color: #3DC3E8;padding-top: 6px;padding-bottom: 6px;">
                    <i class="icon-book" style="color: #FFF"></i> Conversion Details</label>
            </div>

            <div class="mws-themer-section">
            	<div class="mws-stat-container clearfix">            	
                	<a class="mws-stat" id="best_to_base">
                    	<!-- Statistic Icon (edit to change icon) -->
                    	<span class="mws-stat-icon" id="money_icon_from"></span>
                        
                        <!-- Statistic Content  to base-->
                        <span class="mws-stat-content">
                        	<span class="mws-stat-title">(<span id="from_abb"></span> -> UGX)</span>
                            <span class="mws-stat-value" id="inter_result">0</span>
                        </span>
                    </a>
                	<a class="mws-stat" id="best_to_final">
                    	<!-- Statistic Icon (edit to change icon) -->
                    	<span class="mws-stat-icon" id="money_icon_to"></span>
                        
                        <!-- Statistic Content  to final-->
                        <span class="mws-stat-content">
                        	<span class="mws-stat-title">(UGX -> <span id="to_abb"></span>)</span>
                            <span class="mws-stat-value" id="final_result">0</span>
                        </span>
                    </a>                                      
                </div>
            </div>

            <div class="mws-themer-separator"></div>
            <div class="mws-themer-section">
                <label>Conversion Rates</label><br>
                <ul id="rates_used">
                </ul>
            </div>

        </div>
    </div>
    <!-- Themer End -->
    <?php 
       
     $curr_abbs = '"'.$row_currencies['currency_abbr'].'",';   
     $buy = ""; $buy_date = "";
     $sell = ""; $sell_date = ""; 
     $today = "";$previous_date = "";$start_time = "";$end_time = "";
     $fall_back_limit = 0;
     $count = 0;
        
    function reset_date( &$previous_date, &$today, &$start_time, &$end_time ){
            $today = date('Y-m-d');
            $previous_date = $today;     
            $start_time =  $today . " 00:00:00";
            $end_time =  $today . " 23:59:00";
    }

    function sub_date( &$previous_date, &$today, &$start_time, &$end_time ){
                        $previous_date = strtotime('-1 day', strtotime( $previous_date ));              
                        $previous_date = date('Y-m-j', $previous_date);
                        $start_time =  $previous_date . " 00:00:00";
                        $end_time =  $previous_date . " 23:59:00";
    }
    

while(++$count <= $totalRows_currencies)
    {
        $row_abbs = mysql_fetch_assoc($currencies);
        $curr_abbs = $curr_abbs .'"'.$row_abbs['currency_abbr'].'",';
        
        reset_date($previous_date, $today, $start_time, $end_time);

        // skip query if current currency is home_currency
        if ($count == $_SESSION['MM_homeCurrency'])
        {
                    $sell = $sell .'"1",';
                    $sell_date = $sell_date .'"'.$today.'",';
                    $buy = $buy .'"1",';
                    $buy_date = $buy_date .'"'.$today.'",';
             continue;
        } 

        // select selling for current currency
        do{
            $q_curr_con = "SELECT `selling`, `dated` FROM `daily_rates` WHERE `dated` >= '$start_time' and `dated` <= '$end_time' and `currency_id` = '$count' limit 1";
            $q_curr_con = mysql_query($q_curr_con, $fiber) or die(mysql_error());
            $count_rows = mysql_num_rows($q_curr_con);
            if ($count_rows == 1)
                {
                    $result = mysql_fetch_assoc($q_curr_con);
                    $sell = $sell .'"'.$result['selling'].'",';
                    $sell_date = $sell_date .'"'.$result['dated'].'",';
                    reset_date($previous_date, $today, $start_time, $end_time);
                } else {
                    sub_date($previous_date, $today, $start_time, $end_time);
                        if($fall_back_limit == 30){
                            $sell = $sell .'"null",';
                            $sell_date = $sell_date .'"No record for past 30 days",';
                        }
                }
        }while($count_rows == 0 and $fall_back_limit++ < 30);
                    $fall_back_limit = 0;

        // select buying for that current currency
        do{
            $q_curr_con = "SELECT `buying`, `dated` FROM `daily_rates` WHERE `dated` >= '$start_time' and `dated` <= '$end_time' and `currency_id` = $count limit 1";
            $q_curr_con = mysql_query($q_curr_con, $fiber) or die(mysql_error());
            $count_rows = mysql_num_rows($q_curr_con);
            if ($count_rows == 1)
                {
                    $result = mysql_fetch_assoc($q_curr_con);
                    $buy = $buy .'"'.$result['buying'].'",';
                    $buy_date = $buy_date .'"'.$result['dated'].'",';
                    reset_date($previous_date, $today, $start_time, $end_time);
                } else {
                    sub_date($previous_date, $today, $start_time, $end_time);
                        if($fall_back_limit == 30){
                            $buy = $buy .'"null",';
                            $buy_date = $buy_date .'"No record for past 30 days",';
                        }
                    }
        }while($count_rows == 0 and $fall_back_limit++ < 30);
                    $fall_back_limit = 0;
                            
    }

    $curr_abbs = rtrim($curr_abbs, ',');
    $buy = rtrim($buy, ',');
    $buy_date = rtrim($buy_date, ',');
    $sell = rtrim($sell, ',');
    $sell_date = rtrim($sell_date, ',');



?>

<script>
    <?php
        
 echo 'var curr_abbs = ['.$curr_abbs.'];' ;
 echo 'var home_curr = '.$_SESSION['MM_homeCurrency'].';'; 
 echo 'var buy_array = ['.$buy.'];';
 echo 'var b_date_array = ['.$buy_date.'];';

 echo 'var sell_array = ['.$sell.'];';
 echo 'var s_date_array = ['.$sell_date.'];';
    ?>

</script>


*/