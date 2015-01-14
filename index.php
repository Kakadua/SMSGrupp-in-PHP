<?php
	error_reporting(0);
	include "SMSGrupp-in-PHP/smsgrupp.class.php"; //https://github.com/Kakadua/SMSGrupp-in-PHP
	include "PHP-Snippets/get_url_directory.php"; //https://github.com/Kakadua/PHP-Snippets
?>
<?php
	$phone_number = '';
	$password = '';
	$group_id = urldecode($_GET['grp']);
	$message = urldecode($_GET['msg']);   
	if($group_id != '' && $message != ''){
		$smsgrupp = new smsgrupp($phone_number, $password);
		$smsgrupp-> send_to_group($message, $group_id);
		$smsgrupp-> sign_out();
	}
?>
<html>
	<head>
		<title>SMSGrupp API</title>
		<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
		<style type="text/css">
			*{ font-size:20px; font-family:'Lato',sans-serif; background-color:#ccc;}
			a{ color:#606aaa; text-decoration:none; font-weight:bold; }
		</style>
	</head>
	<body>
		Att skicka SMS med <b>SMSGrupp API</b> är enkelt. Skapa bara en <a href="https://smsgrupp.se/" target="_blank">SMSGrupp</a> och bjud in vårt nummer (<b><?php echo $phone_number; ?></b>) till den.<br/>
		Sen skickar du sms genom att göra en GET request mot <?php echo get_url_directory();?>?grp=<b>{GRUPP_ID}</b>&msg=<b>{MSG}</b><br/>&nbsp;<br/>
		<b>{GRUPP_ID}</b> =  Du ser det i adressen till din grupp på <a href="https://smsgrupp.se/" target="_blank">smsgrupp.se</a>. I det här exemplet är det 123456, http://smsgrupp.se/grupp/sapp/123456)<br/>
		<b>{MSG}</b> = Det meddelande du vill skicka, se till att det är URL encodat<br/>&nbsp;<br/>
		APIet är Open Source och kan laddas ner på <a href="https://github.com/Kakadua/SMSGrupp-in-PHP/tree/API" target="_blank">den här GitHub sidan</a>.<br/>
		Det bygger på våran <b>SMSGrupp in PHP</b> klass som finns publicerad på <a href="https://github.com/Kakadua/SMSGrupp-in-PHP/tree/master" target="_blank">den här GitHub sidan</a>
	</body>
</html>