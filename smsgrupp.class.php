<?php
	//SMSGrupp in PHP
	//Created by Popeen.com	
	class smsgrupp {
		var $phone_number;
		var $ch;
		var $source;
		
		function __construct($phone_number, $password) {
			//Login url
			$loginUrl     = 'https://www.smsgrupp.se/';
			//Cookie file
			$cookie  = dirname(__FILE__) . '\cookie.txt';
			unlink($cookie);
			
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_URL, $loginUrl);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie);
			curl_setopt($this->ch, CURLOPT_HEADER, false); 
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
					
			//define post fields
			$postfields = array(
				"number" => $phone_number,
				"password" => $password,
				"rememberme:" => ''
			);

			//Create string from the array
			$p = "";
			foreach($postfields as $k=>$v) {
				$p .= $k.'='.$v.'&';
			}

			//sign in
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $p);
			$this->source = curl_exec($this->ch);
		}
		
		function send_to_group($msg, $group){
			curl_setopt($this->ch, CURLOPT_URL, 'https://api.getsupertext.com/v1/conversations/'.$group.'/messages');
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, 'message='.$msg.'&send_to_self=1');
			//Emulate header sent by browser
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
				'Origin: https://www.smsgrupp.se',
				"Accept-Encoding: gzip,deflate,lzma,sdch",
				"Accept-Language: sv-SE,sv;q=0.8,en-US;q=0.6,en;q=0.4",
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.132 Safari/537.36 OPR/21.0.1432.57',
				"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
				"Accept: application/json, text/javascript, */*; q=0.01",
				'Referer:  https://www.smsgrupp.se/grupp/sapp/'.$group,
				"Connection: keep-alive",
				"Message-Count: 1",
				"Auth-Token: ".$this->get_between($this->source, 'Auth-Token", "', '"'),
				"Before: 62299612"  //TODO, Get this number to auto update
			));
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_exec($this->ch);
		}
		
		function sign_out(){
			curl_setopt($this->ch, CURLOPT_URL, 'http://smsgrupp.se/logout');
			$out = curl_exec($this->ch);
		}
	
	
	
		//Helpers
		function get_between($content,$start,$end){
			$r = explode($start, $content);
			if (isset($r[1])){
				$r = explode($end, $r[1]);
				return $r[0];
			}
			return '';
		}
	}
		
?>