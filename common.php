<?php
/**
 * Common functions
 */
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Africa/Kampala');
$session_timeout = 3600; //15mins timeout.
if (!isset($_SESSION)) {
    //session_set_cookie_params(10);
    session_start();
}
if (!isset($_SESSION['timeout_idle'])) {
    $_SESSION['timeout_idle'] = time() + $session_timeout;
} else {
    if ($_SESSION['timeout_idle'] < time()) {
        $_SESSION = array();
        session_destroy();
    } else {
        $_SESSION['timeout_idle'] = time() + $session_timeout;
    }
}

//format date human readable
function relativeTime($timestamp){
    if(strtotime($timestamp) == 0){
        return "Never";
    }
    $difference = time() - strtotime($timestamp);
    if(!is_numeric($difference)){
        return "Never";
    }
    $periods = array("sec", "min", "hour", "day", "week", "month", "years", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");

    if ($difference > 0) { // this was in the past
        $ending = "ago";
    } else { // this was in the future
        $difference = -$difference;
        $ending = "to go";
    }
    $j = 0;
    while($difference >= $lengths[$j] && $j < count($lengths)) {
        $difference /= $lengths[$j];
        $j++;
    }

    $difference = round($difference);
    if($difference != 1) $periods[$j].= "s";
    $text = "$difference $periods[$j] $ending";
    return $text;
}

//DB input cleansing
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
    if (PHP_VERSION < 6) {
        $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
        case "text":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
        case "long":
        case "int":
            $theValue = ($theValue != "") ? intval($theValue) : "NULL";
            break;
        case "double":
            $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
            break;
        case "date":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
        case "defined":
            $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
            break;
    }
    return $theValue;
}