SMSGrupp in PHP
================
This is a class that lets you interct with your SMSGrupp account.
Please note that SMSGrupp is only availiable for Swedish operators by the time I am writing this.

To get the ID of the group you want to send the messages to all you have to do is sign in to smsgrupp.se and go to the group. The ID will then show in the address bar of your browser.


Example:
================
The url of the group I want to send to is http://smsgrupp.se/grupp/sapp/123456
That means that the groups ID is 123456. I can then use this code to send a message to this group

	$phone_number = '0123456789';
	$password = 'myAwsomePassword';
	$group_id = '123456';
	$message = 'This is the message I want to send';	
	
	$smsgrupp = new smsgrupp($phone_number, $password); //Create a sms grupp object, give it your phone number and password so it can sign in.
	$smsgrupp-> send_to_group($message, $group_id); //Send a message, give it the id of the group you want to send to
	$smsgrupp-> sign_out(); //Sign out