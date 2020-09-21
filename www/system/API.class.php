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

	private $keys = null;
	private $server = 'api.captchas.io';
	
	function __construct($key) {
		$this->keys = $key;
	}
	
	function get($url) {
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
	
	function image($image) {
		$post = array(
			'key' => $this->keys,
			'method' => 'post',
			'file' => '@'.$image
		);		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.captchas.io/in.php');
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
		
		$returned = explode("|", $response);
		$id = trim($returned[1]);   
		
		return $id;		
	}
	
	function base64($base64) {
		$post = array(
			'key' => $this->keys,
			'method' => 'base64',
			'body' => $base64
		);		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.captchas.io/in.php');
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
		
		$returned = explode("|", $response);
		$id = trim($returned[1]);   
		
		return $id;		
	}	
	
	function recaptcha($googlekey, $pageurl, $proxy = null, $proxytype = null, $version = 'v2', $action = 'verify', $invisble = '0', $min_score = '0.3') {
		$post = array(
			'key' => $this->keys,
			'method' => 'userrecaptcha',
			'googlekey' => $googlekey,
			'pageurl' => $pageurl,
			'version' => $version,
			'action' => $action,
			'invisble' => $invisble,
			'min_score' => $min_score,
			'proxy' => $proxy,
			'proxytype' => $proxytype
		);		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.captchas.io/in.php');
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
		
		$returned = explode("|", $response);
		$id = trim($returned[1]);   

		//$answer = $this->get('https://' . $this->server . '/res.php?key=' . $this->keys . '&action=get&id='.$id);
		//$return = explode("|", $answer);
		
		return trim($id);		
	}		
}

?>