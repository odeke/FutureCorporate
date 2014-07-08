<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php"); ?>
<?php
//deletion request
if (isset($_GET['thread_id_del'])) {
    $query_message_del = sprintf("UPDATE `ticket_threads` set thread_status = 4, last_updated=%s where `thread_id`=%s and user_id=%s ",
        GetSQLvalueString(date('Y-m-d H:i:s'), "text"),
        GetSQLvalueString($_GET['thread_id_del'], "int"),
        GetSQLValueString($_SESSION['MM_ID'], "int")
    );
    $message_del = mysql_query($query_message_del, $fiber) or die(mysql_error());
    header("Location: support.php?info=Ticket deleted");
}

// dismas reply*****************************************************
if (isset($_POST['reply_form'])){

    $date = date('Y-m-d H:i:s');

    $q_send_msg = sprintf("INSERT INTO `tickets`(`body`, `created`, `thread_id`) VALUES (%s,%s,%s)",
        GetSQLvalueString($_POST['message'], "text"),
        GetSQLvalueString($date, "text"),
        GetSQLvalueString($_POST['thread_id'], "int")
    );
    $q_send_msg = mysql_query($q_send_msg, $fiber) or die("error: sending message: ".mysql_error());

    $q_thread = sprintf("UPDATE `ticket_threads` set thread_status = 3, last_updated=%s where `thread_id`=%s and user_id=%s ",
        GetSQLvalueString($date, "text"),
        GetSQLvalueString($_POST['thread_id'], "int"),
        GetSQLValueString($_SESSION['MM_ID'], "int")
    );
    $q_thread = mysql_query($q_thread, $fiber) or die("error: sending message: ".mysql_error());
    header("Location: support_read.php?thread_id=" . $_POST['thread_id']);
}

// end reply*****************************************************

$colname_user_id = "-1";
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
$colname_thread_id = "-1";
if (isset($_GET['thread_id'])) {
    $colname_thread_id = $_GET['thread_id'];
}

$query_tickets = sprintf("SELECT subject, ticket_id, tickets.thread_id, body, created, user_id, staff_id,`bureaus_name` AS recipient
    FROM tickets
    left join ticket_threads on tickets.thread_id=ticket_threads.thread_id
    LEFT JOIN bureaus ON ticket_threads.`user_id` = bureaus.bureaus_id
    WHERE tickets.thread_id = %s and ticket_threads.thread_status != 4 ORDER BY ticket_id asc",
    GetSQLValueString($colname_thread_id, "int")); //echo $query_tickets;
$tickets = mysql_query($query_tickets, $fiber) or die(mysql_error());
$row_tickets = mysql_fetch_assoc($tickets);
$row2 = $row_tickets;
$totalRows_tickets = mysql_num_rows($tickets);

?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">

    <?php include_once("inc_link.php"); ?>

    <title>Support tickets - Inforex Corporate</title>

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
    <?php $tickets_pg="active"; include_once('inc_sidebar.php'); ?>

    <!-- Main Container Start -->
    <div id="mws-container" class="clearfix">

        <!-- Inner Container Start -->
        <div class="container">


            <!-- Panels Start -->
            <div class="mws-panel grid_8">
                <div class="mws-panel-header">
                    <span><i class="icon-envelope"></i><?php
                        echo $row_tickets['subject']; ?></span>
                </div>
                <div class="mws-panel-toolbar">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <a href="#" class="btn hide"><i class="icol-email-open-image"></i> Reply</a>
                            <a href="support_read.php?thread_id_del=<?php echo $row2['thread_id']; ?>" class="btn"><i class="icol-cross"></i> Delete</a>
                        </div>
                    </div>
                </div>
                <div class="mws-panel-body">
                    <h3 class="message_title"><?php //echo $row_tickets['subject']; ?></h3>
                    <?php if ($totalRows_tickets > 0) { ?>
                        <?php do {
                            $dated = date("jS M, h:ia", strtotime($row_tickets['created']));

                            ?>
                            <span class="badge message_date pull-left <?php
                            echo (empty($row_tickets['staff_id'])) ? "" : "badge-info "; ?>">
                                <?php
                                echo ($row_tickets['user_id'] == $_SESSION['MM_ID']) ? "You" : "Staff";
                                echo " (" . $dated . ")"; ?></span>
                            <p class="<?php
                            echo ($row_tickets['user_id'] == $_SESSION['MM_ID']) ? "clear" : ""; ?> line-border-top message-p"><?php echo $row_tickets['body']; ?></p>
                        <?php } while ($row_tickets = mysql_fetch_assoc($tickets)); ?>
                    <?php } ?>

                    <form class="mws-form" action="support_read.php?thread_id=<?php echo $row2['thread_id']; ?>" method="post">
                        <div class="mws-form-block">
                            <div class="mws-form-row line-border-top">
                                <label class="mws-form-label">Add a reply</label>
                                <div class="mws-form-item">
                                    <textarea rows="" cols="" class="large" name="message"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mws-button-row mws-button-row-message">
                            <input type="hidden" name="reply_form" value="reply"/>
                            <input type="hidden" name="thread_id" value="<?php echo $row2['thread_id']; ?>">
                            <input type="hidden" name="to_who" value="<?php echo ($row2['from_user_id'] == $_SESSION['MM_ID']) ? $row2['user_id'] : $row2['from_user_id']; ?>">
                            <input type="submit" value="Send" class="btn btn-danger">
                            <input type="reset" value="Clear" class="btn ">
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