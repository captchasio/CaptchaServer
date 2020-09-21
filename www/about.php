<?php
	ini_set('display_errors', 1);
	error_reporting(E_ERROR);
	
	require_once('system/CaptchaServer.class.php');

	$server = new CaptchaServer();
	
	$version = $server->get_version();
	$db = $server->get_db();
	
	$images = array($server->get_images() => 'selected');
	$recaptcha = array($server->get_recaptcha() => 'selected');
	$funcaptcha = array($server->get_funcaptcha() => 'selected');
	$text = array($server->get_text() => 'selected');
	$audio = array($server->get_audio() => 'selected');
	
	$key = $server->get_key();
	$secret = $server->get_secret();	
	
	if (isset($_POST['submit']) && !empty($_POST['submit'])) {
		$config = array("key" => $_POST['key'], "secret" => $_POST['secret'], "recaptcha" => $_POST['recaptcha'], "images" => $_POST['images'], "text" => $_POST['text'], "audio" => $_POST['audio'], "funcaptcha" => $_POST['funcaptcha']);
		
		$server->save_config($config, dirname(__FILE__) . "/system/config.ini");
		
		$images = array($server->get_images() => 'selected');
		$recaptcha = array($server->get_recaptcha() => 'selected');
		$funcaptcha = array($server->get_funcaptcha() => 'selected');
		$text = array($server->get_text() => 'selected');
		$audio = array($server->get_audio() => 'selected');
		
		$key = $server->get_key();
		$secret = $server->get_secret();										
	}	
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>About</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <!--  Material Dashboard CSS    -->
    <link href="assets/css/material-dashboard.css?v=1.2.0" rel="stylesheet" />
    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="assets/css/demo.css" rel="stylesheet" />
    <!--     Fonts and icons     -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons' rel='stylesheet' type='text/css'>
	
	<script src="assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="assets/js/material.min.js" type="text/javascript"></script>	
</head>

<body>
    <div class="wrapper">
        <div class="sidebar" data-color="purple" data-image="assets/img/sidebar-1.jpg">
            <div class="logo">
                <a href="index.php" class="simple-text">
                    Captcha Server
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <a href="index.php">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="material-icons">person</i>
                            <p>Settings</p>
                        </a>
                    </li>
                    <li>
                        <a href="documentation.php">
                            <i class="material-icons">developer_mode</i>
                            <p>Documentation</p>
                        </a>
                    </li>					
                    <li class="active">
                        <a href="about.php">
                            <i class="material-icons">content_paste</i>
                            <p>About</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <nav class="navbar navbar-transparent navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="about.php">About</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header" data-background-color="green">
                                    <h4 class="title">About Captcha Server</h4>
                                </div>								
                                <div class="card-content">
									<h4><b>What</b></h4>
									<div>
										<p><b><?=$version?></b> is a stable and fast captcha solving server platform built by one of the best captcha solving services online. How it works? Ver very simply... CaptchaServer provides you with an API to solve captchas. Accessing the API is very easy and the response is fast. See <a href="documentation.php"><u>documentation</u></a> for more information on how to use the API service.</p>
									</div>
									<h4><b>Who</b></h4>
									<div>
										<p>Created with love and passion to make captcha solving better, CAPTCHAs.IO is the company that made better our software CaptchaServer.</p>
									</div>	
									<hr>
									<div>
										<p>Version: <?=$version?></p>
										<p>Copyright: CAPTCHAs.IO</p>
										<p>Website: <a href="http://captchas.io/" target="_new">http://captchas.io/</a></p>
										<p>Email: admin@captchas.io</p>
										<p>Skype: isnare.glenn</p>
									</div>										
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <nav class="pull-left">
                        <ul>
                            <li>
								 <a href="https://www.captchaservers.com/" target="_new"><?=$version?></a>
                            </li>
                        </ul>
                    </nav>
                    <p class="copyright pull-right">
                        &copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                        Made with love by <a href="http://captchas.io/" target="_new">CAPTCHAs.IO</a> for a better captcha solving
                    </p>
                </div>
            </footer>
        </div>
    </div>
</body>

<!--  Charts Plugin -->
<script src="assets/js/chartist.min.js"></script>
<!--  Dynamic Elements plugin -->
<script src="assets/js/arrive.min.js"></script>
<!--  PerfectScrollbar Library -->
<script src="assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--  Notifications Plugin    -->
<script src="assets/js/bootstrap-notify.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Material Dashboard javascript methods -->
<script src="assets/js/material-dashboard.js?v=1.2.0"></script>
</html>