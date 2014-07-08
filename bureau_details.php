<?php include_once("initialize.php");
mysql_select_db($database_fiber, $fiber);
$colname_user_id = -1;
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
// start Dismas Change selecting bureaus
$id = mysql_real_escape_string(trim($_GET['id']));
// end Dismas chang
$query_details = sprintf(" SELECT bureaus_name, bureaus_id, officephone, cellphone, email, street, city, premises, last_update as dated, bureaus_user
FROM bureaus
		where bureaus_id = $id",
    GetSQLValueString($colname_user_id, "int"));
$details = mysql_query($query_details, $fiber) or die(mysql_error());
$row_details = mysql_fetch_assoc($details);
$totalRows_details = mysql_num_rows($details);

// dismas selecting currencies
$today = date('Y-m-d');
$start_time =  $today . " 00:00:00";
$end_time =  $today . " 23:59:00";

$query_currencies = "SELECT `currency_id`, `buying`, `selling` FROM `rates_archive` where `bureaus_id` = '$id' and dated > '$start_time' AND dated < '$end_time' order by  `currency_id`, `dated`";
$currency_details = mysql_query($query_currencies, $fiber) or die(mysql_error());
$row_count_currency = mysql_num_rows($currency_details);
$row_currency_details = mysql_fetch_assoc($currency_details);
//echo $row_count_currency; exit;

// dismas, get me currency names
$query_curr_name = "SELECT `currency_id`, `currency_name` FROM `currencies`";
$query_curr_name = mysql_query($query_curr_name) or die(mysql_error());
$curr_names = array("none");

while($row_currency_name_details = mysql_fetch_assoc($query_curr_name))
    {$curr_names[] = $row_currency_name_details['currency_name'];}

//
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<?php include_once("inc_link.php"); ?>

<title>Bureau Details - Inforex Corporate</title>

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
        <?php $bureau_details_pg="active"; include_once('inc_sidebar.php'); ?>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">
            
            	<!-- Statistics Button Container -->
            	<?php include_once('inc_stats.php'); ?>
                
                <!-- Panels Start -->
                
            <div class="mws-panel grid_4">
                <div class="mws-panel-header">
                    <span>Profile</span>
                </div>
                <div class="mws-panel-body no-padding">
                    <ul class="mws-summary clearfix">
                        <li>
                            <span class="key">Account Name</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['bureaus_name']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Premises</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['premises']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Street</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['street']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">City</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['city']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Rating</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['email']; ?></span>
                                </span>
                        </li>
                    </ul>
                </div>
            </div>

<!--Dismas: adding setion for bureau's rrates today-->

            <div class="mws-panel grid_4">
                    <div class="mws-panel-header">
                        <span><i class="icon-table"></i> Forex rates for ->  <?php echo $today;?></span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <table class="mws-datatable-bureaus mws-table">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Buying</th>
                                    <th>Selling</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($row_count_currency > 0) { ?>
                            <?php do { ?>
                                <tr>
                                    <td><?php echo $curr_names[$row_currency_details['currency_id']]; ?></td>
                                    <td><?php echo $row_currency_details['buying']; ?></td>
                                    <td><?php echo $row_currency_details['selling']; ?></td>
                                </tr>
                            <?php } while ($row_currency_details = mysql_fetch_assoc($currency_details)); ?>
                            <?php } ?>
                            </tbody>
                        </table>
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

    <!-- jQuery-UI Dependent Scripts -->
    <script src="jui/js/jquery-ui-1.9.2.min.js"></script>
    <script src="jui/jquery-ui.custom.min.js"></script>
    <script src="jui/js/jquery.ui.touch-punch.js"></script>

    <!-- Plugin Scripts -->
    <!--[if lt IE 9]>
    <script src="js/libs/excanvas.min.js"></script>
    <![endif]-->
    <script src="plugins/flot/jquery.flot.min.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.tooltip.min.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.pie.min.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.stack.min.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.resize.min.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.time.min.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.axislabels.js"></script>
    <script src="plugins/flot/plugins/jquery.flot.symbol.min.js"></script>

    <!-- Core Script -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/core/mws.js"></script>

    <?php include_once("inc_themer_js.php"); ?>
    <!-- Demo Scripts (remove if not needed) -->
    <script type="text/javascript">
        var Buying = <?php include('graph_buying.php'); ?>;
        var Selling = <?php include('graph_selling.php'); ?>;
    </script>
    <script src="js/demo/demo.dashboard.js?<?php echo time(); ?>"></script>

    

</body>
</html>