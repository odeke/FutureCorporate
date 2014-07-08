<?php
require_once('Connections/fiber.php');
include_once("initialize.php");
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<?php include_once("inc_link.php"); ?>

<title>Dashboard - Inforex Corporate</title>

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
        <?php $dashboard_pg="active"; include_once('inc_sidebar.php'); ?>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">
            
            	<!-- Statistics Button Container -->
            	<?php include_once('inc_stats.php'); ?>
                
                <!-- Panels Start -->
                
            	<div class="mws-panel grid_5">
                	<div class="mws-panel-header">
                    	<span><i class="icon-graph"></i> <?php echo $_SESSION['MM_activeCurrencyName']; ?> <-> Uganda Shilling</span>
                    </div>
                    <div class="mws-panel-body">
                        <div id="mws-dashboard-chart" style="height: 300px;"></div>
                    </div>
                </div>
                
            	<div class="mws-panel grid_3">
                	<div class="mws-panel-header">
                    	<span><i class="icon-book"></i> Account overview</span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <ul class="mws-summary clearfix">
                            <li>
                                <span class="key"><i class="icon-exclamation-sign"></i>New messages</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $totalRows_messages2; ?></span>
                                </span>
                            </li>
                            <li>
                                <span class="key"><i class="icon-certificate"></i> Account status</span>
                                <span class="val">
                                    <span class="text-nowrap">Active</span>
                                </span>
                            </li>
                            <li>
                                <span class="key"><i class="icon-key"></i> Last Sign In</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $_SESSION['MM_last_seen']; ?></span>
                                </span>
                            </li>
                        </ul>
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