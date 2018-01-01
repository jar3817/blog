<?php
include_once("_inc/main.php");

//die(var_dump($post));

$op = (isset($get->op)) ? $get->op : "";
switch ($op) {
	// signin and signout
	case "auth":
	user_auth_form();
	break;
	
	// process login
	case "auth_in":
	user_auth_callback();
	break;
	
	case "auth_in2":
	user_auth_callback2();
	break;
	
	// process logout
	case "auth_out":
	user_logout();
	redirect("/");
	break;
	
	case "view":
	post_view();
	break;	
	
	default:
	post_view();
}
?>
