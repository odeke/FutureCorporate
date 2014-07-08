<?php
require_once('Connections/fiber.php');
include_once("initialize.php");
$colname_auctions_bids = -1;
if (isset($_SESSION['MM_ID']) && isset($_POST['id'])) {
    $colname_auctions_bids = $_POST['id'];
}
mysql_select_db($database_fiber, $fiber);
//bids listing
$query_auctions_bids = sprintf("SELECT bids.bureaus_id, bureaus_name, amount, expires, order_id, bids, bids_offer, bids_id, bids_status, bids_expiry, curr_from.currency_abbr as from_currency, curr_to.currency_name as to_currency
    FROM bids left join bureaus on  bids.bureaus_id=bureaus.bureaus_id
    LEFT JOIN sales on bids.auctions_id = sales.auctions_id
    left join currencies curr_from on curr_from.currency_id = from_currency
	left join currencies curr_to on curr_to.currency_id = to_currency
    WHERE sales.order_id = %s ORDER BY bids_id DESC", GetSQLValueString($colname_auctions_bids, "int"));
$auctions_bids = mysql_query($query_auctions_bids, $fiber) or die(mysql_error());
$row_auctions_bids = mysql_fetch_assoc($auctions_bids);
$totalRows_auctions_bids = mysql_num_rows($auctions_bids);

//auction details
$query_auction = sprintf("SELECT amount, expires, order_id, bids, status, curr_from.currency_abbr as from_currency, curr_to.currency_name as to_currency, curr_to.currency_abbr as to_currency_abbr
    FROM sales
    left join currencies curr_from on curr_from.currency_id = from_currency
	left join currencies curr_to on curr_to.currency_id = to_currency
    WHERE sales.order_id = %s", GetSQLValueString($colname_auctions_bids, "int"));
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
                            <span class="key"><i class="icon-time"></i> Expiry</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['expires']; ?> </span>
                                </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-shopping-cart"></i> Offers</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['bids']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-key"></i> Auction status</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_auction['status']; ?></span>
                                </span>
                        </li>
                    </ul>
                </div>

                <form action="auction_details.php" method="post" style="display: none" id="refresh_pg" name="refresh_pg">
                    <input type="hidden" name="id" value="<?php echo $row_auctions_bids['order_id']; ?>" />
                </form>
            </div>

            <div class="mws-panel grid_5">
                <div class="mws-panel-header">
                    <span><i class="icon-table"></i> Offers</span>
                </div>
                <div class="mws-panel-body no-padding">
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
                    <table class="mws-datatable-bids mws-table">
                        <thead>
                        <tr>
                            <th>Exchange rate</th>
                            <th>Forex Bureau</th>
                            <th>Offer expires</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($totalRows_auctions_bids > 0) { 
						 //ratings
						 $one = 5;
						 $two = 2;
						 $three = 6;
						?>
                        <?php do { ?>
                            <tr>
                                <td><?php echo $row_auctions_bids['bids_offer']; ?></td>
                                <td><?php echo $row_auctions_bids['bureaus_name']; ?> 
								<span class="badge badge-warning"><?php echo $row_auctions_bids['bids_id']%2 ? $one : $two ?></span></td>
                                <td><?php echo $row_auctions_bids['bids_expiry']; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn" onclick="showUrlInDialog('bureau_details_mini.php?id=<?php echo $row_auctions_bids['bureaus_id']; ?>'); return false;"> Details</a>
                                    </div>
                                    <?php if($row_auction['status'] == 'Open'){
                                        if ($row_auctions_bids['bids_expiry'] > date('Y-m-d H:i:s')) { ?>
                                            <div class="btn-group">
                                                <a href="#" class="btn" onclick="accept_bid_<?php echo $row_auctions_bids['bids_id']; ?>.submit();"> Accept</a>
                                                <form action="auction_process.php" method="post" style="display: none" id="accept_bid_<?php echo $row_auctions_bids['bids_id']; ?>">
                                                    <input type="hidden" name="bid_id" value="<?php echo $row_auctions_bids['bids_id']; ?>" />
                                                    <input type="hidden" name="accept_bid" value="YES" />
                                                </form>
                                            </div>
                                        <?php }
                                    }
                                         ?>
                                </td>
                            </tr>
                        <?php } while ($row_auctions_bids = mysql_fetch_assoc($auctions_bids)); ?>
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
<script type="text/javascript">
    function showUrlInDialog(url){
        var tag = $("<div></div>");
        $.ajax({
            url: url,
            success: function(data) {
                tag.html(data).dialog({
                    title: "Forex Bureau details",
                    modal: true,
                    width: "640"
                }).dialog('open');
            }
        });
    }
</script>
<?php include_once("inc_themer_js.php"); ?>
</body>
</html>
<?php
mysql_free_result($auctions_bids);
?>