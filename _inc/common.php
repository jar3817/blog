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

?>