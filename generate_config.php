<?php

	require_once 'kint-0.9/Kint.class.php';
	
	function curl_post_request($url, $post, $header){		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);			
		$p = "";
		foreach($post as $k=>$v) {
			$p .= $k.'='.$v.'&';
		}
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
		return curl_exec($ch);
	}
	
	function request_pin($phone_number){	
		$url = 'https://api.getsupertext.com/v1/auth/pin';
		
		$postfields = array(
				"phone_nr" => $phone_number,
				"Client-Token" => 'android',
				"Client-Version:" => '299'
		);
		
		$header = array(
				'X-Features: 7',
				'Client-Token: android',
				"Client-OS-Version: 19",
				"Client-Version: 299",
				"Client-Token: android",
				"Client-Version: 299",
				"Content-Type: application/x-www-form-urlencoded",
				"Host: api.getsupertext.com",
				"Connection: Keep-Alive",
				"Accept-Encoding: gzip",
				'User-Agent: okhttp/2.3.0'
			);
			
		return curl_post_request($url, $postfields, $header);
	}
	
	function confirm_pin($phone_number, $pin){	
		$url = 'https://api.getsupertext.com/v1/auth/pin/confirm';
		
		$postfields = array(
				"phone_nr" => $phone_number,
				"pin" => $pin,
				"Client-Token" => 'android',
				"Client-Version:" => '299'
		);
		
		$header = array(
				'X-Features: 7',
				'Client-Token: android',
				"Client-OS-Version: 19",
				"Client-Version: 299",
				"Client-Token: android",
				"Client-Version: 299",
				"Content-Type: application/x-www-form-urlencoded",
				"Host: api.getsupertext.com",
				"Connection: Keep-Alive",
				"Accept-Encoding: gzip",
				'User-Agent: okhttp/2.3.0'
			);
			
		return curl_post_request($url, $postfields, $header);
	}
	
	function file_write($file, $data){
		$fh = fopen($file, 'w') or die("can't open file");
		fwrite($fh, $data);
		fclose($fh);		
	}
	
	
	if(isset($_POST['number']) && !isset($_POST['pin'])){
		
		request_pin($_POST['number']);
?>
		<form method="POST" action="generate_config.php">
			Enter pin: <input type="number" name="pin"/>
			<input type="hidden" name="number" value="<?php echo $_POST['number']; ?>" />
			<input type="submit" value="Confirm" />
		</form>
		
<?php		

	} else if(isset($_POST['pin'])){
		$temp = json_decode(confirm_pin($_POST['number'], $_POST['pin']),true); 
		$json['phone_nr'] = $_POST['number'];
		$json['Auth-Token'] = $temp['token'];
		$json['generated'] = time();
		
		file_write('config.cfg', json_encode($json));

		echo "Your config file has been created. Its recommended that you now delete or move generate_config.php so no one else can replace your config file.";


	} else{
		
?>
		Enter the phonenumber you want to use
		<form method="POST" action="generate_config.php">
			<input type="text" name="number" value="+46" />
			<input type="submit" value="Request pin" />
		</form>

<?php

	}
	
?>