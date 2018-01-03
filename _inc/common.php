<?php

// UTC time in the database, converts to user's timezone
function format_date($date, $timetoo = 0, $format = "F j, Y") {
	global $site;
	$changetime = new DateTime($date, new DateTimeZone('UTC'));
	$changetime->setTimezone(new DateTimeZone($site->settings->default_timezone));
	$time24 = "H:i";
	$time12 = "g:i a";
	$time = (1) ? $time12 : $time24;
	$time = ($timetoo) ? " - " . $time : "";
	return $changetime->format($format . $time);
}

// make a "random" string of $len length
function generate_key($len=6) {
	$k = uniqid();
	if ($len > strlen($k)) {
		$len = strlen($k);
	}
	
	$key = "";
	for ($i=0; $i<(strlen($k)/2)-1; $i++) {
		$key .= $k[strlen($k) - 1 - $i] . $k[$i];
	}
	
	return substr($key, 0, $len);
}

function navigation(){
	global $site;
	?>
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"><?=$site->settings->site_name?></a>
			</div>
			<div class="collapse navbar-collapse">
				<!--
				<ul class="nav navbar-nav">
					<li><a href="/hist">History</a></li>
				</ul>
				-->
				<ul class="nav navbar-nav navbar-right">
<?php if (user_is_manager()) { ?>
					<li><button onclick="window.location.href='<?=$site->settings->uri_man_new_post?>';" class="btn btn-default navbar-btn">New Post</button></li>
<?php } ?>
<?php 
	if (user_is_logged_in()) { 
		//$c = message_unread_count($site->user->id);
		$c = 0;
		$unread = ($c > 0) ? " <span class=\"badge badge-error\">$c</span>" : "";
?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img class="img-circle profile-thumbnail" src="<?=$site->user->picture_url?>"> <?=given_name($site->user->name)?> <?=$unread?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
<?php if (user_is_manager()) { ?>
							<li><a href="<?=$site->settings->uri_manager?>">Manager</a></li>
<?php } ?>
							<li><a href="<?=$site->settings->uri_settings?>">Settings</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="<?=$site->settings->uri_logout?>">Signout</a></li>
						</ul>
					</li>
<?php } else { ?>
					<li class=""><a href="<?=$site->settings->uri_login?>">sign in</a></li>
<?php } ?>
				</ul>
			</div>
		</div>
	</nav>
	<?php
}

function redirect($url){
	header("Location: $url");
	die();
}

function redirect_return() {
	global $site;
	$url = base64_decode($site->post->return);
	header("Location: $url");
	die();
}

function return_obj_fail($str) {
	$o = new StdClass();
	$o->result = "failure";
	$o->value = 0;
	$o->message = $str;
	return $o;
}

function return_obj_success() {
	$o = new StdClass();
	$o->result = "success";
	$o->value = 1;
	return $o;
}

// recursively add slashes to all text fields
function slash($obj){
	$obj2 = new stdClass();
	foreach($obj as $key => $value) {
		if (is_object($value)){
			$obj2->key = slash($value);
		}
		
		if (is_string($value)){
			$obj2->$key = addslashes($value);
		} else {
			$obj2->$key = $value;
		}
	}
	return $obj2;
}

// recursively remove slashes from all text fields of the object
function unslash($obj){
	$obj2 = new stdClass();
	foreach($obj as $key => $value) {
		if (is_object($value)){
			$obj2->key = unslash($value);
		}
		
		if (is_string($value)) {
			$obj2->$key = stripslashes($value);
		} else {
			$obj2->$key = $value;
		}
	}
	return $obj2;
}

// sanitize strings for using in URLs (How's your Hotdog? => hows-your-hotdog
function url_string($str){
	$remove = array(
		"/", "\\", "`", "~", "!", "@", "#", "\$", "%", "^", 
		"&", "*", "(", ")", "+", "=", "?", ",", ".", "<", ">", "'", "\""
	);
	$out = str_replace($remove, "", strtolower($str));
	$replace = array(" ", "_");
	return str_replace($replace, "-", $out);
}

function timer_start() {
	$GLOBALS['exetime'] = microtime(true);
}

function timer_end() {
	$end = microtime(true) - $GLOBALS['exetime'];
	echo "<div class=\"execution-timer code-font small\">execution time: " . $end . " seconds</div>"; 
}

function given_name($fullname) {
	$n = explode(" ", trim($fullname));
	return $n[0];
}

function time_ago($date) {
	global $site;
	
	$now = time();
	$then = format_date($date, 0, "U");
	
	// future-proofing
	if ($now < $then) return "soon";
	
	$delta = $now - $then;
	if ($delta < 60) return "just now";
	else if ($delta < 3600) {
		$t = floor($delta / 60);
		return sprintf("%d minute%s ago", $t, ($t == 1) ? "" : "s");
	} else if ($delta < (3600 * 24)) {
		$t = floor($delta / 3600);
		return sprintf("%d hour%s ago", $t, ($t == 1) ? "" : "s");
	} else if ($delta < (86400 * 7)) {
		$t = floor($delta / 86400);
		return sprintf("%d day%s ago", $t, ($t == 1) ? "" : "s");
	} else if ($delta < (86400 * 30)) {
		$t = floor($delta / (86400 * 7));
		return sprintf("%d week%s ago", $t, ($t == 1) ? "" : "s");
	} else if ($delta < (86400 * 7 * 52)) {
		$t = floor($delta / (86400 * 30));
		return sprintf("%d month%s ago", $t, ($t == 1) ? "" : "s");
	} else {
		$t = floor($delta / (86400 * 7 * 52));
		return sprintf("%d year%s ago", $t, ($t == 1) ? "" : "s");
	}
}

function get_return_url(){
	return base64_encode($_SERVER["REQUEST_URI"]);
}

function now() {
	return date("Y-m-d H:i:s");
}

function page_terms() {
	global $site;
	
	include_once("_inc/head.php");
	navigation();
?>
	<div class="container">
		<h1>Terms of Service</h1>
		<p></p>
	</div>
<?php
	include_once("_inc/foot.php");
}
?>