<?php
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
mysql_select_db($database_fiber, $fiber);

/*
$query_messages1 = sprintf(" SELECT thread_id,box, message, dated, msg_type, msg_status, from_user_id, user_id, bureau_sender.bureaus_name AS sender,
	bureau_recipient.`bureaus_name` AS recipient, msg_id, msg_status, msg_status_sender
    FROM messages
    LEFT JOIN bureaus bureau_sender ON messages.from_user_id = bureau_sender.bureaus_id
    LEFT JOIN bureaus bureau_recipient ON messages.`user_id` = bureau_recipient.bureaus_id
    WHERE user_id = %s OR from_user_id = %s GROUP BY thread_id ORDER BY msg_id DESC",
    GetSQLValueString($colname_user_id, "int"),
    GetSQLValueString($colname_user_id, "int"));
$messages1 = mysql_query($query_messages1, $fiber) or die(mysql_error());
$row_messages1 = mysql_fetch_assoc($messages1);
$totalRows_messages1 = mysql_num_rows($messages1);
 
$query_messages2 = sprintf(" SELECT thread_id,box, message, dated, msg_type, msg_status, from_user_id, user_id, bureau_sender.bureaus_name AS sender,
	bureau_recipient.`bureaus_name` AS recipient, msg_id, msg_status, msg_status_sender
    FROM messages
    LEFT JOIN bureaus bureau_sender ON messages.from_user_id = bureau_sender.bureaus_id
    LEFT JOIN bureaus bureau_recipient ON messages.`user_id` = bureau_recipient.bureaus_id
    WHERE user_id = %s OR from_user_id = %s and msg_status='Unread' GROUP BY thread_id ORDER BY msg_id DESC",
    GetSQLValueString($colname_user_id, "int"),
    GetSQLValueString($colname_user_id, "int"));
$messages2 = mysql_query($query_messages2, $fiber) or die(mysql_error());

*/
//support msgs preview
$q_tickets = "select distinct tickets.`thread_id` from tickets
    left join ticket_threads on tickets.thread_id = ticket_threads.thread_id
    where `user_id` = $colname_user_id and `thread_status` = 1 order by ticket_threads.last_updated DESC";
$q_tickets = mysql_query($q_tickets, $fiber) or die("Error getting unread support tickets" . mysql_error());
$totalRows_tickets_preview = mysql_num_rows($q_tickets);

/*dismas msg preview*/
$new_table_li="";

$q_threads_un = "select distinct `thread_id` from messages where `user_id` = $colname_user_id and `msg_status` = 'Unread' order by `thread_id` DESC";
$q_threads_un = mysql_query($q_threads_un, $fiber) or die("Error getting unread threads" . mysql_error());
$totalRows_messages2 = mysql_num_rows($q_threads_un);

while($q_thread_ids = mysql_fetch_assoc($q_threads_un)){
    $next_thread = $q_thread_ids['thread_id'];
$q_inbox = sprintf("SELECT thread_id, box, message, dated, msg_type, msg_status, from_user_id, user_id, bureau_sender.bureaus_name AS sender,
	bureau_recipient.`bureaus_name` AS recipient, msg_id, msg_status, msg_status_sender
    FROM messages
    LEFT JOIN bureaus bureau_sender ON messages.from_user_id = bureau_sender.bureaus_id
    LEFT JOIN bureaus bureau_recipient ON messages.`user_id` = bureau_recipient.bureaus_id
    WHERE `thread_id` = %s order by dated DESC limit 1",
    GetSQLValueString($next_thread, "int")
);
$q_inbox = mysql_query($q_inbox, $fiber) or die(mysql_error());
$row_inbox = mysql_fetch_assoc($q_inbox);

$sender_receiver = ($row_inbox['from_user_id'] == $_SESSION['MM_ID']) ? $row_inbox['recipient'] : $row_inbox['sender'];
$mess = substr($row_inbox['message'], 0, 30); $mess .= (strlen($row_inbox['message']) > 20) ? " ..." : "";
$sender_icon = ($row_inbox['msg_status'] == "Unread") ? "unread" : "read";

$new_table_li .= '<li class="'.$sender_icon.'">';
$new_table_li .= '<a href="message_read.php?thread_id='.$row_inbox['thread_id'].'">';
$new_table_li .= '<span class="sender">'.$sender_receiver.'</span>';
$new_table_li .= '<span class="message">'.$mess.'</span>';
$new_table_li .= '<span class="time">'.$row_inbox['dated'].'</span>';
$new_table_li .= '</a></li>';

}


?>
<div id="mws-user-message" class="mws-dropdown-menu">
                <a href="#" data-toggle="dropdown" class="mws-dropdown-trigger"><i class="icon-envelope"></i></a>

                <!-- Unred messages count -->
                <span class="mws-dropdown-notif"><?php echo $totalRows_messages2; ?></span>

                <!-- Messages dropdown -->
                <div class="mws-dropdown-box">
                    <div class="mws-dropdown-content">
                        <ul class="mws-messages">
                            <?php if ($totalRows_messages2 > 0) echo $new_table_li; ?>

                        </ul>
                        <div class="mws-dropdown-viewall">
                            <a href="messages.php">View All Messages</a>
                        </div>
                    </div>
                </div>
            </div>