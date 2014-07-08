<?php include_once("initialize.php");
mysql_select_db($database_fiber, $fiber);
if (isset($_SESSION['MM_ID'])) {
    $colname_user_id = $_SESSION['MM_ID'];
}
// start Dismas Change selecting bureaus
$id = mysql_real_escape_string(trim($_GET['id']));
// end Dismas chang
$query_details = sprintf(" SELECT bureaus_name, bureaus_id, officephone, cellphone, email, street, city, premises, last_update as dated, bureaus_user
FROM bureaus
		where bureaus_id = %s",
    GetSQLValueString($_GET['id'], "int"));
$details = mysql_query($query_details, $fiber) or die(mysql_error());
$row_details = mysql_fetch_assoc($details);
$totalRows_details = mysql_num_rows($details);
?>

            <div class="mws-panel">
                <div class="mws-panel-header">
                    <span><?php echo $row_details['bureaus_name']; ?></span>
                </div>
                <div class="mws-panel-body no-padding">
                    <ul class="mws-summary clearfix">
                        <li>
                            <span class="key">Premises</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['premises']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Street</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['street']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">City</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['city']; ?></span>
                                </span>
                        </li>
                        <li>
                            <span class="key">Rating</span>
                                <span class="val">
                                    <span class="text-nowrap"><?php echo $row_details['email']; ?></span>
                                </span>
                        </li>
                    </ul>
                </div>
            </div>

