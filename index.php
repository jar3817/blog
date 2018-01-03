<?php
include_once("_inc/main.php");

switch ((isset($get->op)) ? $get->op : "") {

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
	
	case "auth_out":
	user_logout();
	redirect("/");
	break;
	
	case "view":
	post_view();
	break;	
	
	case "post-comment":
	post_add_comment();
	break;
	
	case "tos":
	page_terms();
	break;
	
	default:
	post_view();
}
?>
