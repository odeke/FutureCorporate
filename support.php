<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php");

//insertion
if (isset($_POST['new_ticket'])) {
    //new thread
    $insertSQL = sprintf("INSERT INTO ticket_threads (thread_status, last_updated, subject, user_id)
                         VALUES (%s, %s, %s, %s)",
        GetSQLValueString(3, "int"),
        GetSQLValueString(date('Y-m-d H:i:s'), "text"),
        GetSQLValueString($_POST['subject'], "text"),
        GetSQLValueString($_SESSION['MM_ID'], "int"));
    $insertion = mysql_query($insertSQL, $fiber) or die(mysql_error());
    $thread_id = mysql_insert_id($fiber);

    $insertSQL = sprintf("INSERT INTO tickets (thread_id, body, created)
                         VALUES
                         (%s, %s, %s)",
        GetSQLValueString($thread_id, "int"),
        GetSQLValueString($_POST['body'], "text"),
        GetSQLValueString(date('Y-m-d H:i:s'), "text"));
    $insertion = mysql_query($insertSQL, $fiber) or die(mysql_error());
    $insertGoTo = "support.php?success=Message sent, you will recieve a reply within 2 hours";
    header(sprintf("Location: %s", $insertGoTo));
}

$colname_user_id = "-1";
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
mysql_select_db($database_fiber, $fiber);
$query_messages = sprintf("SELECT ticket_id, subject, last_updated, tickets.thread_id, thread_status
    FROM tickets
    left join ticket_threads on tickets.thread_id=ticket_threads.thread_id
    WHERE ticket_threads.user_id = %s and ticket_threads.thread_status!=4 GROUP BY tickets.thread_id ORDER BY ticket_id DESC",
    GetSQLValueString($colname_user_id, "int")); //echo $query_messages;
$messages = mysql_query($query_messages, $fiber) or die(mysql_error());
$row_messages = mysql_fetch_assoc($messages);
$totalRows_messages = mysql_num_rows($messages);
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">
    <?php include_once("inc_link.php"); ?>
    <title>Support - Inforex Corporate</title>
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
    <?php $support_pg="active"; include_once('inc_sidebar.php'); ?>
    <!-- Main Container Start -->
    <div id="mws-container" class="clearfix">
        <!-- Inner Container Start -->
        <div class="container">
            <!-- Panels Start -->
            <div class="mws-panel grid_8">
                <div class="mws-panel-header">
                    <span><i class="icon-table"></i> Customer Support</span>
                </div>
                <div class="mws-panel-toolbar">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <a href="#" class="btn" id="mws-form-dialog-mdl-btn"><i class="icol-pencil"></i> New ticket</a>
                        </div>

                    </div>
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
                    <table class="mws-table mws-datatable">
                        <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($totalRows_messages > 0) { ?>
                            <?php do { ?>
                                <tr>
                                    <td><a href="support_read.php?thread_id=<?php echo $row_messages['thread_id']; ?>">
                                        <?php echo substr($row_messages['subject'], 0, 80);
                                            echo (strlen($row_messages['subject']) > 80) ? " ..." : ""; ?> </a></td>
                                    <td><?php echo $row_messages['last_updated']; ?></td>
                                </tr>
                            <?php } while ($row_messages = mysql_fetch_assoc($messages)); ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="mws-form-dialog">
                <form id="mws-validate" class="mws-form" method="post" action="support.php">
                    <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
                    <div class="mws-form-inline">
                        <div class="mws-form-row">
                            <label class="mws-form-label">Subject</label>
                            <div class="mws-form-item">
                                <input type="text" name="subject" class="required large">
                            </div>
                        </div>
                        <div class="mws-form-row">
                            <label class="mws-form-label">Message</label>
                            <div class="mws-form-item">
                                <textarea rows="" cols="" class="large required" name="body"></textarea>
                                <input type="hidden" name="new_ticket" value="1" />
                            </div>
                        </div>
                    </div>
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
<script src="custom-plugins/fileinput.js"></script>

<!-- jQuery-UI Dependent Scripts -->
<script src="jui/js/jquery-ui-1.9.2.min.js"></script>
<script src="jui/jquery-ui.custom.min.js"></script>
<script src="jui/js/jquery.ui.touch-punch.js"></script>

<!-- Plugin Scripts -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/colorpicker/colorpicker-min.js"></script>
<script src="plugins/validate/jquery.validate-min.js"></script>

<!-- Core Script -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/core/mws.js"></script>

<!-- Demo Scripts (remove if not needed) -->
<script src="js/demo/demo.table.js"></script>

<?php include_once("inc_themer_js.php"); ?>
</body>
</html>