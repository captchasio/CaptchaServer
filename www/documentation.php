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
    <title>Documentation</title>
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
                    <li class="active">
                        <a href="documentation.php">
                            <i class="material-icons">developer_mode</i>
                            <p>Documentation</p>
                        </a>
                    </li>					
                    <li>
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
                        <a class="navbar-brand" href="documentation.php">Documentation</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header" data-background-color="blue">
                                    <h4 class="title">Developer API Document</h4>
                                </div>								
                                <div class="card-content">
									<h4><b>Introduction</b></h4>
									<div>
										<p>This document talks about the CaptchaServer API which end users and developers will use to SOLVE CAPTCHAs using the CaptchaServer system. The document will contain API usage examples and request parameters to make captcha solving easy and integration seemless.</p>
										<p>
											There are few simple steps to solve your captcha or recognize the image:

											<ul>
												<li>Send your image or captcha to the server.</li>
												<li>Get the ID of your task.</li>
												<li>Start a cycle that checks if your task is completed.</li>
												<li>Get the result.</li>
											</ul>	
										</p>
									</div>
									<h4><b>API</b></h4>
									<div>
										<p>The API is a simple <i>RESTful HTTP</i> web service platform and it is patterned from the <i>2captcha.com</i> clone API system.</p>
									</div>	
									<h4><b>Authentication</b></h4>
									<div>
										<p>You authenticate to the API by using the <i>Local API Key</i> which you have set in your settings.</p>
									</div>		
									<h4><b>Service Endpoints</b></h4>
									<div>
										<p><span class="text-info">http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/in.php</span> - For uploading your captcha images.</p>
										<p><span class="text-info">http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/res.php</span> - For getting the captcha answers.</p>
									</div>	
									<h4><b>Parameters</b></h4>
									<div>
										<div style="margin-bottom: 20px;">
											<div>Endpoint: <i>http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/in.php</i></div>
											<table class="table table-bordered table-hover">
												<thead>
													<tr>											
														<td><b>Name</b></td>
														<td><b>Value</b></td>
													</tr>	
												</thead>
												<tbody>
													<tr>
														<td>key</td>
														<td>Local API key value</td>
													</tr>	
													<tr>
														<td>method</td>
														<td>"post" or "userrecaptcha" or "hcaptcha"</td>
													</tr>
													<tr>
														<td>file</td>
														<td>Ex: @/path/to/captcha/image.png</td>
													</tr>												
													<tr>
														<td>googlekey</td>
														<td>The Google Key</td>
													</tr>
													<tr>
														<td>sitekey</td>
														<td>The data-sitekey</td>
													</tr>													
													<tr>
														<td>pageurl</td>
														<td>The URL where googlekey or sitekey is found</td>
													</tr>	
													<tr>
														<td>invisible</td>
														<td>"1" or "0" ~ used for solving recaptcha</td>
													</tr>	
													<tr>
														<td>version</td>
														<td>"v2" or "v3" ~ used for solving recaptcha</td>
													</tr>	
													<tr>
														<td>action</td>
														<td>"verify" ~ used for solving recaptcha</td>
													</tr>	
													<tr>
														<td>proxy</td>
														<td>"123.34.234.1:8080" ~ used for solving recaptcha</td>
													</tr>	
													<tr>
														<td>proxytype</td>
														<td>"HTTP", "HTTPS", "SOCKS4", "SOCKS5" ~ used for solving recaptcha</td>
													</tr>													
												</tbody>
											</table>
										</div>
										<div style="margin-top: 10px;">
											<div>Endpoint: <i>http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/res.php</i></div>
											<table class="table table-bordered table-hover">
												<thead>
													<tr>											
														<td><b>Name</b></td>
														<td><b>Value</b></td>
													</tr>	
												</thead>
												<tbody>
													<tr>
														<td>key</td>
														<td>Local API key value</td>
													</tr>	
													<tr>
														<td>action</td>
														<td>"get"</td>
													</tr>
													<tr>
														<td>id</td>
														<td>The captcha ID Ex: 1235634</td>
													</tr>													
												</tbody>
											</table>
										</div>										
									</div>
									<h4><b>Usage Examples</b></h4>
									<div>
										<p>Uploading captcha images for solving is very easy, simply:</p>
										<p>Call an HTTP request to this API endpoint with the parameters as shown in this example: <span class="text-info">http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/in.php?method=<b>post</b>&file=<b>@/path/to/image/captcha.png</b>&key=<b>{LOCAL_API_KEY}</b></span></p>
										<p>Example response: <b>OK|876340</b> where 876340 is the captcha ID used below.</p>
										<p>To call for captcha answer with captcha ID, simply:</p>
										<p>Call an HTTP request to this API endpoint with the parameters as shown in this example: <span class="text-info">http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/res.php?action=<b>get</b>&key=<b>{LOCAL_API_KEY}</b>&id=<b>876340</b></span></p>
										<p>Example response: <b>OK|hJyu4</b> where hJyu4 is the captcha answer.</p>
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