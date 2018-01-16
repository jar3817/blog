<?php

function post_add($obj) {
	global $site;
	
	try {
		$sql = "INSERT INTO post (
					author, title, title_url, content, date_created, date_published
				) VALUES (
					?, ?, ?, ?, NOW(), NOW()
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

function post_edit($obj) {
	global $site;
	
	try {
		$sql = "UPDATE post SET 
					author = ?,
					comments = ?,
					title = ?, 
					title_url = ?, 
					content = ?, 
					date_created = ?,
					published = ?,
					date_published = ?,
					edited = ?,
					editor = ?,
					date_edited = ?
				WHERE id = ?
				LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $obj->author, PDO::PARAM_INT);
		$q->bindValue(2, $obj->comments, PDO::PARAM_INT);
		$q->bindValue(3, $obj->title, PDO::PARAM_STR);
		$q->bindValue(4, $obj->title_url, PDO::PARAM_STR);
		$q->bindValue(5, $obj->content, PDO::PARAM_STR);
		$q->bindValue(6, $obj->date_created, PDO::PARAM_STR);
		$q->bindValue(7, $obj->published, PDO::PARAM_INT);
		$q->bindValue(8, $obj->date_published, PDO::PARAM_STR);
		$q->bindValue(9, $obj->edited, PDO::PARAM_INT);
		$q->bindValue(10, $obj->editor, PDO::PARAM_INT);
		$q->bindValue(11, $obj->date_edited, PDO::PARAM_STR);
		$q->bindValue(12, $obj->id, PDO::PARAM_INT);
		$q->execute();
		
		return return_obj_success();
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

function post_get_by_name($name) {
	global $site;
	
	try {
		$sql = "SELECT * FROM post WHERE title_url = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $name, PDO::PARAM_INT);
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

function post_list($published=1, $offset=0, $length=10) {
	global $site;
	
	$pub = ($published) ? " AND published = 1" : "";
	try {
		$sql = "SELECT *
				FROM post
				WHERE 1 $pub
				ORDER BY date_published DESC
				LIMIT ? 
				OFFSET ?";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $length, PDO::PARAM_INT);
		$q->bindValue(2, $offset, PDO::PARAM_INT);
		$q->execute();
		
		$success = return_obj_success();
		while ($r = $q->fetchAll(PDO::FETCH_OBJ)) {	
			return $r;
		}
		
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_slug($o) {
	global $site;
	
	if (!$o->published) {
		return;
	}
	
	$c = ($o->comments == 0) ? "" : sprintf("// %d comment%s", $o->comments, ($o->comments == 1) ? "" : "s");
?>
	<div class="post">
		<h1><a href="/<?=$o->title_url?>"><?=$o->title?></a></h1>
		<h2><?=format_date($o->date_published)?> <?=$c?></h2>
		<p>
			<?=$o->content?>
		</p>
	</div>
<?php
}

function post_index() {
	global $site;
	$offset = (isset($site->get->page)) ? $site->get->page * $site->settings->posts_per_page : 0;
	$posts = post_list(1, $offset, $site->settings->posts_per_page);
	//$posts = (isset($posts->result)) ? $posts->result : null;
	
	$site->extra_js[] = "/assets/js/infinite.js";
	
	include_once("_inc/head.php");
	navigation();
	
	echo "<div id=\"posts\" class=\"container\">\n";
	
	foreach ((array)$posts as $p) {
		post_slug(unslash($p));
	}
	
	if (!$posts) {
		echo "<div>no posts to display...</div>";
	}
	echo "</div>\n";
	echo "<div id=\"loading\"></div>";
	include("_inc/foot.php");
}

function post_scroll() {
	global $site;
	
	$offset = (isset($site->get->page)) ? $site->get->page * $site->settings->posts_per_page : 0;
	$posts = post_list(1, $offset, $site->settings->posts_per_page);
	
	foreach ((array)$posts as $p) {
		post_slug(unslash($p));
	}
}

function post_single($name) {
	global $site;
	
	$p = post_get_by_name($name);
	$p = unslash($p);
	
	include_once("_inc/head.php");
	navigation();
	$comments = comment_list($p->id);
?>
<div class="container">
	<div class="post">
		<h1><?=$p->title?></h1>
		<h2><?=format_date($p->date_published)?></h2>
		<p>	
			<?=$p->content?>
		</p>
		
		<div id="comments">
<?php if (user_is_logged_in()) { ?>
			<button class="btn btn-default" data-toggle="modal" data-target="#newcomment">Add a comment</button>
			<?=comment_modal($p->id)?>
<?php } ?>
<?php 
		foreach ((array)$comments as $c) { 
			$c = unslash($c);
?>
			<hr>
			<div class="comment">
				<a name="<?=$c->comment_key?>"></a>
				<div class="comment-header"><?=($c->user) ? given_name($c->author) : $c->name?> // <span class="code-font" title="<?=format_date($c->date_created, 1)?>"><?=time_ago($c->date_created)?></span></div>
				<div class="comment-body"><?=$c->body?></div>
			</div>
<?php } ?>
		</div>
	</div>
</div>
<?
	include("_inc/foot.php");
}

function post_view() {
	global $site;
	
	if (isset($site->get->postname) && strlen($site->get->postname) > 0) {
		post_single($site->get->postname);
	} else {
		post_index();
	}
	
	die();
}

function post_add_comment() {
	global $site;
	
	if (!user_is_logged_in()) {
		redirect($settings->uri_login);
		die();
	}
	
	$p = post_get($site->post->post);
	$o = new stdClass();
	
	$o->user 	= $site->user->id;
	$o->post 	= $p->id;
	$o->body 	= $site->post->body;
	$o->address	= $_SERVER['REMOTE_ADDR'];
	
	$o = slash($o);
	$r = comment_add($o);
	
	// insert each media item
	$m = new stdClass();
	foreach ($site->post->media as $media) {
		$m->comment = $r->id;
		$m->filename = get_filename($media);
		comment_media_add($m);
	}
	
	redirect_return();
}

function post_publish($id) {
	global $site;
	
	$p = post_get($id);
	$p->published = 1;
	$p->date_published = now();
	
	post_edit($p);
}

function post_unpublish($id) {
	global $site;
	
	$p = post_get($id);
	$p->published = 0;

	post_edit($p);
}

function post_img_upload() {
	global $site;
	
	if ($_FILES['file']['name']) {
		if (!$_FILES['file']['error']) {
			$name = generate_key(8);
			$ext = explode('.', $_FILES['file']['name']);
			$filename = sprintf("%s.%s", $name, $ext[sizeof($ext)-1]);
			$destination = sprintf("%s/%s/%s", $site->settings->site_path, $site->settings->uri_uploaddir, $filename);
			$location = $_FILES["file"]["tmp_name"];
			move_uploaded_file($location, $destination);
			echo sprintf("%s/%s", $site->settings->uri_uploaddir, $filename);
		} else {
		  echo $settings->uri_img_oops;
		}
	}
	
	die();
}

function post_media_get($id) {
	global $site;
	
	try {
		$sql = "SELECT * FROM post_media WHERE id = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $id, PDO::PARAM_INT);
		$q->execute();
		
		while ($r = $q->fetch(PDO::FETCH_OBJ)) {
			return $r;
		}
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_media_get_list($id) {
	global $site;
	
	try {
		$sql = "SELECT * FROM post_media WHERE post = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $id, PDO::PARAM_INT);
		$q->execute();
		
		while ($r = $q->fetchAll(PDO::FETCH_OBJ)) {
			return $r;
		}
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_media_add($obj) {
	global $site;
	
	try {
		$sql = "INSERT INTO post_media (
					post, filename, date_added
				) VALUES (
					?, ?, NOW()
				)";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $obj->post, PDO::PARAM_INT);
		$q->bindValue(2, $obj->filename, PDO::PARAM_STR);
		$q->execute();
		
		$r = return_obj_success();
		$r->id = $site->db->lastInsertId();
		return $r;
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_media_delete($id) {
	global $site;
	
	try {
		$m = post_media_get($id);
		$filepath = sprintf("%s%s/%s", $site->settings->site_path, $site->settings->uri_uploaddir, $m->filename);
		unlink($filepath);
		
		$sql = "DELETE FROM post_media WHERE id = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $id, PDO::PARAM_INT);
		$q->execute();
		
		$r = return_obj_success();
		return $r;
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function post_delete($id) {
	global $site;
	
	try {
		$sql = "DELETE FROM post WHERE id = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $id, PDO::PARAM_INT);
		$q->execute();
		
		$m = post_media_get_list($id);
		foreach ((array) $m as $media) {
			post_media_delete($media->id);
		}
		
		$c = comment_list($id, 0, 0, 1000);
		foreach ((array) $c as $comment) {
			comment_delete($comment->id);
		}
		
		$r = return_obj_success();
		return $r;
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

?>