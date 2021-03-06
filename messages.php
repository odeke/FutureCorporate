<?php require_once('Connections/fiber.php'); ?>
<?php include_once("initialize.php");
$colname_user_id = -1;
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
//dismas to save messages******************************************************************************************
if (isset($_POST['send_message'])){
    $date = date('Y-m-d H:i:s', time());

    $q_threads = sprintf("INSERT INTO `msg_threads`(`sender_id`, last_updated) VALUES (%s, %s)",
        GetSQLValueString($msg_id, "int"),
        GetSQLValueString($date, "text")
    );
    $qe_threads = mysql_query($q_threads, $fiber) or die("error: creating thread: ".mysql_error());

    $thread_id = mysql_insert_id();

    $q_send_msg = sprintf("INSERT INTO `messages` (`user_id`, `from_user_id`, `message`, `dated`, `box`, thread_id) VALUES (%s,%s,%s,%s,%s,%s)",
        GetSQLValueString($_POST['to_who'], "text"),
        GetSQLValueString($_SESSION['MM_ID'], "int"),
        GetSQLvalueString($_POST['message'], "text"),
        GetSQLvalueString($date, "text"),
        GetSQLvalueString('Inbox', "text"),
        GetSQLvalueString($thread_id, "int")
        );
    $q_send_msg = mysql_query($q_send_msg, $fiber) or die("error: sending message: ".mysql_error());
}


//dismas end sending messages**************************************************************************************

//deletion request
if (isset($_SESSION['msg_id_del'])) {
    $q_inbox = sprintf(" SELECT thread_id, msg_type, msg_status, from_user_id, user_id, msg_id, msg_status, msg_status_sender
        FROM messages
        WHERE `msg_id` = %s and msg_type = 0 order by dated DESC limit 1",
        GetSQLValueString($_SESSION['msg_id_del'], "int")
    );
    $q_inbox = mysql_query($q_inbox, $fiber) or die(mysql_error());
    $row_msg = mysql_fetch_assoc($q_inbox);

    //check to see who is the msg sender
    if($row_msg['from_user_id'] == $_SESSION['MM_ID']){
        $sender = "from_user_id";
        $sender_status = "msg_status_sender";
    } elseif($row_msg['user_id'] == $_SESSION['MM_ID']) {
        $sender = "user_id";
        $sender_status = "msg_status";
    } else {
        header("Location: messages.php?error=Failed to Delete");
        exit;
    }

    $query_message_del = sprintf("UPDATE messages set ". $sender_status ." = %s
    WHERE ". $sender ." = %s and thread_id=%s",
        GetSQLValueString('Deleted', "text"),
        GetSQLValueString($row_msg['user_id'], "int"),
        GetSQLValueString($row_msg['thread_id'], "int"));
    $message_del = mysql_query($query_message_del, $fiber) or die(mysql_error());
}

// dismas processing unread messages *************************************************************************

$new_table_rows="";

    $q_inbox = sprintf("select message, dated, user_id, from_user_id, messages.thread_id, bureau_sender.bureaus_name as sender, bureau_recipient.bureaus_name as recipient from messages
        LEFT JOIN bureaus bureau_sender ON messages.from_user_id = bureau_sender.bureaus_id
        LEFT JOIN bureaus bureau_recipient ON messages.`user_id` = bureau_recipient.bureaus_id
        left join msg_threads on messages.thread_id = msg_threads.thread_id
        where `user_id` = %s or from_user_id = %s and `msg_type` = 0 and sender_status =1
        group by thread_id order by `dated` DESC",
        GetSQLValueString($_SESSION['MM_ID'], "int"),
        GetSQLValueString($_SESSION['MM_ID'], "int")
    ); //echo $q_inbox;
    $q_inbox = mysql_query($q_inbox, $fiber) or die(mysql_error());
    $q_threads_count = mysql_num_rows($q_inbox);

    while($row_inbox = mysql_fetch_assoc($q_inbox)){
        $sender_receiver = ($row_inbox['from_user_id'] == $_SESSION['MM_ID']) ? $row_inbox['recipient'] : $row_inbox['sender'];
        $mess = substr($row_inbox['message'], 0, 80);
        $mess .= (strlen($row_inbox['message']) > 80) ? " ..." : "";

        $new_table_rows .= '<tr class="new_message">';
        $new_table_rows .= '<td>'.$sender_receiver .'</td>';
        $new_table_rows .= '<td><a href="message_read.php?thread_id='.$row_inbox['thread_id'].'">'.$mess.'</a></td>';
        $new_table_rows .= '<td>'.$row_inbox['dated'].'</td>';
        $new_table_rows .= '</tr>';
    }

mysql_select_db($database_fiber, $fiber);
$query_bureaus = "SELECT bureaus_name, bureaus_id FROM bureaus where account_type=1";
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
                        <span><i class="icon-table"></i> Messages</span>
                    </div>
                    <div class="mws-panel-toolbar">
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <a href="#" class="btn" id="mws-form-dialog-mdl-btn"><i class="icol-pencil"></i> New message</a>
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
                                <th>With</th>
                                <th>Messages</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($q_threads_count > 0) echo $new_table_rows; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="mws-form-dialog">
                    <form id="mws-validate" class="mws-form" method="post" action="messages.php">
                        <input type="hidden" name="send_message" id="send_message"/><!-- dismas inculded this to detect if a send message form was sent-->
                        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
                        <div class="mws-form-inline">
                            <div class="mws-form-row">
                                <label class="mws-form-label">To</label>
                                <div class="mws-form-item">
                                    <select class="required" name="to_who" id="to_who">
                                        <option value="" label="">-- Select a recipient --</option>
                                        <?php
                                        do {
                                            ?>
                                            <option value="<?php echo $row_bureaus['bureaus_id']?>" label="<?php echo $row_bureaus['bureaus_name']?>"><?php echo $row_bureaus['bureaus_name']?></option>
                                        <?php
                                        } while ($row_bureaus = mysql_fetch_assoc($bureaus));
                                        $rows = mysql_num_rows($bureaus);
                                        if($rows > 0) {
                                            mysql_data_seek($bureaus, 0);
                                            $row_bureaus = mysql_fetch_assoc($bureaus);
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mws-form-row hide" >
                                <label class="mws-form-label">Subject</label>
                                <div class="mws-form-item">
                                    <input type="text" name="subject" class="required large" value="default" />
                                </div>
                            </div>
                            <div class="mws-form-row">
                                <label class="mws-form-label">Message</label>
                                <div class="mws-form-item">
                                    <textarea rows="" cols="" class="large required" name="message"></textarea>
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