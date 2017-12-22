<?php
error_reporting(E_ALL & ~E_STRICT);
date_default_timezone_set('UTC');

$settings = new stdClass();

// site settings
$settings->site_name	= "Joe's Life";
$settings->site_path	= "/var/www/domains/blog.reid.ws";
$settings->site_url		= "https://reid.ws";


// database settings
$settings->db_host 		= "localhost";
$settings->db_user 		= "root";
$settings->db_pass 		= "";
$settings->db_schema 	= "blog";
$settings->db_timezone	= "+00:00";	// store everything in GMT

?>