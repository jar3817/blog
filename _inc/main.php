<?php

// just while developing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// include all libraries
include_once("_inc/settings.php");
include_once("_inc/database-lib.php");
include_once("_inc/common.php");
include_once("_inc/post-lib.php");

// connect to the database
$db = db_connect();

// make life easier
$get = (object)$_GET;
$post = (object)$_POST;

// revive any existing sessions
session_start();

$site = new stdClass();
$site->db = $db;
$site->get = $get;
$site->post = $post;
$site->settings = $settings;

?>