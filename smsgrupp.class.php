<?php
	/**
	 * Class for interacting with the Swedish sms service Supertext, previously known as SMSGrupp
	 *
	 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
	 *
	 * @license https://github.com/Kakadua/SMSGrupp-in-PHP/blob/master/LICENSE Creative Commons Attribution 4.0 International Public License
	 *
	 * @link https://github.com/Kakadua/SMSGrupp-in-PHP/tree/master
	 *
	 * @package SMSGrupp-in-PHP
	 *
	 */
	 
	class smsgrupp {
		var $phone_number;
		var $ch;
		var $source;
		
		/**
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @version 1
		 */		
		function __construct($phone_number, $password) {
			$loginUrl     = 'https://www.smsgrupp.se/';
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
					
			$postfields = array(
				"number" => $phone_number,
				"password" => $password,
				"rememberme:" => ''
			);

			$p = "";
			foreach($postfields as $k=>$v) {
				$p .= $k.'='.$v.'&';
			}

			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $p);
			$this->source = curl_exec($this->ch);
		}
		
		/**
		 * Send a textmessage to a group
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $msg The message you want to send
		 * @param string $group The id of the group you want to send to
		 *
		 * @version 1.2
		 */
		function send_to_group($msg, $group){
			curl_setopt($this->ch, CURLOPT_URL, 'https://api.getsupertext.com/v1/conversations/'.$group.'/messages');
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, 'message='.$msg.'&send_to_self=1');
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
				"Accept: application/json, text/javascript, */*; q=0.01",
				"Accept-Encoding: gzip, deflate",
				"Accept-Language: en-US,en;q=0.8",
				"Auth-Token: ".$this->get_between($this->source, 'Auth-Token", "', '"'),
				"Before: 71480837",  //TODO, Get this number to auto update
				"Client-Token: ".$this->get_between($this->source, 'Client-Token", "', '"'),
				"Client-Version: 1",
				"Connection: keep-alive",
				"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
				"Host: api.getsupertext.com",
				"Message-Count: 1",
				'Origin: https://www.smsgrupp.se',
				'Referer:  https://www.smsgrupp.se/grupp/sapp/'.$group,
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.132 Safari/537.36 OPR/21.0.1432.57'
			));
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_exec($this->ch);
		}
		
		/**
		 * Sign out
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @version 1
		 */
		function sign_out(){
			curl_setopt($this->ch, CURLOPT_URL, 'http://smsgrupp.se/logout');
			$out = curl_exec($this->ch);
		}
		
		/**
		 * Gets a substring between two other substrings. 
		 * 
		 * OBS, this function only gives you the first match, 
		 * if you want all of them use  get_between_all instead
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @license https://raw.githubusercontent.com/Kakadua/PHP-Snippets/master/LICENSE Unlicense
		 *
		 * @link https://github.com/Kakadua/PHP-Snippets/
		 *
		 * @package Kakadua-PHP-Snippets
		 *
		 * @param String $content The full string you want to check
		 * @param String $before The substring before the string you want
		 * @param String $after The substring after the string you want
		 *
		 * @return String The function returns the read data or FALSE on failure. 
		 *
		 *	@version 1
		 */
		function get_between($content, $before, $after){
			$temp = explode($before, $content);
			if (isset($temp[1])){
				$temp = explode($after, $temp[1]);
				return $temp[0];
			} else{
				return '';
			}
		}
		
	}
?>