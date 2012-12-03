<?php
/*
PHPBB Forum manipulation Class
By Felix Manea (felix.manea@gmail.com)
www.ever.ro
Licensed under LGPL
NOTE: You are required to leave this header intact.
*/
//bag clasa
require_once("phpbb.class.php");

$phpbb_action = @$_REQUEST["op"];
//***************************************************************
//parameters used at class construction
//first parameter = absoulute physical path of the phpbb 3 forum ($phpbb_root_path variable)
//second parameter = php scripts extensions ($phpEx variable)
$phpbb = new phpbb("path/to/forum", "php");

switch($phpbb_action){
	case "login":
		//DATE TEST
		$phpbb_vars = array("username" => "test", "password" => "123123");
		//END DATE TEST
		$phpbb_result = $phpbb->user_login($phpbb_vars);
	break;
	case "logout":
		$phpbb_result = $phpbb->user_logout();
	break;
	case "loggedin":
		$phpbb_result = $phpbb->user_loggedin();
	break;
	case "user_add":
		//DATE TEST
		$phpbb_vars = array("username" => "test", "password" => "123", "user_email" => "test@test.com", "group_id" => "2");
		//END DATE TEST
		$phpbb_result = $phpbb->user_add($phpbb_vars);
	break;
	case "user_delete":
		//DATE TEST
		$phpbb_vars = array(/*"user_id" => "53", */"username" => "test");
		//END DATE TEST
		$phpbb_result = $phpbb->user_delete($phpbb_vars);
	break;
	case "user_update":
		//DATE TEST
		$phpbb_vars = array(/*"user_id" => "53", */"username" => "test", "user_email" => "1@2.com", "user_yim" => "my_yim", "user_website" => "http://www.ever.ro");
		//END DATE TEST
		$phpbb_result = $phpbb->user_update($phpbb_vars);
	break;
	case "change_password":
		//DATE TEST
		$phpbb_vars = array(/*"user_id" => "53", */"username" => "test", "password" => "123123");
		//END DATE TEST
		$phpbb_result = $phpbb->user_change_password($phpbb_vars);
	break;
}


if(isset($phpbb_result)) echo $phpbb_result."<br /><br />";
?>
<a href="?op=loggedin">loggedin</a><br />
<a href="?op=login">login</a><br />
<a href="?op=logout">logout</a><br />
<a href="?op=user_add">user_add</a><br />
<a href="?op=user_delete">user_delete</a><br />
<a href="?op=user_update">user_update</a><br />
<a href="?op=change_password">change_password</a><br />