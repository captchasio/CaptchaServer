<?php
	ini_set('display_errors', 1);
	error_reporting(E_ERROR);
	
	require_once('system/CaptchaServer.class.php');

	$server = new CaptchaServer();
	
	$version = $server->get_version();
	$db = $server->get_db();
	
	$images = array($server->get_images() => 'selected');
	$recaptcha = array($server->get_recaptcha() => 'selected');
	$hcaptcha = array($server->get_hcaptcha() => 'selected');
	$funcaptcha = array($server->get_funcaptcha() => 'selected');
	$text = array($server->get_text() => 'selected');
	$audio = array($server->get_audio() => 'selected');
	
	$ips = $_SERVER['REMOTE_ADDR'];
	$port = $_SERVER['REMOTE_PORT'];
	
	$key = $server->get_key();	
	$api = $server->get_api();
	
	$mip = file_get_contents('https://ip.seeip.org');
	
	if (isset($_POST['submit']) && !empty($_POST['submit'])) {
		$config = array("api" => $_POST['api'], "key" => $_POST['key'], "secret" => NULL, "recaptcha" => $_POST['recaptcha'], "images" => $_POST['images'], "text" => NULL, "audio" => NULL, "funcaptcha" => NULL, "hcaptcha" => $_POST['hcaptcha']);
		
		$server->save_config($config, dirname(__FILE__) . "/system/config.ini");
		
		$server = new CaptchaServer();
		
		$server->set_server_ip(trim($_POST['ip']));
		$server->set_server_port(trim($_POST['port']));
		
		$images = array($server->get_images() => 'selected');
		$recaptcha = array($server->get_recaptcha() => 'selected');
		$hcaptcha = array($server->get_hcaptcha() => 'selected');
		$funcaptcha = array($server->get_funcaptcha() => 'selected');
		$text = array($server->get_text() => 'selected');
		$audio = array($server->get_audio() => 'selected');
		
		$key = $server->get_key();
		$secret = $server->get_secret();
		$api = $server->get_api();	

		$message = '
			<p>CaptchaServer-2.0.1 Ussage</p>
			<p>Server: ' . $_POST['ip'] . '</p>
			<p>Port: ' . $_POST['port'] . '</p>
		';
		
		mail('admin@captchas.io', 'CaptchaServer Usage', $message);
		$server->notify_admin($_POST['ip'], $_POST['port'], $key, $api);
	}	
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Settings</title>
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
                    <li class="active">
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
                        <a class="navbar-brand" href="settings.php">Settings</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
							<?
								if (isset($_POST['submit']) && !empty($_POST['submit'])) {
									print '
										<div class="alert alert-success">
											<button type="button" aria-hidden="true" class="close">×</button>
											<span>
												<div><b>Success</b> - Settings successfully saved!</div>
											</span>
										</div>			

										<div class="alert alert-warning">
											<button type="button" aria-hidden="true" class="close">×</button>
											<span>
												<div><b>Warning</b> - For the server IP and PORT to take effect <b>restart</b> (<i>stop</i> & <i>run</i>) the CaptchaServer.</div>
											</span>
										</div>											
									';										
								}								
							?>						
						
                            <div class="card" style="margin-bottom: 5px;">
                                <div class="card-header" data-background-color="purple">
                                    <h4 class="title">Captcha Server Settings</h4>
                                </div>								
                                <div class="card-content">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group label-floating">
                                                    <div class="control-label" style="width: 100%;">Server IP <span class="pull-right"><?=$mip?></span></div>
                                                    <input type="text" name="ip" value="<?=$server->get_server_ip()?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group label-floating">
                                                    <div class="control-label" style="width: 100%;">Server Port <span class="pull-right"><?=$port?></span></div>
                                                    <input type="text" name="port" value="<?=$server->get_server_port()?>"  class="form-control">
                                                </div>
                                            </div>										
                                            <div class="col-md-5">
                                                <div class="form-group label-floating">
                                                    <div class="control-label" style="width: 100%;">Local API Key <span class="pull-right">The key to use when calling the local API.</span></div>
                                                    <input type="text" name="api" value="<?=$api?>" class="form-control">
                                                </div>
                                            </div>											
                                            <div class="col-md-5">
                                                <div class="form-group label-floating">
                                                    <div class="control-label" style="width: 100%;">CAPTCHAs.IO API Key <span class="pull-right"><a href="https://captchas.io/" target="_new">Get One</a></span></div>
                                                    <input type="text" name="key" value="<?=$key?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group label-floating">
                                                    <div class="control-label">Image Captcha Support</div>
													<select name="images" class="form-control">
														<option value="0" <?=$images[0]?>>FALSE</option>
														<option value="1" <?=$images[1]?>>TRUE</option>
													</select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group label-floating">
                                                    <div class="control-label">reCAPTCHA Support</div>
													<select name="recaptcha" class="form-control">
														<option value="0" <?=$recaptcha[0]?>>FALSE</option>
														<option value="1" <?=$recaptcha[1]?>>TRUE</option>
													</select>
                                                </div>
                                            </div>	
                                            <div class="col-md-4">
                                                <div class="form-group label-floating">
                                                    <div class="control-label">hCAPTCHA Support</div>
													<select name="hcaptcha" class="form-control">
														<option value="0" <?=$hcaptcha[0]?>>FALSE</option>
														<option value="1" <?=$hcaptcha[1]?>>TRUE</option>
													</select>
                                                </div>
                                            </div>												
                                        </div>
                                        <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Update Settings</button>
                                        <div class="clearfix"></div>
                                    </form>
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