<?php
	ini_set('display_errors', 0);
	error_reporting(0);
	
	require_once('system/CaptchaServer.class.php');

	$server = new CaptchaServer();

	$key = $server->get_key();
	$secret = $server->get_secret();
	 
	$version = $server->get_version();
	$db = $server->get_db();
		
	if ($_REQUEST['q'] == 'solves') {
		$solves = $db->query("SELECT count(*) AS `solves` FROM `requests` WHERE `status` = 1 AND DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
		$data = $solves->fetchArray();
		$solves = $data['solves']; 	
		
		print number_format($solves);		
	} else if ($_REQUEST['q'] == 'requests') {
		$requests = $db->query("SELECT count(*) AS `requests` FROM `requests` WHERE DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
		$data = $requests->fetchArray();
		$requests = $data['requests'];
	
		print number_format($requests);
	} else if ($_REQUEST['q'] == 'tokens') {
		$tokens = file_get_contents("https://api.captchas.io/res.php?key=".$key."&action=getbalance");
	
		print number_format($tokens);
	} else if ($_REQUEST['q'] == 'ave') {
		$requests = $db->query("SELECT (SUM(`time`)/COUNT(`time`)) AS `ave` FROM `response_timer`");
		$result = $requests->fetchArray();
		$score = $result['ave'];
		
		print number_format($score, 1);
	} else if ($_REQUEST['q'] == 'sweep') {
		if ($_REQUEST['t'] == 'solves') {
			$db->query("DELETE FROM `requests` WHERE `status` = 1 AND DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
			$solves = $db->query("SELECT count(*) AS `solves` FROM `requests` WHERE `status` = 1 AND DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
			$data = $solves->fetchArray();
			$solves = $data['solves']; 	
			
			print number_format($solves);					
		} else if ($_REQUEST['t'] == 'requests') {
			$db->query("DELETE FROM `requests` WHERE DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
			$requests = $db->query("SELECT count(*) AS `requests` FROM `requests` WHERE DATE(`date`) = DATE(CURRENT_TIMESTAMP)");
			$data = $requests->fetchArray();
			$requests = $data['requests'];
		
			print number_format($requests);				
		} else if ($_REQUEST['t'] == 'ave') {
			$db->query("DELETE FROM `response_timer`");
			$requests = $db->query("SELECT (SUM(`time`)/COUNT(`time`)) AS `ave` FROM `response_timer`");
			$result = $requests->fetchArray();
			$score = $result['ave'];
			
			print number_format($score, 1);			
		}
	} else if ($_REQUEST['q'] == 'console') {
		$requests = $db->query("SELECT * FROM `requests` WHERE DATE(`date`) = DATE(CURRENT_TIMESTAMP) ORDER BY `date` DESC LIMIT 1");
		$result = $requests->fetchArray();
		$request = $server->get_request_data($result['id']);
		
		$data = json_decode($request, TRUE);
		$subdata = empty($data['recaptcha']) ? json_decode($data['data'], TRUE) : $data;
		
		if (!empty($result['id'])) {
			if ($data['recaptcha'] == 1) {
				print '
					<span id="captcha"><img src="assets/img/recaptcha.png" style="height: 20%; width: 40%; border: 1px dotted #AAA;"></span>
					<h2 id="answer">[ g-token ]</h2>
					<div><small id="timelapse">Timelapse: ' . $data['elapsed'] . 's</small></div>		
				';				
			} else {
				$answer = explode("|", $data['token']);
				
				print '
					<span id="captcha"><img src="' . $data['images']['base64'] . '" style="height: 20%; width: 40%; border: 1px dotted #AAA; margin-bottom: 5px; padding-bottom: 0px;"></span>
					<h2 id="answer" style="margin-top:5px;padding-top:0px;">' . $answer[1] . '</h2>
					<div><small id="timelapse">Timelapse: ' . $data['elapsed'] . 's</small></div>		
				';
			}
			$db->query("UPDATE `requests` SET `displayed` = 1 WHERE `id` = " . $result['id']);
			$db->query("INSERT INTO `response_timer`(`id`, `time`) VALUES (" . $result['id'] . ", " . $data['elapsed'] . ")");
		}
	}
?>