<?php
require_once('Connections/fiber.php');
include_once("common.php");

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=mysql_real_escape_string($_POST['password']);
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "dashboard.php";
  $MM_redirectLoginFailed = "index.php?error=Invalid credentials";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_fiber, $fiber);

  //enter the blowfish
////This string tells crypt to use blowfish for 5 rounds.
//    $Blowfish_Pre = '$2a$13$';
//    $Blowfish_End = '$';
//
//    // Blowfish accepts these characters for salts.
//    $Allowed_Chars =
//        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
//    $Chars_Len = 63;
//
//// 18 would be secure as well.
//    $Salt_Length = 21;
//
//    $mysql_date = date( 'Y-m-d' );
//    $salt = "";
//
//    for($i=0; $i<$Salt_Length; $i++)
//    {
//        $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
//    }
//    $hashed_pass = crypt($password, $Blowfish_Pre . $salt . $Blowfish_End);

  //company accounts are type 3
  //$LoginRS__query=sprintf("SELECT bureaus_id,bureaus_name, bureaus_user, last_seen, home_curr  FROM bureaus WHERE bureaus_user=%s AND bureaus_pass=%s and account_type = 3",
   // GetSQLValueString($loginUsername, "text"), GetSQLValueString(md5($password), "text"));
    $LoginRS__query=sprintf("SELECT bureaus_id, bureaus_name, bureaus_user, last_seen, home_curr, active_curr, currency_abbr, currency_name, salt, bureaus_pass FROM bureaus
        left join currencies on bureaus.active_curr = currencies.currency_id
        WHERE bureaus_user=%s and account_type = 3",
    GetSQLValueString($loginUsername, "text"));
   
  $LoginRS = mysql_query($LoginRS__query, $fiber) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    $row_login = mysql_fetch_assoc($LoginRS);
    if(crypt($_POST['password'], $row_login['salt']) == $row_login['bureaus_pass']) {
        $loginStrGroup = "";
        if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
        //declare two session variables and assign them
        $_SESSION['MM_Username'] = $loginUsername;
        $_SESSION['MM_UserGroup'] = $loginStrGroup;
        $_SESSION['MM_ID'] = $row_login['bureaus_id'];
        $_SESSION['MM_Name'] = $row_login['bureaus_name'];
        $_SESSION['MM_homeCurrency'] = $row_login['home_curr'];
        $_SESSION['MM_activeCurrencyID'] = $row_login['active_curr'];
        $_SESSION['MM_activeCurrencyAbbr'] = $row_login['currency_abbr'];
        $_SESSION['MM_activeCurrencyName'] = $row_login['currency_name'];
        $_SESSION['MM_last_seen'] = $row_login['last_seen'];

        //save the session  id in db
        $LoggedIn__query=sprintf("UPDATE bureaus set bureaus_token = %s, last_seen = %s where bureaus_user = %s and account_type = 3",
            GetSQLValueString(session_id(), "text"), GetSQLValueString(date("Y-m-d H:i:s"), "text"), GetSQLValueString($loginUsername, "text"));

        $LoggedIn = mysql_query($LoggedIn__query, $fiber) or die(mysql_error());

        //send em whence they came
        if (isset($_SESSION['PrevUrl']) && false) {
            $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
        }
        header("Location: " . $MM_redirectLoginSuccess );
    } else {
        header("Location: ". $MM_redirectLoginFailed );
    }

  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<!-- Viewport Metatag -->
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<!-- Required Stylesheets -->
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="css/fonts/ptsans/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="css/fonts/icomoon/style.css" media="screen">

<link rel="stylesheet" type="text/css" href="css/login.css" media="screen">

<link rel="stylesheet" type="text/css" href="css/mws-theme.css" media="screen">

<title>Inforex Corporate</title>

</head>

<body>

    <div id="mws-login-wrapper">
        <div id="mws-login">
            <h1>Corporate Access</h1>
            <div class="mws-login-lock"><i class="icon-lock"></i></div>
            <div id="mws-login-form">
                <form class="mws-form" action="<?php echo $loginFormAction; ?>" method="POST" name="loginfrm">
                    <div class="mws-form-row">
                        <div class="mws-form-item">
                            <img src="images/logoWhite.png" />
                        </div>
                    </div>
                    <?php if (isset($_GET['error']) ) { ?>
                    <div class="mws-form-message error">
                        <?php echo $_GET['error']; ?>
                    </div>
                    <?php } ?>
                    <div class="mws-form-row">
                        <div class="mws-form-item">
                            <input type="text" name="username" class="mws-login-username required" placeholder="username">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <div class="mws-form-item">
                            <input type="password" name="password" class="mws-login-password required" placeholder="password">
                        </div>
                    </div>
                    <!-- <div id="mws-login-remember" class="mws-form-row mws-inset">
                        <ul class="mws-form-list inline">
                            <li>
                                <input name="remember_me" type="checkbox" id="remember"> 
                                <label for="remember">Remember me</label>
                            </li>
                        </ul>
                    </div> -->
                    <div class="mws-form-row">
                        <input name="login_btn" type="submit" class="btn btn-success mws-login-button" id="login_btn" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Plugins -->
    <script src="js/libs/jquery-1.8.3.min.js"></script>
    <script src="js/libs/jquery.placeholder.min.js"></script>
    <script src="custom-plugins/fileinput.js"></script>
    
    <!-- jQuery-UI Dependent Scripts -->
    <script src="jui/js/jquery-ui-effects.min.js"></script>

    <!-- Plugin Scripts -->
    <script src="plugins/validate/jquery.validate-min.js"></script>

    <!-- Login Script -->
    <script src="js/core/login.js"></script>

</body>
</html>