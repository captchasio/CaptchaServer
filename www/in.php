<?
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
	$recaptchas = $server->get_recaptcha();
	$funcaptchas = $server->get_funcaptcha();
	$texts = $server->get_text();
	$audio = $server->get_audio();
	
	$key = $server->get_key();
	$auth = $server->get_api();
	
	$api = new API($key);
	
	$_key = trim($_REQUEST['key']);
	$_method = trim($_REQUEST['method']);
	$_ip = $_SERVER['REMOTE_ADDR'];
	
	function http_get($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
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
		if ($images == 1 && $_REQUEST['method'] == 'post') {	
			$size = $_FILES['file']['size'];
			$type = $_FILES['file']['type'];			
			$originalName = $_FILES['file']['name'];													
			
			$jpg = 'image/jpg';
			$jpeg = 'image/jpeg';
			$jpeg2 = 'image/pjpeg';
			$gif = 'image/gif';
			$png = 'image/png';
			
			$_jpg = 'jpg';
			$_jpeg = 'jpeg';
			$_jpeg2 = 'pjpeg';
			$_gif = 'gif';
			$_png = 'png';					
			$_bmp = 'bmp';
			
			$_captcha_dir = dirname(__FILE__) . "/data/captchas/";
			$_captcha_file = md5($originalName . time());		
			$md5 = md5($originalName . time());
			$_name = hash_hmac("crc32", $md5, "captchaGwapo123");		
			
			$types = explode(".", $originalName);
			$type = $types[count($types) - 1];
			
			if (strtolower($type) == $_jpg || strtolower($type) == $_jpeg || strtolower($type) == $_jpeg2 || strtolower($type) == $_gif || strtolower($type) == $_png || strtolower($type) == $_bmp) {
				$tail = preg_replace('/(image\/([a-zA-Z]))/is', '$2', $type);
				 
				$final_filename = $_captcha_file;
				$filename = $final_filename . "." . $tail;
				$final_filename = $final_filename . "." . $tail;					
				$_captcha_file = $_captcha_dir . $final_filename;						
				
				if (move_uploaded_file($_FILES['file']['tmp_name'], $_captcha_file)) {
					$_captcha_file = realpath($_captcha_file);
					$_final_file = getCurlValue($_captcha_file, $type, $final_filename);							 					
				}																																										
				
				$starttime = microtime(true);
				
				$answer = $api->base64(to_base64($_captcha_file));
			
				$endtime = microtime(true);
				$elapsed = $endtime - $starttime;			
				
				$data = json_encode(array('answer' => 'CAPCHA_NOT_READY', 'token' => 'CAPCHA_NOT_READY', 'recaptcha' => 0, 'elapsed' => $elapsed, 'images' => array('base64' => to_base64($_captcha_file))));
				
				$id = $server->save_request($data, 0, $answer);
				$server->set_request_status($id, 0);
				
				print 'OK|'. $id;			
			} else {
				print 'ERROR_CAPTCHAIMAGE_BLOCKED';
			}							
		} else if (strtolower(trim($_REQUEST['method'])) == 'userrecaptcha') {								
			$starttime = microtime(true);

			$_proxy = !empty($_REQUEST['proxy']) ? urldecode(trim($_REQUEST['proxy'])) : NULL;	
			$_proxy_type = !empty($_REQUEST['proxy_type']) ? urldecode(trim($_REQUEST['proxy_type'])) : NULL;
						
			$answer = $api->recaptcha(trim($_REQUEST['googlekey']), trim($_REQUEST['pageurl']), $_proxy, $_proxy_type);		

			$endtime = microtime(true);
			$elapsed = $endtime - $starttime;	
					
			$data = json_encode(array('answer' => 'CAPCHA_NOT_READY', 'recaptcha' => 1, 'elapsed' => $elapsed, 'token' => 'CAPCHA_NOT_READY', 'images' => array('base64' => NULL)));
						
			$id = $server->save_request($data, 0, $answer);
		
			print 'OK|'. $id;																						
		} else if (strtolower(trim($_REQUEST['method'])) == 'hcaptcha') {								
			$starttime = microtime(true);

			$_proxy = !empty($_REQUEST['proxy']) ? urldecode(trim($_REQUEST['proxy'])) : NULL;	
			$_proxy_type = !empty($_REQUEST['proxy_type']) ? urldecode(trim($_REQUEST['proxy_type'])) : NULL;
						
			$answer = $api->hcaptcha(trim($_REQUEST['sitekey']), trim($_REQUEST['pageurl']), $_proxy, $_proxy_type);		

			$endtime = microtime(true);
			$elapsed = $endtime - $starttime;	
					
			$data = json_encode(array('answer' => 'CAPCHA_NOT_READY', 'recaptcha' => 1, 'elapsed' => $elapsed, 'token' => 'CAPCHA_NOT_READY', 'images' => array('base64' => NULL)));
						
			$id = $server->save_request($data, 0, $answer);
		
			print 'OK|'. $id;																						
		} else {			
			print 'ERROR_WRONG_METHOD_VALUE';
		}
	} else {			
		print 'ERROR_WRONG_API_KEY';
	}	
?>		
