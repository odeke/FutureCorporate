<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_fiber = "localhost";
$database_fiber = "inforex_uganda";
$username_fiber = "root";
$password_fiber = "";
$fiber = mysql_pconnect($hostname_fiber, $username_fiber, $password_fiber) or trigger_error(mysql_error(),E_USER_ERROR);

mysql_select_db($database_fiber, $fiber);
?>