<?php

// include all libraries
include_once("_inc/common.php");
include_once("_inc/settings.php");
include_once("_inc/database-lib.php");
include_once("_inc/user-lib.php");
include_once("_inc/post-lib.php");
include_once("_inc/comment-lib.php");

timer_start();

// connect to the database
$db = db_connect();

// make life easier
$get = (object)$_GET;
$post = (object)$_POST;

// revive any existing sessions
session_start();

$site = new stdClass();
$site->db = $db;

$user = user_setup();

$site->get = $get;
$site->post = $post;
$site->settings = $settings;
$site->user = $user;
?>