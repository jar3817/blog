<?php
include_once("_inc/main.php");

$op = (isset($get->op)) ? $get->op : "";
switch ($op) {
	// signin and signout
	case "auth":
	user_auth_form();
	break;
	
	case "view":
	post_view();
	break;	
	
	default:
	post_view();
}
?>
