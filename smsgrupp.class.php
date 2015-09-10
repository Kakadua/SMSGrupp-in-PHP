<?php
	require_once 'kint-0.9/Kint.class.php';
	
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
		var $config;
		
		/**
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $backcomp Deprecated variable, do not use in new implementations
		 * @param string $backcomp2 Deprecated variable, do not use in new implementations
		 *
		 * @version 2
		 */		
		function __construct($backcomp = '', $backcomp2 = '') {
			
			$this->config = json_decode($this->file_read('config.cfg'),true);
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->ch, CURLOPT_HEADER, false); 
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
				"Auth-Token: {$this->config['Auth-Token']}",
				'X-Features: 7',
				'Client-Token: android',
				"Client-OS-Version: 19",
				"Client-Version: 299",
				"Client-Token: android",
				"Content-Type: application/x-www-form-urlencoded",
				"Host: api.getsupertext.com",
				"Connection: Keep-Alive",
				"Accept-Encoding: gzip",
				'User-Agent: okhttp/2.3.0'
			));
		}
		
		/**
		 * Get all conversations
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $msg The message you want to send
		 * @param string $group The id of the group you want to send to
		 *
		 * @version 1
		 */
		function get_conversations(){
			curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
			curl_setopt($this->ch, CURLOPT_URL, 'https://api.getsupertext.com/v2/conversations');
			return json_decode(curl_exec($this->ch), true)['conversations'];
		}
		
		/**
		 * Get conversation by id
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $id The id of the conversation you want to get
		 *
		 * @version 1
		 */
		function get_conversation_by_id($id){
			curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
			curl_setopt($this->ch, CURLOPT_URL, "https://api.getsupertext.com/v2/conversations/{$id}");
			return json_decode(curl_exec($this->ch), true);
		}
		
		/**
		 * Get conversations by name
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $name The name of the conversations you want to get
		 *
		 * @version 1
		 */
		function get_conversations_by_name($name){
			$all_conv = $this->get_conversations();
			$found_conv = array();
			foreach($all_conv as $conv){
				if($conv['conversation']['name'] == $name){
					array_push($found_conv, $conv);
				}
			}
			return $found_conv;
		}
		
		/**
		 * Get conversations by multiple names
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param array $names The an array containing the names you want to look for
		 *
		 * @version 1
		 */
		function get_conversations_by_names($names){
			$all_conv = $this->get_conversations();
			$found_conv = array();
			foreach($all_conv as $conv){
				if(in_array($conv['conversation']['name'], $names)){
					array_push($found_conv, $conv);
				}
			}
			return $found_conv;
		}
		
		/**
		 * Get your Supertext profile
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @version 1
		 */
		function get_profile(){
			curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
			curl_setopt($this->ch, CURLOPT_URL, "https://api.getsupertext.com/v1/me");
			return json_decode(curl_exec($this->ch), true);
		}
		
		/**
		 * Send a textmessage to a conversation
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $msg The message you want to send
		 * @param string $conv_id The id of the conversation you want to send to
		 * @param string $attach Any attachment you may want to include, this is optional 
		 *
		 * @version 1
		 */
		function send_to_conversation($msg, $conv_id, $attach=null){
			//TODO, implement sending attachment
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_URL, "https://api.getsupertext.com/v1/conversations/{$conv_id}/messages");	
			$postfields = array(
				"message" => $msg,
				"Client-Token" => 'android',
				"created_at" => time(),
				'Client-Version' => '299',
				'send_to_self' => '1'
			);
			$p = "";
			foreach($postfields as $k=>$v) {
				$p .= $k.'='.$v.'&';
			}
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $p);			
			return json_decode(curl_exec($this->ch), true);
		}
		
		/**
		 * @deprecated Use send_to_conversation instead
		 *
		 * Send a textmessage to a group
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $msg The message you want to send
		 * @param string $group The id of the group you want to send to
		 *
		 * @version 2
		 */
		function send_to_group($msg, $group){
			$this->send_to_conversation($msg, $group);
		}
		
		/**
		 * Add a new user to a conversation
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $name The name you want the user to have in the group
		 * @param string $number The users phonenumber, must use countrycode, ex +467012345
		 * @param string $conv_id The id of the conversation you want to add the user to
		 *
		 * @version 1
		 */
		function add_user_to_conversation($name, $number, $conv_id){
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_URL, "https://api.getsupertext.com/v1/conversations/{$conv_id}/users");
			$postfields = array(
				"member[0][name]" => $name,
				"Client-Token" => 'android',
				"member[0][value]" => $number,
				'Client-Version' => '299',
				'member[0][type]' => 'phone_nr'
			);
			$p = "";
			foreach($postfields as $k=>$v) {
				$p .= $k.'='.$v.'&';
			}
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $p);			
			return json_decode(curl_exec($this->ch), true);
		}
		
		/**
		 * Remove a user from a conversation
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @param string $user_id The id of the user you want to remove
		 * @param string $conv_id The id of the conversation you want to remove the user from
		 *
		 * @version 1
		 */
		function remove_user_from_conversation($user_id, $conv_id){
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($this->ch, CURLOPT_URL, "https://api.getsupertext.com/v1/conversations/{$conv_id}/users/{$user_id}");
			return json_decode(curl_exec($this->ch), true);
		}
		
		/**
		 * @deprecated No longer does anything
		 *
		 * Sign out
		 *
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @version 2
		 */
		function sign_out(){}
		
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
		 * @return String The function returns the read data or an empty string on failure. 
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
		
		/**
		 * Read a file
		 * 
		 * @author Patrik "Popeen" Johansson <patrik@ptjwebben.se>
		 *
		 * @license https://raw.githubusercontent.com/Kakadua/PHP-Snippets/master/LICENSE Unlicense
		 *
		 * @link https://github.com/Kakadua/PHP-Snippets/
		 *
		 * @package Kakadua-PHP-Snippets
		 *
		 * @param String $file the name of the file you want to read
		 *
		 * @return String The function returns the data in the file
		 *
		 *	@version 1
		 */
		function file_read($file){
			$fh = fopen($file, 'r');
			$data = fread($fh, filesize($file));
			fclose($fh);
			return $data;	
		}
		
	}
?>