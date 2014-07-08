<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php");
//deletion request
if (isset($_GET['thread_id_del'])) {
    $query_messages_del = sprintf("SELECT thread_id, sender_id
    FROM msg_threads
    WHERE thread_id = %s",
        GetSQLValueString($_GET['thread_id_del'], "int"));
    $messages_del = mysql_query($query_messages_del, $fiber) or die(mysql_error());
    $row_messages_del = mysql_fetch_assoc($messages_del);

    if($row_messages_del['sender_id'] == $_SESSION['MM_ID']){
        $query_message_del = sprintf("UPDATE `msg_threads` set sender_status = 2, last_updated=%s where `thread_id`=%s and thread_type=1 ",
            GetSQLvalueString(date('Y-m-d H:i:s'), "text"),
            GetSQLvalueString($row_messages_del['thread_id'], "int")
        );
    } else {
        $query_message_del = sprintf("UPDATE `msg_threads` set reciever_status = 2, last_updated=%s where `thread_id`=%s and thread_type=1 ",
            GetSQLvalueString(date('Y-m-d H:i:s'), "text"),
            GetSQLvalueString($row_messages_del['thread_id'], "int")
        );
    }

    $message_del = mysql_query($query_message_del, $fiber) or die(mysql_error());
    header("Location: messages.php?info=Message deleted");
}
    
// dismas reply*****************************************************
if (isset($_GET['reply_form'])){

$date = date('Y-m-d H:i:s');

    $q_send_msg = sprintf("INSERT INTO `messages`(`user_id`, `from_user_id`, `message`, `dated`, `box`, `thread_id`) VALUES (%s,%s,%s,%s,'Reply',%s)",
        GetSQLValueString($_GET['to_who'], "text"),
        GetSQLValueString($_SESSION['MM_ID'], "int"),
        GetSQLvalueString($_GET['message'], "text"),
        GetSQLvalueString($date, "text"),
        GetSQLvalueString($_GET['thread_id'], "int")
            
    );
    $q_send_msg = mysql_query($q_send_msg, $fiber) or die("error: sending message: ".mysql_error());
    header("Location: message_read.php?thread_id=" . $_GET['thread_id']);
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

$query_messages = sprintf("SELECT box, thread_id, message, dated, msg_type, msg_status, from_user_id, user_id, bureau_sender.bureaus_name AS sender,
	bureau_recipient.`bureaus_name` AS recipient
    FROM messages
    LEFT JOIN bureaus bureau_sender ON messages.from_user_id = bureau_sender.bureaus_id
    LEFT JOIN bureaus bureau_recipient ON messages.`user_id` = bureau_recipient.bureaus_id
    WHERE thread_id = %s ORDER BY msg_id",
    GetSQLValueString($colname_thread_id, "int"));
$messages = mysql_query($query_messages, $fiber) or die(mysql_error());
$row_messages = mysql_fetch_assoc($messages);
$row2 = $row_messages;
$totalRows_messages = mysql_num_rows($messages);

//update msg as 'read'
$msg_read_sql = sprintf("update messages set `msg_status_sender` = 'Read',`msg_status` = 'Read' WHERE `thread_id` = %s",
    GetSQLValueString($colname_thread_id, "int"));
$messages_read = mysql_query($msg_read_sql, $fiber) or die(mysql_error());
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">

    <?php include_once("inc_link.php"); ?>

    <title>Messages - Inforex Corporate</title>

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
    <?php $messages_pg="active"; include_once('inc_sidebar.php'); ?>

    <!-- Main Container Start -->
    <div id="mws-container" class="clearfix">

        <!-- Inner Container Start -->
        <div class="container">


            <!-- Panels Start -->
            <div class="mws-panel grid_8">
                <div class="mws-panel-header">
                    <span><i class="icon-envelope"></i>With: <?php
                        echo ($row_messages['from_user_id'] == $_SESSION['MM_ID']) ? $row_messages['recipient'] : $row_messages['sender']; ?></span>
                </div>
                <div class="mws-panel-toolbar">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <a href="#" class="btn hide"><i class="icol-email-open-image"></i> Reply</a>
                            <a href="message_read.php?thread_id_del=<?php echo $row2['thread_id']; ?>" class="btn"><i class="icol-cross"></i> Delete</a>
                        </div>
                    </div>
                </div>
                <div class="mws-panel-body">
                    <h3 class="message_title"><?php // echo $row_messages['subject']; ?></h3>
                    <?php if ($totalRows_messages > 0) { ?>
                        <?php do {
                            $dated = date("jS M, h:ia", strtotime($row_messages['dated']));

                            ?>
                            <span class="badge <?php
                                echo ($row_messages['from_user_id'] == $_SESSION['MM_ID']) ? "pull-left message_date_first" : "badge-info pull-left message_date"; ?>">
                                <?php
                                echo ($row_messages['from_user_id'] == $_SESSION['MM_ID']) ? "You" : $row_messages['sender'];
                                echo " (" . $dated . ")"; ?></span>
                            <p class="<?php
                            echo ($row_messages['from_user_id'] == $_SESSION['MM_ID']) ? "clear" : ""; ?> line-border-top message-p"><?php echo $row_messages['message']; ?></p>
                        <?php } while ($row_messages = mysql_fetch_assoc($messages)); ?>
                    <?php } ?>

                    <form class="mws-form" action="message_read.php" method="get">
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