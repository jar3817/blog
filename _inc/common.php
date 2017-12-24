<?php

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

?>