<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php"); ?>
<?php

$colname_bureaus = "-1";
if (isset($_SESSION['MM_ID'])) {
    $colname_bureaus = $_SESSION['MM_ID'];
}
mysql_select_db($database_fiber, $fiber);
$query_bureaus = sprintf("SELECT * from bureaus ORDER BY last_update DESC", GetSQLValueString($colname_bureaus, "int"));
$bureaus = mysql_query($query_bureaus, $fiber) or die(mysql_error());
$row_bureaus = mysql_fetch_assoc($bureaus);
$totalRows_bureaus = mysql_num_rows($bureaus);
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<?php include_once("inc_link.php"); ?>

<title>Forex Bureaus - Inforex Corporate</title>

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
        <?php $bureaus_pg="active"; include_once('inc_sidebar.php'); ?>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">
            
            	<!-- Statistics Button Container -->
            	<?php include_once('inc_stats.php'); ?>
                
                <!-- Panels Start -->
                
            	<div class="mws-panel grid_8">
                    <div class="mws-panel-header">
                        <span><i class="icon-table"></i> Forex Bureaus</span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <table class="mws-datatable-bureaus mws-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Premises</th>
                                    <th>Street</th>
                                    <th>District</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($totalRows_bureaus > 0) { ?>
                            <?php do { ?>
                                <tr>
                                    <td><?php echo $row_bureaus['bureaus_name']; ?></td>
                                    <td><?php echo $row_bureaus['premises']; ?></td>
                                    <td><?php echo $row_bureaus['street']; ?></td>
                                    <td><?php echo $row_bureaus['city']; ?></td>
                                    <td><?php echo $row_bureaus['bureaus_id']; ?></td>
                                </tr>
                            <?php } while ($row_bureaus = mysql_fetch_assoc($bureaus)); ?>
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
    <script src="custom-plugins/fileinput.js"></script>
    
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