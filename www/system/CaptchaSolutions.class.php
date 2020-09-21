<?php
/*
File: CaptchaSolutions.php
Date: 10/25/2015
Version 1.0
Author: Glenn Prialde
Copyright Captcha Solutions http://www.captchasolutions.com/ 2015.  All rights reserved.
Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
* You must provide a link back to www.henryranch.net on the site on which this software is used.
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the distribution.
* Neither the name of the HenryRanch LCC nor the names of its contributors nor authors may be used to endorse or promote products derived 
from this software without specific prior written permission.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
THE AUTHORS, OWNERS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES 
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
DEALINGS IN THE SOFTWARE.  
*/
class API {
	public $host = 'api.captchas.io';
	public $token = null;
	public $secret = null;		
	public $port = 80;
	public $remoteFile = "/solve";
	public $basicAuthUsernameColonPassword = "";
	public $bufferSize = 2048;
	//public $mode = "post";
	public $mode = "get";
	public $fromEmail = "admin@captchasolutions.com";
	public $postData = "";
	public $localFile = "";
	public $proxy_host = "";
	public $proxy_port = "";
	public $is_proxy = 0;
	
	public function __construct($token, $secret) {
		$this->token = $token;
		$this->secret = $secret;
	}	
	
	public function getCurlValue($filename, $contentType = NULL, $postname = NULL) {
		if (function_exists('curl_file_create')) {
			return curl_file_create($filename, $contentType, $postname);
		}
	 
		$value = "@{$filename};filename=" . $postname;
		if ($contentType) {
			$value .= ';type=' . $contentType;
		}
	 
		return $value;
	}	
	
	public function balance($username) { 
		$url = 'http://api.captchasolutions.com/solve?p=balance&username=' . $username . '&key=' . $this->token . '&secret=' . $this->secret;
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request($url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url, null, null);
		}			
		
		return $ret;		
	}
	public function decode($catpcha) { 
		$url = 'http://api.captchasolutions.com/solve?p=decode&url=' . urlencode($catpcha) . '&key=' . $this->token . '&secret=' . $this->secret;
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request($url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url, null, null);
		}			
		
		return $ret;
	}	
	public function base64($catpcha, $out = 'xml') { 
		$this->localFile = $catpcha;
		if ($this->is_proxy == 1) {
			$ret = $this->_post_captcha('base64', $this->token, $this->secret, $this->localFile, $out, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this->_post_captcha('base64', $this->token, $this->secret, $this->localFile, $out, null, null);
		}
		
		return $ret;
	}	
	
	public function audio($audiofile, $out = 'xml') { 
		$this->localFile = $audiofile;
		if ($this->is_proxy == 1) {
			$ret = $this->_post_audio('audio', $this->token, $this->secret, $this->localFile, $out, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this->_post_audio('audio', $this->token, $this->secret, $this->localFile, $out, null, null);
		}
		
		return $ret;		
	}	
	
	public function text($question) { 
		$url = 'http://api.captchasolutions.com/solve?p=textcaptcha&out=text&question=' . urlencode($question) . '&key=' . $this->token . '&secret=' . $this->secret;
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request($url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url);
		}
		
		return $ret;
	}	
	
	public function in($recaptchas, $google_site_key, $page_url) { 
		$url = 'https://api.re-captchas.com/in.php?key=' . $recaptchas . '&method=userrecaptcha&googlekey=' . $google_site_key . '&pageurl=' . urlencode($page_url);
		
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request("https://www.captchaservers.com/f.php?u=".$url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url);
		}		

		return $ret;
	}	
	
	public function res($recaptchas, $id) { 
		$url = 'https://api.re-captchas.com/res.php?key=' . $recaptchas . '&action=get&id=' . $id;
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request($url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url, null, null);
		}		
		
		return $ret;
	}	
	
	public function nocaptcha($google_site_key, $page_url, $out = 'xml') { 
		$url = 'http://api.captchasolutions.com/solve?p=nocaptcha&googlekey=' . $google_site_key . '&pageurl=' . urlencode($page_url) . '&key=' . $this->token . '&secret=' . $this->secret . '&out=' . $out;
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request($url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url, null, null);
		}		
		
		return $ret;
	}		
	
	public function funcaptcha($site_key, $page_url, $out = 'xml') { 
		$url = 'http://api.captchasolutions.com/solve?p=funcaptcha&publickey=' . $site_key . '&siteurl=' . urlencode($page_url) . '&key=' . $this->token . '&secret=' . $this->secret . '&out=' . $out;
		if ($this->is_proxy == 1) {
			$ret = $this::_get_request($url, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this::_get_request($url, null, null);
		}		
		
		return $ret;
	}
	
	public function upload($catpcha, $out = 'xml') {		
		if ($this->is_proxy == 1) {
			$ret = $this->_post_captcha('upload', $this->token, $this->secret, $catpcha, $out, $this->proxy_host, $this->proxy_port);
		} else {
			$ret = $this->_post_captcha('upload', $this->token, $this->secret, $catpcha, $out);
		}
		
		return $ret;
	}			
	public function set_proxy($host, $port) {
		$this->proxy_host = $host;
		$this->proxy_port = $port;
		$this->is_proxy = 1;
	}
	
	public function _post_audio($p, $key, $secret, $audiofile, $out, $proxy_host = null, $proxy_port = null) {
		$post = array(
			'p' => 'audio',
			'key' => $key,
			'secret' => $secret,
			'audiofile' => $this->getCurlValue($audiofile),
			'out' => $out
		);		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.captchasolutions.com/solve');
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);	
		curl_setopt($ch, CURLOPT_USERAGENT,  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data;"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TCP_NODELAY, TRUE);
		
		if ($proxy_host != null && $proxy_port != null) {			
			$proxy = $proxy_host . ":" . $proxy_port;
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}				
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;		
	}
	
	public function _post_captcha($p, $key, $secret, $captcha, $out, $proxy_host = null, $proxy_port = null) {
		if ($p != 'base64') {
			$captcha = $this->getCurlValue($captcha);
		}
		
		$post = array(
			'p' => $p,
			'key' => $key,
			'secret' => $secret,
			'captcha' => $captcha,
			'out' => $out
		);		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.captchasolutions.com/solve');
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
		
		if ($proxy_host != null && $proxy_port != null) {			
			$proxy = $proxy_host . ":" . $proxy_port;
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}				
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;		
	}
	
	private static function _get_request($url, $proxy_host = null, $proxy_port = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.captchasolutions.com/solve');
		curl_setopt($ch, CURLOPT_HEADER, FALSE);					
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);	
		curl_setopt($ch, CURLOPT_USERAGENT,  "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1800);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
		curl_setopt($ch, CURLOPT_TCP_NODELAY, TRUE);		
		
		if ($proxy_host != null && $proxy_port != null) {			
			$proxy = $proxy_host . ":" . $proxy_port;
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}				
			
		$response=curl_exec($ch);
		curl_close($ch);
		
		return $response;		
	}	
}
?>