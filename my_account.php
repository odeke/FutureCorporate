<?php include_once("initialize.php");
mysql_select_db($database_fiber, $fiber);
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
$query_details = sprintf(" SELECT bureaus_name, bureaus_id, officephone, cellphone, email, street, city, premises, last_update as dated, bureaus_user
FROM bureaus
		where bureaus_id = %s",
    GetSQLValueString($colname_user_id, "int"));
$details = mysql_query($query_details, $fiber) or die(mysql_error());
$row_details = mysql_fetch_assoc($details);
$totalRows_details = mysql_num_rows($details);
//
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">

    <?php include_once("inc_link.php"); ?>

    <title>My Account - Inforex Corporate</title>

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

    <!-- Sidebar Wrapper Navigation -->
    <?php $my_account_pg="active"; include_once('inc_sidebar.php'); ?>

    <!-- Main Container Start -->
    <div id="mws-container" class="clearfix">

        <!-- Inner Container Start -->
        <div class="container">

            <!-- Statistics Button Container -->
            <?php include_once('inc_stats.php'); ?>

            <!-- Panels Start -->
            <div class="mws-panel grid_5">
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
                            <span class="key">Location</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['premises']; ?>, <?php echo $row_details['street']; ?>, <?php echo $row_details['city']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Phone 1</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['officephone']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Phone 2</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['cellphone']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Email</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['email']; ?></span>
                                </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mws-panel grid_3">
                <div class="mws-panel-header">
                    <span><i class="icon-book"></i> Overview</span>
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

            <div class="mws-panel grid_3">
                <div class="mws-panel-header">
                    <span>Change account password</span>
                </div>
                <div class="mws-panel-body no-padding">
                    <form class="mws-form" action="">
                        <div class="mws-form-inline">
                            <div class="mws-form-row">
                                <label class="mws-form-label">Current password</label>
                                <div class="mws-form-item">
                                    <input type="text" class="large">
                                </div>
                            </div>
                            <div class="mws-form-row">
                                <label class="mws-form-label">New password</label>
                                <div class="mws-form-item">
                                    <input type="text" class="large">
                                </div>
                            </div>
                            <div class="mws-form-row">
                                <label class="mws-form-label">Re-type new password</label>
                                <div class="mws-form-item">
                                    <input type="text" class="large">
                                </div>
                            </div>

                        </div>
                        <div class="mws-button-row">
                            <input type="submit" value="Submit" class="btn btn-danger">
                            <input type="reset" value="Reset" class="btn ">
                        </div>
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