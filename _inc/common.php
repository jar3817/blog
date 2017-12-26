<?php

// UTC time in the database, converts to user's timezone
function format_date($date, $timetoo = 0, $format = "M j, Y") {
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
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
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
				<ul class="nav navbar-nav">
					<li><a href="/hist">History</a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
<?php 
	if (user_is_logged_in()) { 
		//$c = message_unread_count($site->user->id);
		$c = 0;
		$unread = ($c > 0) ? " <span class=\"badge badge-error\">$c</span>" : "";
?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span><?=$unread?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?=$site->settings->uri_profile?>">Profile</a></li>
							<li><a href="<?=$site->settings->uri_settings?>">Settings</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="<?=$site->settings->uri_logout?>">Signout</a></li>
						</ul>
					</li>
<?php } else { ?>
					<li class=""><a href="<?=$site->settings->uri_login?>"><span class="glyphicon glyphicon-user"></span></a></li>
<?php } ?>
				</ul>
			</div>
		</div>
	</nav>
	<?php
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
?>