<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php"); ?>
<?php

mysql_select_db($database_fiber, $fiber);
$query_currencies = "SELECT currency_id, currency_name FROM currencies";
$currencies = mysql_query($query_currencies, $fiber) or die(mysql_error());
$row_currencies = mysql_fetch_assoc($currencies);
$totalRows_currencies = mysql_num_rows($currencies);
?>
<?php include_once("initialize.php"); ?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<?php include_once("inc_link.php"); ?>
<link rel="stylesheet" type="text/css" href="jui/css/jquery.ui.timepicker.css" media="screen">

<title>New Auction - Inforex Corporate</title>

</head>

<body>

	<?php include_once("inc_themer.php"); ?>

	<!-- Header -->
	<?php include_once('inc_header.php'); ?>
    
    <!-- Start Main Wrapper -->
    <div id="mws-wrapper">
    
    	<!-- Necessary markup, do not remove -->
		<div id="mws-sidebar-stitch"></div>
		<div id="mws-sidebar-bg"></div>
        
        <!-- Sidebar Wrapper -->
        <?php $auction_new_pg="active"; include_once('inc_sidebar.php'); ?>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">
            
            	<!-- Statistics Button Container -->
            	<?php include_once('inc_stats.php'); ?>
                
                <!-- Panels Start -->
                <div class="mws-panel grid_8">
                    <div class="mws-panel-header">
                        <span><i class="icon-plus-sign"></i> Create an Auction</span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <form class="mws-form wzd-validate" action="do_auction_new.php" method="post">
                            <?php if ($_GET['error'] != "") {
                                ?>
                            <div class="mws-form-message error">
                                <?php echo $_GET['error']; ?>
                            </div>
                            <?php } if ($_GET['success'] != "") { ?>
                            <div class="mws-form-message success">
                                <?php echo $_GET['success']; ?>
                            </div>
                            <?php } if ($_GET['info'] != "") { ?>
                            <div class="mws-form-message info">
                                <?php echo $_GET['info']; ?>
                            </div>
                            <?php } if ($_GET['warning'] != "") { ?>
                            <div class="mws-form-message warning">
                                <?php echo $_GET['warning']; ?>
                            </div>
                            <?php } ?>
                            <fieldset class="wizard-step mws-form-inline">
                                <legend class="wizard-label"><i class="icol-cart"></i> Transaction details</legend>
                                <fieldset class="mws-form-inline">
                                    <legend>Cash details</legend>
                                    <div id class="mws-form-row bordered">
                                        <label class="mws-form-label">I have <span class="required">*</span></label>
                                        <div class="mws-form-item">
                                            <div class="mws-form-cols">
                                                <div class="mws-form-col-6-8">
                                                    <label class="mws-form-label">Amount</label>
                                                    <div class="mws-form-item">
                                                        <input type="text" name="amount" class="number required" id="amount" >
                                                    </div>
                                                </div>
                                                <div class="mws-form-col-2-8">
                                                    <label class="mws-form-label">Currency</label>
                                                    <div class="mws-form-item">
                                                        <select class="required" name="from_currency" id="from_currency">
                                                          <?php
															do {  
															?>
															<option value="<?php echo $row_currencies['currency_id']?>" label="<?php echo $row_currencies['currency_name']?>" <?php if($row_currencies['currency_id'] == $_SESSION['home_currency']) { ?> selected="selected" <?php }  ?>><?php echo $row_currencies['currency_name']?></option>
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
                                        </div>
                                    </div>
                                
                                    <div class="mws-form-row borderedlast">
                                        <label class="mws-form-label">I want <span class="required">*</span></label>
                                        <div class="mws-form-item">
                                            <select class="required large" name="to_currency" id="to_currency">
                                                <?php
                                                do {
                                                    ?>
                                                    <option value="<?php echo $row_currencies['currency_id']?>" label="<?php echo $row_currencies['currency_name']?>"><?php echo $row_currencies['currency_name']?></option>
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
                                </fieldset>
                                <fieldset class="mws-form-inline">
                                <legend>Time period</legend>
                                    <div class="mws-form-row">
                                        <label class="mws-form-label">Expiry Date <span class="required">*</span></label>
                                        <div class="mws-form-item">
                                            <input type="text" class="mws-datepicker large required" id="expiry_date" readonly name="expiry_date">
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="mws-form-row">
                                    <label class="mws-form-label">Expiry Time <span class="required">*</span></label>
                                    <div class="mws-form-item">
                                        <input type="text" class="mws-spinner-time required" value="3:30 PM" id="expiry_time" name="expiry_time">
                                        <label for="expiry_time" class="error" generated="true" style="display:none"></label>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="wizard-step mws-form-inline">
                                <legend class="wizard-label"><i class="icol-lock"></i> Confirmation</legend>
                                <fieldset class="mws-form-inline">
                                    <legend>Summary</legend>
                                                <div class="mws-form-row bordered">
                                                    <label class="mws-form-label">Amount to be exchanged</label>
                                                    <div class="mws-form-item">
                                                        <span class="summary_item" id="amount_txt"></span>
                                                    </div>
                                                </div>
                                                <div class="mws-form-row bordered">
                                                    <label class="mws-form-label">Target currency</label>
                                                    <div class="mws-form-item">
                                                        <span class="summary_item" id="to_currency_txt"></span>
                                                    </div>
                                                </div>
                                                <div class="mws-form-row bordered">
                                                    <label class="mws-form-label">Expiry date</label>
                                                    <div class="mws-form-item">
                                                        <span class="summary_item" id="expiry_date_txt"></span>
                                                    </div>
                                                </div>
                                                <div class="mws-form-row bordered">
                                                    <label class="mws-form-label">Expiry Time</label>
                                                    <div class="mws-form-item clearfix">
                                                        <span class="summary_item" id="expiry_time_txt"></span>
                                                    </div>
                                                </div>

                                                <div class="mws-form-row">
                                                    <label class="mws-form-label">I agree to the Inforex TOS <span class="required">*</span></label>
                                                    <div class="mws-form-item">
                                                        <ul class="mws-form-list inline">
                                                            <li><input type="checkbox" id="tos_y" name="tos" class="required"> <label for="tos_y">Yes</label></li>
                                                        </ul>
                                                        <label class="error plain" generated="true" for="tos" style="display:none"></label>
                                                    </div>
                                                </div>
                                                </fieldset>
                                    
                                
                            </fieldset>
                        </form>
                    </div>
                </div>

                
            	
                <!-- Panels End -->
            </div>
            <!-- Inner Container End -->
                       
            <!-- Footer -->
            <?php include_once('inc_footer.php'); ?>
            
        </div>
        <!-- Main Container End -->
        
    </div>

    <!-- JavaScript Plugins -->
    <script src="js/libs/jquery-1.8.3.min.js"></script>
    <script src="js/libs/jquery.mousewheel.min.js"></script>
    <script src="js/libs/jquery.placeholder.min.js"></script>
    <script src="custom-plugins/fileinput.js"></script>
    
    <!-- jQuery-UI Dependent Scripts -->
    <script src="jui/js/jquery-ui-1.9.2.min.js"></script>
    <script src="jui/jquery-ui.custom.min.js"></script>
    <script src="jui/js/jquery.ui.touch-punch.js"></script>

    <script src="jui/js/globalize/globalize.js"></script>

    <!-- Plugin Scripts -->
    <script src="plugins/colorpicker/colorpicker-min.js"></script>
    <script src="plugins/validate/jquery.validate-min.js"></script>

    <!-- Wizard Plugin -->
    <script src="custom-plugins/wizard/wizard.min.js"></script>
    <script src="custom-plugins/wizard/jquery.form.min.js"></script>

    <!-- Core Script -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/core/mws.js"></script>

    <!-- Demo Scripts (remove if not needed) -->
    <script src="js/demo/demo.formelements.js"></script>
    <script src="js/demo/demo.wizard.js"></script>
    <script type="text/javascript">
        function addCommas(nStr){
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;

            while(rgx.test(x1)){
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        $(document).ready(function() {
            // jQuery-UI Datepicker
            if( $.fn.datepicker ) {
                $(".mws-datepicker").datepicker({
                    showOtherMonths: true,
                    minDate: 'today',
                    dateFormat: 'yy-mm-dd'
                });
            }

            //form wizard update
            var today = new Date();
            var hours = today.getHours();
            var mins = today.getMinutes();
            if (mins < 10) mins = "0" + mins;
            var am_pm = (hours >= 12) ? "PM" : "AM";
            if(hours > 12) hours -= 12;
            //add 3 hours into future
            if(((hours + 3) >= 12) && am_pm == 'PM') {
                if((hours +3)==12 ){
                    hours += 3;
                } else {
                    hours = (hours + 3) - 12;
                }
                am_pm = "AM";
            } else {
                hours = (hours + 3);
                am_pm = "PM";
            }

            //if (hours > 12) hours -= 3;

            var two_hours_time = hours + ':' + mins + ' ' + am_pm;

            $("#expiry_time").val(two_hours_time);
            $("#amount").bind('change', function(){
                //$("#amount_txt").text(parseInt($("#amount").val()).toLocaleString('en-US')).append("&nbsp;").append($('#from_currency').val());
                $("#amount_txt").text(addCommas($("#amount").val())).append("&nbsp;").append($('#from_currency > option:selected').attr('label'));
            });

            $("#from_currency").bind('change', function(){
                $("#amount_txt").text(addCommas($("#amount").val())).append("&nbsp;").append($('#from_currency > option:selected').attr('label'));
            });

            $("#to_currency_txt").text($("#to_currency > option:selected").attr('label'));
            $("#to_currency").bind('change', function(){
                $("#to_currency_txt").text($("#to_currency > option:selected").attr('label'));
            });

            $("#expiry_date").bind('change', function(){
                $("#expiry_date_txt").text($("#expiry_date").val());
            });

            $("#expiry_time_txt").text($("#expiry_time").val());
            $("#expiry_time").bind('keyup', function(){
                $("#expiry_time_txt").text($("#expiry_time").val());
            });
        });

        

    </script>


    <?php include_once("inc_themer_js.php"); ?>

    

</body>
</html>
<?php
mysql_free_result($currencies);
?>
