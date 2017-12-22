<?php

function comment_add($obj) {
	global $site;
	
	try {
		$sql = "INSERT INTO comment (
					comment_key, user, post, date_created,
					body, name, address, url, email
				) VALUES (
					?, ?, ?, NOW(),
					?, ?, ?, ?, ?
				)";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, generate_key(), PDO::PARAM_STR);
		$q->bindValue(2, $obj->user, PDO::PARAM_INT);
		$q->bindValue(3, $obj->post, PDO::PARAM_INT);
		$q->bindValue(4, $obj->body, PDO::PARAM_STR);
		$q->bindValue(5, $obj->name, PDO::PARAM_STR);
		$q->bindValue(6, $obj->address, PDO::PARAM_STR);
		$q->bindValue(7, $obj->url, PDO::PARAM_STR);
		$q->bindValue(8, $obj->email, PDO::PARAM_STR);
		$q->execute();
		
		$r = return_obj_success();
		$r->id = $site->db->lastInsertId();
		return $r;
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function comment_edit($obj) {
	global $site;
	
	try {
		$sql = "UPDATE comment SET 
					comment_key = ?, 
					user = ?, 
					post = ?, 
					published = ?, 
					date_created = ?,
					body = ?,
					name = ?,
					address = ?,
					url = ?,
					email = ?
				WHERE id = ?
				LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $obj->comment_key, PDO::PARAM_STR);
		$q->bindValue(2, $obj->user, PDO::PARAM_INT);
		$q->bindValue(3, $obj->post, PDO::PARAM_INT);
		$q->bindValue(4, $obj->published, PDO::PARAM_INT);
		$q->bindValue(5, $obj->date_created, PDO::PARAM_STR);
		$q->bindValue(6, $obj->body, PDO::PARAM_STR);
		$q->bindValue(7, $obj->name, PDO::PARAM_STR);
		$q->bindValue(8, $obj->address, PDO::PARAM_STR);
		$q->bindValue(9, $obj->url, PDO::PARAM_STR);
		$q->bindValue(10, $obj->email, PDO::PARAM_STR);
		$q->bindValue(11, $obj->id, PDO::PARAM_INT);
		$q->execute();
		
		return return_obj_success();
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function comment_get($id) {
	global $site;
	
	try {
		$sql = "SELECT * FROM comment WHERE id = ? LIMIT 1";
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

function comment_list($post, $offset=0, $length=50) {
	global $site;
	
	try {
		$sql = "SELECT *
				FROM comment
				WHERE post = ?
				ORDER BY date_created DESC
				LIMIT ? 
				OFFSET ?";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $post, PDO::PARAM_INT);
		$q->bindValue(2, $length, PDO::PARAM_INT);
		$q->bindValue(3, $offset, PDO::PARAM_INT);
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