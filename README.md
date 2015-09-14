Supertext in PHP
================
This is a class that lets you interact with your account at Supertext, previously known as SMSGrupp.
OBS, you are currently in the BETA branch, things might not always work and will probably change. 
During development I use Kint (http://raveren.github.io/kint/) and PHP Snippets (https://github.com/Kakadua/PHP-Snippets) so you will probably need those too.

Documentation for the BETA that are always somewhat up to date can be found at http://popeen.com/dev/tools/SMSGrupp-in-PHP/docs/

Setup:
================
Put the files on a server that can run php files, then you need to generate a config file for your account. Do this by visiting http://{YOUR SITE}/generate_config.php and follow the instructions. Make sure generate_config has permission to create and write to files.
Once the config file is generated make sure to delete generate_config.php so no one else can generate a new file.

**VERY IMPORTANT:** Make sure that you deny access to config.cfg in htaccess so that no one can get access to it, should they manage to do that they can use it to get full access to your account. In the default .htaccess that comes with this class this has already been done.


Example:
================
In this example I grab a conversation called Beer and send a message to it
	
	$message = "This class is free as in both beer and speech!";
	
	$smsgrupp = new smsgrupp();
	$conv_named_beer = $smsgrupp -> get_conversations_by_name('Testar')[0]; //The function returns an array with all your conversations named Beer, for this example we just take the first one
	$smsgrupp-> send_to_conversation($message, $conv_named_beer['id']); //Send a message, give it the id of the conversation you want to send to