<?php

// begin a transaction
function db_begin(){
	global $db;
	if (!isset($GLOBALS['open_transaction']) || (!$GLOBALS['open_transaction'])) {
		$db->beginTransaction();
		$GLOBALS['open_transaction'] = true;
	}
}

// commit a transaction
function db_commit(){
	global $db;
	if (isset($GLOBALS['open_transaction']) && $GLOBALS['open_transaction']) {
		$db->commit();
		$GLOBALS['open_transaction'] = false;
	}
}

function db_connect() {
	global $settings;
	if (!isset($settings)){
		die("Unable to connect to database...");
	}

	try {
		// open a connection
		$db = new PDO(
			"mysql:host={$settings->db_host};dbname={$settings->db_schema};charset=utf8mb4", 
			$settings->db_user, 
			$settings->db_pass
		);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		
		// set default timezone to something other than the system zone
		$q = $db->prepare("SET time_zone = ?");
		$q->bindValue(1, $settings->db_timezone, PDO::PARAM_STR);
		$q->execute();
		
		return $db;
	} catch(PDOException $e) {
		echo "<h2>{$e->getMessage()}</h2>";
		echo "<pre>{$e->getTraceAsString()}</pre>";
	}
}

// die and rollback
function db_die($msg){
	if (isset($GLOBALS['open_transaction']) && $GLOBALS['open_transaction']){
		db_rollback();
		die_gracefully("<h3>Database Problem</h3><p>Transaction rolled back - $msg</p>");
	} else {
		die_gracefully("<h3>Database Problems</h3><p>$msg</p>");
	}
}

function db_error($sql) {
	db_die(mysql_error() . ": <p><strong>$sql</strong></p>");
}

// rollback a failed transaction
function db_rollback(){
	global $db;
	$db->rollBack();
	$GLOBALS['open_transaction'] = false;
}
?>