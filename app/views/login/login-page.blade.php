<?php
//error_reporting(E_ALL ^ E_NOTICE);
/*
session_start(); // Start Session
header('Cache-control: private'); // IE 6 FIX
 
// always modified
header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
// HTTP/1.1
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
// HTTP/1.0
header('Pragma: no-cache');
 
$cookieName = 'Inq_Auth';
$cookieTime = (3600 * 24 * 30); // 30 days

$action = "";
if(isset($_GET["action"]))    $action = $_GET["action"];
if(isset($_POST["action"]))   $action = $_POST["action"];
if(strtolower($action) == "logout")	{
	if (isset($_COOKIE[$cookieName])) {
    unset($_COOKIE[$cookieName]);
    setcookie($cookieName, '', time() - 3600, '/'); // empty value and old timestamp
	}
	session_destroy();
	header("Location:index.php");
}
*/
// ---------- Invoke Auto-Login if no session is registered ---------- //
/*
if(!$_SESSION['LOGIN_ID'] && $action == "") {
  if(isset($cookieName)) {
    // Check if the cookie exists
    if(isset($_COOKIE[$cookieName])) {
      parse_str($_COOKIE[$cookieName]);
  
      // Make a verification
      $verifyResult = json_decode(verifyStoredLogin($cookieEmail,$hash,$gConn));
      if($verifyResult->{"RESULT"}  === "OK") $_SESSION['LOGIN_ID'] = $cookieEmail;
    }
  }
}
*/
//$ctlMsg["ERROR"] = "";
/*
if(isset($_POST['btnSubmit'])) {
  $loginId = trim(antiInjection($_POST['loginEmail']));
  $loginPassword = trim(antiInjection($_POST['loginPassword']));
  //$loginRememberMe = antiInjection($_POST['loginRememberMe']);
  $loginRememberMe = 0;

  $loginResult = json_decode(checkLogin($loginId,$loginPassword,$gConn));
  //print_r($loginResult);
  if($loginResult->{"RESULT"} == "OK") {
    $_SESSION['LOGIN_ID'] = trim($loginId);
    $_SESSION['LOGIN_NAME'] = $loginResult->{"LOGIN_NAME"};
    $_SESSION['LOGIN_GROUP'] = $loginResult->{"LOGIN_GROUP"};

    // Autologin Requested?
    if($loginRememberMe == 1 || $loginRememberMe == "on") {
      $passwordHash = md5($loginId.$loginPassword); // will result in a 32 characters hash
      setcookie ($cookieName, 'cookieEmail='.$loginId.'&hash='.$passwordHash, time() + $cookieTime);
    }
  }
  else {
    $ctlMsg["ERROR"] = $loginResult->{"MESSAGE"};
  }
}
*/
/*
if(isset($_SESSION['LOGIN_ID'])) {
  header("Location: ../index.php");
  exit;
}
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?php echo asset_url(); ?>/assets/images/pintech.png">
	<title><?php echo getSetting("APP_NAME"); ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="<?php echo asset_url(); ?>/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="<?php echo asset_url(); ?>/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="<?php echo asset_url(); ?>/assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="<?php echo asset_url(); ?>/assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="<?php echo asset_url(); ?>/assets/css/colors.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/app.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/login.js"></script>
	<!-- /theme JS files -->

	<!-- sweet alert-->
	<script src="<?php echo asset_url(); ?>/assets/js/sweetalert/sweetalert.min.js"></script>
  <link rel="stylesheet" href="<?php echo asset_url(); ?>/assets/js/sweetalert/sweetalert.css">

</head>

<body onLoad="onLoad()">
	<div class="navbar navbar-inverse" style="background-color: #bb0a0a !important">
		<!--
		<div class="navbar-header">
			<a class="navbar-brand" href="#"><img src="../assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>
		//-->
		<!--
		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="#">
						<i class="icon-display4"></i> <span class="visible-xs-inline-block position-right"> Go to website</span>
					</a>
				</li>

				<li>
					<a href="#">
						<i class="icon-user-tie"></i> <span class="visible-xs-inline-block position-right"> Contact admin</span>
					</a>
				</li>

				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-cog3"></i>
						<span class="visible-xs-inline-block position-right"> Options</span>
					</a>
				</li>
			</ul>
		</div>
		-->
	</div>

	<!-- Page container -->
	<div class="page-container login-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content">
					<form action="login" method="post">
						<div class="panel panel-body login-form">
							<div class="text-center">
								<img src="<?php echo asset_url(); ?>/assets/images/pintech.png" style="width: 270px;"></i>
								<h4 class="content-group">Login PINtech</h4>
							</div>

							<div class="form-group has-feedback has-feedback-left">
								<input type="text" class="form-control" placeholder="Username / Email / Ponsel" name="loginEmail" id="loginEmail" onkeyup="this.value = this.value.toUpperCase();">
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
								<!--
								<div class="input-group">
		              <span class="input-group-addon"></span>
		              <input type="text" class="form-control" placeholder="User ID" name="loginEmail" id="loginEmail">
		            </div> 
		            -->           
							</div>

							<div class="form-group has-feedback has-feedback-left">
								<div class="input-group">
									<input type="password" class="form-control" placeholder="Password" name="loginPassword" id="loginPassword">
									<span class="input-group-addon"><!-- <input type="checkbox" id="showPass"> --><i class="icon-eye" id="showPass"></i></span>
								</div>
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
							</div>

							<div class="form-group login-options">
								<div class="row">
									<!--
									<div class="col-sm-6">
										<label class="checkbox-inline">
											<input type="checkbox" class="styled" checked="checked" name="loginRememberMe" id="loginRememberMe">
											Ingat data saya
										</label>
									</div>
									-->									
								</div>
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-block" name="btnSubmit" style="background-color: #bb0a0a; color: #fff">Login <i class="icon-arrow-right14 position-right" ></i></button>
							</div>
						</div>
					</form>

					<div class="footer text-muted"></div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		function onLoad() {
			$("#loginEmail").focus();
			
			<?php
			if(Session::has("ctlError")) {
				?>
				sweetAlert("Error Message", "<?php echo Session::get('ctlError'); ?>", "error");
				<?php
			}
			?>
		}
	</script>
	 <script type="text/javascript">
          $('#showPass').click(function() {
			if ($(this).hasClass('icon-eye')) {
				$('#loginPassword').attr('type', 'text');
				$(this).removeClass('icon-eye');
				$(this).addClass('icon-eye-blocked');
			} else {
				$('#loginPassword').attr('type', 'password');
				$(this).removeClass('icon-eye-blocked');
				$(this).addClass('icon-eye');
			}
		});
      </script>
</body>
</html>

