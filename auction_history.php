<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php"); ?>
<?php

$colname_my_auctions = "-1";
if (isset($_SESSION['MM_ID'])) {
  $colname_my_auctions = $_SESSION['MM_ID'];
}
mysql_select_db($database_fiber, $fiber);
$query_my_auctions = sprintf("SELECT auctions_id, order_id, curr_from.currency_abbr as from_currency, curr_to.currency_abbr as to_currency, amount, created, expires, status, close_date, bids
    FROM sales left join currencies on  sales.from_currency=currencies.currency_id
    left join currencies curr_from on curr_from.currency_id = from_currency
	left join currencies curr_to on curr_to.currency_id = to_currency
    WHERE bureaus_id = %s ORDER BY auctions_id DESC", GetSQLValueString($colname_my_auctions, "int"));
$my_auctions = mysql_query($query_my_auctions, $fiber) or die(mysql_error());
$row_my_auctions = mysql_fetch_assoc($my_auctions);
$totalRows_my_auctions = mysql_num_rows($my_auctions);
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<?php include_once("inc_link.php"); ?>

<title>My Auctions - Inforex Corporate</title>

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
        <?php $auction_history_pg="active"; include_once('inc_sidebar.php'); ?>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">
            
            	<!-- Statistics Button Container -->
            	<?php include_once('inc_stats.php'); ?>
                
                <!-- Panels Start -->
                
            	<div class="mws-panel grid_8">
                    <div class="mws-panel-header">
                        <span><i class="icon-table"></i> My Auctions</span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <table class="mws-datatable-fn mws-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Target Currency</th>
                                    <th>Ends on</th>
                                    <th>Offers</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($totalRows_my_auctions > 0) { ?>
                                <?php do { ?>
  <tr>
    <td><form action="auction_details.php" method="post">
      <button type="submit" name="id" value="<?php echo $row_my_auctions['order_id']; ?>" class="btn btn-inverse"><i class="icon-search"></i> <?php echo $row_my_auctions['order_id']; ?></button>
    </form>
    </td>
    <td><?php echo $row_my_auctions['amount'] . " " . $row_my_auctions['from_currency']; ?></td>
    <td><?php echo $row_my_auctions['to_currency']; ?></td>
    <td><?php echo $row_my_auctions['expires']; ?></td>
    <td><?php echo $row_my_auctions['bids']; ?></td>
    <td><?php echo $row_my_auctions['status']; ?></td>
  </tr>
  <?php } while ($row_my_auctions = mysql_fetch_assoc($my_auctions)); ?>
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
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/colorpicker/colorpicker-min.js"></script>

    <!-- Core Script -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/core/mws.js"></script>

    <!-- Demo Scripts (remove if not needed) -->
    <script src="js/demo/demo.table.js"></script>

    <?php include_once("inc_themer_js.php"); ?>

    

</body>
</html>
<?php
mysql_free_result($myauctions);

mysql_free_result($my_auctions);
?>
