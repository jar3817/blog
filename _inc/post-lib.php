<?php

function post_add($obj) {
	global $site;
	
	try {
		$sql = "INSERT INTO post (
					author, title, title_url, content, date_created
				) VALUES (
					?, ?, ?, ?, NOW()
				)";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $obj->author, PDO::PARAM_INT);
		$q->bindValue(2, $obj->title, PDO::PARAM_STR);
		$q->bindValue(3, $obj->title_url, PDO::PARAM_STR);
		$q->bindValue(4, $obj->content, PDO::PARAM_STR);
		$q->execute();
		
		$r = return_obj_success();
		$r->id = $site->db->lastInsertId();
		return $r;
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_get($id) {
	global $site;
	
	try {
		$sql = "SELECT * FROM post WHERE id = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $id, PDO::PARAM_INT);
		$q->execute();
		while ($r = $q->fetch(PDO::FETCH_OBJ)) {
			$tmp = return_obj_success();
			$tmp->result = $r;
			return $r;
		}
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_list($offset=0, $length=10) {
	global $site;
	
	try {
		$sql = "SELECT id, user_id, category, post_date, title, link_title, published, hidden
				FROM post
				ORDER BY post_date DESC
				LIMIT ? 
				OFFSET ?";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $length, PDO::PARAM_INT);
		$q->bindValue(2, $offset, PDO::PARAM_INT);
		$q->execute();
		
		$success = return_obj_success();
		while ($r = $q->fetchAll(PDO::FETCH_OBJ)) {	
			$success->result = $r;
			return $success;
		}
		
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

?>