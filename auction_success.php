<?php
require_once('Connections/fiber.php');
include_once("initialize.php");
if (!isset($_GET['id'])) {
    die;
}
mysql_select_db($database_fiber, $fiber);

//auction details
$query_auction = sprintf("SELECT amount, expires, order_id, bids, sales.status, curr_from.currency_abbr as from_currency, curr_to.currency_name as to_currency, curr_to.currency_abbr as to_currency_abbr, close_date, bureaus_name, bureaus.bureaus_id, email, street, city, country, officephone, cellphone
    FROM sales
    left join currencies curr_from on curr_from.currency_id = from_currency
	left join currencies curr_to on curr_to.currency_id = to_currency
	left join bids on sales.auctions_id = bids.auctions_id
	left join bureaus on bids.bureaus_id=bureaus.bureaus_id
    WHERE sales.auctions_id = %s and bids_status=%s",
    GetSQLValueString($_GET['id'], "int"),
    GetSQLValueString('Won', "text")
);
$auction = mysql_query($query_auction, $fiber) or die(mysql_error());
$row_auction = mysql_fetch_assoc($auction);
?>
    <!DOCTYPE html>
    <!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
    <!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
    <!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
    <!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">
    <?php include_once("inc_link.php"); ?>
    <title>Auction details - Inforex Corporate</title>
</head>

<body>
<?php //include_once("inc_themer.php"); ?>
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
            <div class="mws-panel grid_3">
                <div class="mws-panel-header">
                    <span><i class="icon-book"></i> Auction Summary</span>
                </div>
                <div class="mws-panel-toolbar">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <!-- <a href="#" class="btn"><i class="icol-accept"></i> Accept Best Offer</a>-->
                            <a href="#" onclick="refresh_pg.submit();" class="btn"><i class="icol-arrow-refresh"></i> Refresh</a>
                            <a href="#" class="btn"><i class="icol-cross"></i> Cancel Auction</a>
                        </div>
                    </div>
                </div>
                <div class="mws-panel-body no-padding">
                    <ul class="mws-summary clearfix">
                        <li>
                            <span class="key"><i class="icon-tags"></i>Transaction ID</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['order_id']; ?></span>
                                </span>
                        </li><li>
                            <span class="key"><i class="icon-briefcase"></i> Amount</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['amount']; ?> <?php echo $row_auction['from_currency']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-screenshot "></i> Target Currency</span>
                                <span class="val">
                                    <span class="text-nowrap icol32-money-<?php echo $row_auction['to_currency_abbr']; ?>"><?php echo $row_auction['to_currency']; ?> </span>
                                </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-time"></i> Closed</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['close_date']; ?> </span>
                                </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-shopping-cart"></i> Offers</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['bids']; ?></span>
                                </span>
                        </li>
                    </ul>
                </div>

                <form action="auction_details.php" method="post" style="display: none" id="refresh_pg" name="refresh_pg">
                    <input type="hidden" name="id" value="<?php echo $row_auctions_bids['order_id']; ?>" />
                </form>
            </div>

            <div class="mws-panel grid_3">
                <div class="mws-panel-header">
                    <span><i class="icon-book"></i> Winning Forex Bureau</span>
                </div>
                <div class="mws-panel-toolbar">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <!-- <a href="#" class="btn"><i class="icol-accept"></i> Accept Best Offer</a>-->
                            <a href="#" onclick="refresh_pg.submit();" class="btn"><i class="icol-email"></i> Send Message</a>
                        </div>
                    </div>
                </div>
                <div class="mws-panel-body no-padding">
                    <ul class="mws-summary clearfix">
                        <li>
                            <span class="key">Name</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['bureaus_name']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Office phone</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['officephone']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Mobile phone</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['cellphone']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Email</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['email']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Premises</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['premises']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Street</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['street']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">City</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['city']; ?></span>
                                </span>
                        </li>

                    </ul>
                </div>

                <form action="auction_details.php" method="post" style="display: none" id="refresh_pg" name="refresh_pg">
                    <input type="hidden" name="id" value="<?php echo $row_auction['order_id']; ?>" />
                </form>
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
mysql_free_result($auctions_bids);
?>