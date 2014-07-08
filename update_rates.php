<?php include_once("initialize.php");
mysql_select_db($database_fiber, $fiber);
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}

function updateRates($buying, $selling, $rates_id, $bureaus_id, $currency_id) {
    global $hostname_water, $username_water, $password_water, $database_water;
    $connection = mysqli_connect($hostname_water, $username_water, $password_water, $database_water, '3306');
    $stmt = mysqli_prepare($connection, "UPDATE rates SET buying=?,selling=?, dated=?, latest=1 WHERE rates_id=?");

    $today_now = date('Y-m-d H:i:s');
    mysqli_stmt_bind_param($stmt, 'ddsi', $buying, $selling, $today_now, $rates_id);

    mysqli_stmt_execute($stmt);
    if(mysqli_error($connection)) {
        $msg = mysqli_errno($connection) . ": " . mysqli_error($connection);
        throw new Exception('MySQL Error - '. $msg);
    }
    mysqli_stmt_free_result($stmt);

    //update bureaus last_update date
    $stmt3 = mysqli_prepare($connection, "UPDATE bureaus SET last_update=? WHERE bureaus_id=?");

    mysqli_stmt_bind_param($stmt3, 'si', $today_now, $bureaus_id);

    mysqli_stmt_execute($stmt3);
    if(mysqli_error($connection)) {
        $msg = mysqli_errno($connection) . ": " . mysqli_error($connection);
        throw new Exception('MySQL Error - '. $msg);
    }
    mysqli_stmt_free_result($stmt3);

    //archive rates
    $stmt2 = mysqli_prepare($connection, "INSERT INTO rates_archive (currency_id, bureaus_id, buying, selling, dated) VALUES (?, ?, ?, ?, ?)");

    $today_now = date('Y-m-d H:i:s');
    mysqli_stmt_bind_param($stmt2, 'iidds', $currency_id, $bureaus_id, $buying, $selling, $today_now);

    mysqli_stmt_execute($stmt2);
    if(mysqli_error($connection)) {
        $msg = mysqli_errno($connection) . ": " . mysqli_error($connection);
        throw new Exception('MySQL Error - '. $msg);
    }

    mysqli_stmt_free_result($stmt2);
    mysqli_close($connection);

    //return $autoid;
}

$query_bureaus_rates = sprintf("SELECT bureaus_name, rates_id, rates.bureaus_id, rates.currency_id, currency_abbr, buying, selling, dated, last_update FROM `rates`
        left join currencies on rates.currency_id=currencies.currency_id
        left join bureaus on rates.bureaus_id = bureaus.bureaus_id
        where rates.bureaus_id=%s  and latest=1 group by rates.currency_id", GetSQLValueString($_SESSION['MM_ID'], "int"));
$bureaus_rates = mysql_query($query_bureaus_rates, $fiber) or die(mysql_error());
$row_bureaus_rates = mysql_fetch_assoc($bureaus_rates);
$num_rows = mysql_num_rows($bureaus_rates);
//
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update_rates")) {
//print_r($_POST);
for($i = $_POST["curr_num"]; $i > 0; $i--){
    if(isset($_POST["buying_" . $i])){
        if($_POST["buying_" . $i] != 0){
            updateRates($_POST['buying_'.$i], $_POST['selling_'.$i], $_POST['rates_id_'.$i], $_POST['bureaus_id'], $_POST['currency_id_'.$i]);
        }
    }

}

$insertGoTo = "update_rates.php?s=Rates updated&t=alert_success";
if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $insertGoTo));
}
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">

    <?php include_once("inc_link.php"); ?>

    <title>Update rates - Inforex Corporate</title>

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

            <div class="mws-panel grid_3">
                <div class="mws-panel-header">
                    <span>My Forex rates</span>
                </div>
                <div class="mws-panel-body no-padding">
                    <form class="mws-form" action="">
                        <fieldset class="mws-form-inline">
                            <legend>Last updated: <?php echo relativeTime($row_bureaus_rates['last_update']) ?></legend>
                            <?php do { ?>
                            <div class="mws-form-row bordered">
                                <label class="mws-form-label"><img src="images/flags/<?php echo $row_bureaus_rates['currency_abbr']; ?>.png" /> <?php echo $row_bureaus_rates['currency_abbr']; ?></label>
                                <div class="mws-form-item">
                                    <input type="text" class="small_currency" value="<?php echo $row_bureaus_rates['buying']; ?>" name="buying_<?php echo $row_bureaus_rates['currency_id']; ?>">
                                </div>
                            </div>
                            <input type="hidden" name="currency_id_<?php echo $row_bureaus_rates['currency_id']; ?>" value="<?php echo $row_bureaus_rates['currency_id']; ?>">
                            <input type="hidden" name="rates_id_<?php echo $row_bureaus_rates['currency_id']; ?>" value="<?php echo $row_bureaus_rates['rates_id']; ?>">

                                <?php $curr_num = $row_bureaus_rates['currency_id']; } while ($row_bureaus_rates = mysql_fetch_assoc($bureaus_rates)) ?>

                            <div class="mws-form-row bordered">
                                <label class="mws-form-label">Textarea</label>
                                <div class="mws-form-item">
                                    <textarea class="large" cols="" rows=""></textarea>
                                </div>
                            </div>
                        </fieldset>

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