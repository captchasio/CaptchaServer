<?php
class CaptchaServer {
	private $version = NULL;
	private $db = NULL;
	private $ini = NULL;
	private $settings = NULL;
	private $settings_file = NULL;
	
	function __construct() {
		//$this->db = new SQLite3Database(dirname(dirname(__FILE__)) . "/data/database.db");
		$this->db = new SQLite3(dirname(dirname(__FILE__)) . "/data/database.db");
		$this->version = file(dirname(dirname(__FILE__)) . "/data/version.txt")[0];
		
		$this->ini = parse_ini_file(dirname(__FILE__) . "/config.ini");
		
		$this->settings_file = dirname(dirname(dirname(__FILE__))) . "/settings.json";
		$settings_string = file_get_contents($this->settings_file); 
		$settings = json_decode($settings_string, TRUE);
	
		$this->settings = $settings;
	}

	function get_server_ip() {
		return $this->settings["web_server"]["listen_on"][0];
	}
	
	function get_server_port() {
		return $this->settings["web_server"]["listen_on"][1];
	}
	
	function set_server_ip($ip) {
		$this->settings["web_server"]["listen_on"][0] = $ip;
		
		$json = json_encode($this->settings);
		$return = file_put_contents($this->settings_file, $json);		
		
		return $return;
	}
	
	function set_server_port($port) {
		$this->settings["web_server"]["listen_on"][1] = $port;
		
		$json = json_encode($this->settings);
		$return = file_put_contents($this->settings_file, $json);		
		
		return $return;		
	}
	
	function get_version() {
		return $this->version;
	}
	
	function get_db() {
		return $this->db;
	}	
	
	function get_api() {
		return $this->ini["api"];
	}
	
	function get_images() {
		return $this->ini["images"];
	}
	
	function get_recaptcha() {
		return $this->ini["recaptcha"];
	}

	function get_funcaptcha() {
		return $this->ini["funcaptcha"];
	}
	
	function get_hcaptcha() {
		return $this->ini["hcaptcha"];
	}	
	
	function get_text() {
		return $this->ini["text"];
	}	
	
	function get_audio() {
		return $this->ini["audio"];
	}	

	function get_key() {
		return $this->ini["key"];
	}

	function get_secret() {
		return $this->ini["secret"];
	}
	
	function save_request($data, $status, $recaptcha) {		
		$content = json_encode(array('data' => $data, 'status' => $status));
		
		if ($recaptcha != NULL) {
			$this->db->query("INSERT INTO `requests`(`data`, `status`, `recaptcha`) VALUES ('" . $content . "', " . $status . ", " . $recaptcha . ")");
		} else {
			$this->db->query("INSERT INTO `requests`(`data`, `status`) VALUES ('" . $content . "', " . $status . ")");
		}

		$result = $this->db->query("SELECT last_insert_rowid() AS `id`");
		$data = $result->fetchArray();
		$id = $data['id'];		
		
		return $id;
	}	

	function set_request_status($id, $status) {
		$return = $this->db->query("UPDATE `requests` SET `status` = " . $status . " WHERE `id` = " . $id);
		
		return $return;
	}
	
	function set_request_data($data, $id, $status) {
		$return = $this->db->query("UPDATE `requests` SET `status` = " . $status . ", `data` = '" . $data . "' WHERE `id` = " . $id);
		
		return $return;
	}	
	
	function get_request_data($id) {
		if ($id) {
			$result = $this->db->query("SELECT `data` FROM `requests` WHERE `id` = " . $id);
			$data = $result->fetchArray();
			
			return $data['data'];
		} else {
			return NULL;
		}
	}
	
	function get_recaptcha_id($id) {
		if ($id) {
			$result = $this->db->query("SELECT `recaptcha` FROM `requests` WHERE `id` = " . $id);
			$data = $result->fetchArray();
			
			return $data['recaptcha'];
		} else {
			return NULL;
		}
	}	
	
	function to_base64($image) {
		$type = pathinfo($image, PATHINFO_EXTENSION);
		$data = file_get_contents($image);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);	
		
		return $base64;
	}
	
	function upload($captcha) {
		$originalName = $captcha['tmp_name'];
		
		$_jpg = 'jpg';
		$_jpeg = 'jpeg';
		$_jpeg2 = 'pjpeg';
		$_gif = 'gif';
		$_png = 'png';					
		$_bmp = 'bmp';
		
		$types = explode(".", $originalName);
		$type = $types[count($types) - 1];
		
		$_captcha_dir = dirname(__FILE__) . "/data/captchas/";
		$_captcha_file = md5($originalName . time());	
		
		$tail = preg_replace('/(image\/([a-zA-Z]))/is', '$2', $type);
						
		$_captcha_file = $_captcha_dir . $_captcha_file . "." . $tail;
			
		if (move_uploaded_file($captcha['tmp_name'], $_captcha_file)) {
			$_captcha_file = realpath($_captcha_file);

			return $_captcha_file;
		}		
		
		return FALSE;
	}
	
	function solve_image($captcha) {
		$starttime = microtime(true);
		
		$postData=array();
		$postData['method']='post';
		$postData['key']=$this->get_key();
		$postData['file']=$captcha;
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,'http://' . $this->get_server_ip() . ':' . $this->get_server_port() . '/in.php');
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($ch,CURLOPT_TIMEOUT,20);
		$_raw=curl_exec($ch);
		curl_close($ch);	
		
		$_rest = explode("|", $_raw);
		$id = trim($_rest[1]);    

		$response = file_get_contents('http://' . $this->get_server_ip() . ':' . $this->get_server_port() . '/res.php?action=get&id='.$id.'&key=' . $this->get_key());

		$answer_raw = explode("|", $response);
		$answer = trim($answer_raw[1]); 
		
		$endtime = microtime(true);
		$elapsed = $endtime - $starttime;
		
		$return = json_encode(array('answer' => $answer, 'captcha' => $captcha, 'elapsed' => $elapsed));
					
		return $return;
	}	
	
	function save_config($array, $path) {
		unset($content, $arrayMulti);

		# See if the array input is multidimensional.
		foreach($array AS $arrayTest){
			if(is_array($arrayTest)) {
			  $arrayMulti = true;
			}
		}

		# Use categories in the INI file for multidimensional array OR use basic INI file:
		if ($arrayMulti) {
			foreach ($array AS $key => $elem) {
				$content .= "[" . $key . "]\n";
				foreach ($elem AS $key2 => $elem2) {
					if (is_array($elem2)) {
						for ($i = 0; $i < count($elem2); $i++) {
							$content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n" . PHP_EOL;
						}
					} else if ($elem2 == "") {
						$content .= $key2 . " = \n" . PHP_EOL;
					} else {
						$content .= $key2 . " = \"" . $elem2 . "\"\n" . PHP_EOL;
					}
				}
			}
		} else {
			foreach ($array AS $key2 => $elem2) {
				if (is_array($elem2)) {
					for ($i = 0; $i < count($elem2); $i++) {
						$content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n" . PHP_EOL;
					}
				} else if ($elem2 == "") {
					$content .= $key2 . " = \n" . PHP_EOL;
				} else {
					$content .= $key2 . " = \"" . $elem2 . "\"\n" . PHP_EOL;
				}
			}
		}

		if (!$handle = fopen($path, 'w')) {
			return false;
		}
		if (!fwrite($handle, $content)) {
			return false;
		}
		fclose($handle);
		return true;
	}	
	
	function notify_admin($ip, $port, $lkey, $ckey) {
		$post = array(
			'ip' => $ip,
			'port' => $port,
			'lkey' => $lkey,
			'ckey' => $ckey
		);		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://app.captchas.io/notifier.php');
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);	
		curl_setopt($ch, CURLOPT_USERAGENT,  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data;"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1800);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
		curl_setopt($ch, CURLOPT_TCP_NODELAY, TRUE);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return true;	
	}	
}	
?>