<?php
	ini_set('display_errors', 0);
	error_reporting(0);	
	
	session_start();
	ob_start();
	
	require_once('system/CaptchaServer.class.php');
	require_once('system/API.class.php');
	
	$server = new CaptchaServer();
	
	$version = $server->get_version();
	$db = $server->get_db();
	$images = $server->get_images();
	$recaptcha = $server->get_recaptcha();
	$funcaptcha = $server->get_funcaptcha();
	$hcaptcha = $server->get_hcaptcha();
	$text = $server->get_text();
	$audio = $server->get_audio();
	
	$key = $server->get_key();
	$auth = $server->get_api();
	
	$api = new API($key);
	
	$_key = trim($_REQUEST['key']);
	
	function http_get($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$raw=curl_exec($ch);
		curl_close($ch);		
		
		return $raw;
	}
	
	function to_base64($image) {
		$type = pathinfo($image, PATHINFO_EXTENSION);
		$data = file_get_contents($image);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);	
		
		return $base64;
	}
	
	function base64_to_file($base64_string, $output_file) {
		$data = explode(',', $base64_string);
		file_put_contents($output_file, base64_decode($data[1]));		
					
		return $output_file; 
	}	
	
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

	if ($auth == $_key) {	
		if (strtolower(trim($_REQUEST['action'])) == "get") {			
			$request = $server->get_request_data($_REQUEST['id']);
			
			$data = json_decode($request, TRUE);
			$subdata = empty($data['recaptcha']) ? json_decode($data['data'], TRUE) : $data;

			if (!empty($subdata['answer']) && strstr($subdata['answer'], 'OK|') === TRUE ) {
				print 'OK|' . $subdata['answer'];
			} else {
				$recaptcha = $server->get_recaptcha_id($_REQUEST['id']);
				
				if (!empty($subdata['token']) && $subdata['token'] != NULL) {
					$_rest = explode("|", $subdata['token']);	
					$response = trim($_rest[0]);
						
					if ($response == "CAPCHA_NOT_READY" || empty($response)) {
						$starttime = microtime(true);
						
						$url = 'http://api.captchas.io/res.php?key='.$key.'&action=get&id=' . $recaptcha;
						$answer = trim(http_get($url));		

						$endtime = microtime(true);
						$elapsed = $endtime - $starttime;			
						$subdata['elapsed'] += $elapsed;
						
						$_rest = explode("|", $answer);	
						$token = trim($_rest[1]);							
						
						if ($answer == "CAPCHA_NOT_READY" || empty($answer)) {				
							$data = json_encode(array('recaptcha' => $subdata['recaptcha'], 'elapsed' => $subdata['elapsed'], 'token' => 'CAPCHA_NOT_READY', 'images' => $subdata['images']));
							$server->set_request_data($data, trim($_REQUEST['id']), 0);			
							
							print 'CAPCHA_NOT_READY';
						} else if ($answer == 'ERROR_CAPTCHA_UNSOLVABLE' || $answer == 'ERROR_WRONG_CAPTCHA_ID') {			
							$data = json_encode(array('recaptcha' => $subdata['recaptcha'], 'elapsed' => $subdata['elapsed'], 'token' => 'ERROR_CAPTCHA_IS_UNSOLVABLE', 'images' => $subdata['images']));
							$server->set_request_data($data, trim($_REQUEST['id']), 2);			
							
							print 'ERROR_CAPTCHA_IS_UNSOLVABLE';				
						} else {
							if ($token == 'ERROR_CAPTCHA_UNSOLVABLE' || $token == 'ERROR_WRONG_CAPTCHA_ID') {
								print $token;
							} else {
								print 'OK|' . $token;
							}		
							
							$data = json_encode(array('recaptcha' => $subdata['recaptcha'], 'elapsed' => $subdata['elapsed'], 'token' => 'OK|' . $token, 'images' => $subdata['images']));
							
							$server->set_request_status(trim($_REQUEST['id']), 1);
							$server->set_request_data($data, trim($_REQUEST['id']), 1);				
						}	
					} else if ($subdata['token'] == 'ERROR_CAPTCHA_UNSOLVABLE' || $subdata['token'] == 'ERROR_WRONG_CAPTCHA_ID') {	
						print 'ERROR_CAPTCHA_IS_UNSOLVABLE';				
					} else {
						print $subdata['token'];
					}				
				} else {
					$starttime = microtime(true);				

					$url = 'http://api.captchas.io/res.php?key='.$key.'&action=get&id=' . $recaptcha;
					$answer = trim(http_get($url));
					
					$endtime = microtime(true);
					$elapsed = $endtime - $starttime;			
					$subdata['elapsed'] += $elapsed;
					
					$_rest = explode("|", $answer);
					$response = trim($_rest[0]);	
					$token = trim($_rest[1]);

					if ($token == "CAPCHA_NOT_READY") {				
						$data = json_encode(array('recaptcha' => $subdata['recaptcha'], 'elapsed' => $subdata['elapsed'], 'token' => 'CAPCHA_NOT_READY', 'images' => $subdata['images']));
						$server->set_request_data($data, trim($_REQUEST['id']), 0);			
						
						print 'CAPCHA_NOT_READY';	
					} else if ($answer == 'ERROR_CAPTCHA_UNSOLVABLE' || $answer == 'ERROR_WRONG_CAPTCHA_ID') {
						$data = json_encode(array('recaptcha' => $subdata['recaptcha'], 'elapsed' => $subdata['elapsed'], 'token' => 'ERROR_CAPTCHA_IS_UNSOLVABLE', 'images' => $subdata['images']));
						$server->set_request_data($data, trim($_REQUEST['id']), 2);			
						
						print 'ERROR_CAPTCHA_IS_UNSOLVABLE';				
					} else {
						if ($token == 'ERROR_CAPTCHA_UNSOLVABLE' || $token == 'ERROR_WRONG_CAPTCHA_ID') {
							print $token;
						} else {
							print 'OK|' . $token;
						}	
						
						$data = json_encode(array('recaptcha' => $subdata['recaptcha'], 'elapsed' => $subdata['elapsed'], 'token' => 'OK|' . $token, 'images' => $subdata['images']));
						
						$server->set_request_status(trim($_REQUEST['id']), 1);
						$server->set_request_data($data, trim($_REQUEST['id']), 1);				
					}	
				}
			}
		} 
	} else {			
		print 'ERROR_WRONG_API_KEY';
	}		
?>