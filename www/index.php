<?php
	ini_set('display_errors', 0);
	error_reporting(0);
	
	require_once('system/CaptchaServer.class.php');

	$server = new CaptchaServer();
 
	$key = $server->get_key();
	$server_ip = $server->get_server_ip();
	$server_port = $server->get_server_port();
	$api = $server->get_api();
	
	if (empty($key) || empty($api) || empty($server_ip) || empty($server_port)) {
		header('Location: settings.php');
	}
	
	$version = $server->get_version();
	$db = $server->get_db();
	
	$tokens = file_get_contents("https://api.captchas.io/res.php?key=".$key."&action=getbalance");
	
	$solves = $db->query("SELECT count(*) AS `solves` FROM `requests` WHERE `status` = 1 AND DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
	$data = $solves->fetchArray();
	$solves = $data['solves'];
	
	$requests = $db->query("SELECT count(*) AS `requests` FROM `requests` WHERE DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
	$data = $requests->fetchArray();
	$requests = $data['requests'];	
	
	$ave = 0;		
	
	function getCurlValue($filename, $contentType = NULL, $postname = NULL) {
		if (function_exists('curl_file_create')) {
			return curl_file_create($filename, $contentType, $postname);
		}
	 
		$value = "@{$filename};filename=" . $postname;
		if ($contentType) {
			$value .= ';type=' . $contentType;
		}
	 
		return $value;
	}	
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Dashboard</title>
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
    <script>		   
		setInterval(function(){updateSolves()}, 2000);
		setInterval(function(){updateTokens()}, 2000);
		setInterval(function(){updateRequests()}, 2000);
		setInterval(function(){updateAve()}, 2000);
		setInterval(function(){updateConsole()}, 1000);
		
		function updateSolves(){
			$.get( "http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/ajax.php?q=solves",{})
				.done(function(data) {
				document.getElementById("solves").innerHTML = data;
			});
		};
		
		function updateTokens(){
			$.get( "http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/ajax.php?q=tokens",{})
				.done(function(data) {
				document.getElementById("tokens").innerHTML = data;
			});
		};
		
		function updateRequests(){
			$.get( "http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/ajax.php?q=requests",{})
				.done(function(data) {
				document.getElementById("requests").innerHTML = data;
			});
		};
		
		function updateAve(){
			$.get( "http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/ajax.php?q=ave",{})
				.done(function(data) {
				document.getElementById("ave").innerHTML = data;
			});
		};

		function updateConsole(){
			$.get( "http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/ajax.php?q=console",{})
				.done(function(data) {
				document.getElementById("console").innerHTML = data;
			});
		};		
		
		function sweep(i){
			document.getElementById('sweep'+i).innerHTML = "<img src='assets/img/loading.png' border='0'>";
			$.get( "http://<?=$server->get_server_ip()?>:<?=$server->get_server_port()?>/ajax.php?q=sweep&t="+i,{})
				.done(function(data) {
				document.getElementById('sweep'+i).innerHTML = "<a href=\"javascript:sweep('"+i+"');\"><b class=\"material-icons text-primary\">delete_sweep</b></a>";
				document.getElementById(i).innerHTML = data;
			});
		};		
    </script>	
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
                    <li class="active">
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
                        <a class="navbar-brand" href="index.php">Dashboard</a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
						<?
							if (empty($key)) {
								print '
									<div class="col-md-8">
										<div class="alert alert-info">
											<button type="button" aria-hidden="true" class="close">×</button>
											<span>
												<b> Information - </b> Please set your CAPTCHAs.IO API key in your <a href="settings.php"><u>settings</u></a>.</span>
										</div>	
									</div>
								';										
							}								
						?>	
						
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="orange">
                                    <i class="material-icons">toll</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Threads</p>
                                    <h3 class="title" id="tokens"><?=number_format($tokens)?></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats" style="width: 100%;">
                                        <i class="material-icons text-success">link</i>
                                        <a href="https://captchas.io" target="_new">Get More Threads...</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="green">
                                    <i class="material-icons">http</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Requests</p>
                                    <h3 class="title" id="requests"><?=number_format($requests)?></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats" style="width: 100%;">
										<div class="pull-right" id="sweeprequests"><a href="javascript:sweep('requests');"><b class="material-icons text-primary">delete_sweep</b></a></div>
                                        <i class="material-icons text-success">date_range</i> Last 24 Hours
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="red">
                                    <i class="material-icons">info_outline</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Solves</p>
                                    <h3 class="title" id="solves"><?=number_format($solves)?></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats" style="width: 100%;">
										<div class="pull-right" id="sweepsolves"><a href="javascript:sweep('solves');"><b class="material-icons text-primary">delete_sweep</b></a></div>
                                        <i class="material-icons text-success">local_offer</i> All Time Solves
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="blue">
                                    <i class="material-icons">timelapse</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Seconds</p>
                                    <h3 class="title" id="ave"><?=number_format($ave, 1)?></h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats" style="width: 100%;">
										<div class="pull-right" id="sweepave"><a href="javascript:sweep('ave');"><b class="material-icons text-primary">delete_sweep</b></a></div>
                                        <i class="material-icons text-success">av_timer</i> Average Response Time
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
                    <div class="row">
					
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header" data-background-color="orange">
                                    <span class="pull-right">Host: <?=$server->get_server_ip()?> &nbsp;-&nbsp; Port: <?=$server->get_server_port()?></span>
									<h4 class="title">Console</h4>
                                </div>
                                <div class="card-content text-center" id="console" style="border: 1px solid #AAAAAA; margin: 10px; background-color: #F3F3F3;">	
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
<!--   Core JS Files   -->
<script src="assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/material.min.js" type="text/javascript"></script>
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
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="assets/js/demo.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        // Javascript method's body can be found in assets/js/demos.js
        demo.initDashboardPageCharts();

    });
</script>

</html>