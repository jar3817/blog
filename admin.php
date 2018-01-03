<?php
include_once("_inc/main.php");

if (!user_is_manager()) {
	include_once("_inc/head.php");
	navigation();
	echo "<div>Only managers can access this page.</div>";
	include_once("_inc/foot.php");
}

switch ((isset($post->op)) ? $post->op : "") {
	case "create-post":
	admin_create_post();
	break;
}

switch ((isset($get->op)) ? $get->op : "") {
	case "new-post":
	admin_new_post();
	break;
	
	default:
	admin_index();
}	

function admin_index() {
	global $site;
	
	include_once("_inc/head.php");
	navigation();
	$p = post_list(0, 50, 0);
	$p = (isset($p->result)) ? $p->result : null;
?>
	<div class="container">
<?php 
	foreach ((array)$p as $post) {
		$post = unslash($post);
?>
		<div><?=$post->title?></div>
<?php } ?>
	</div>
<?php
	include_once("_inc/foot.php");
}

function admin_new_post() {
	global $site;
	include_once("_inc/head.php");
	navigation();
?>
		<div class="container">
			<form role="form" action="<?=$site->settings->uri_man_new_post?>" method="post">
				<div class="form-group">
					<input type="text" id="title" name="title" class="form-control" placeholder="Post Title" required autofocus>
				</div>
				<div class="form-group">
					<input type="text" id="title_url" name="title_url" class="form-control" placeholder="post-url-title" required>
				</div>
				<div class="form-group">
					<textarea id="body" name="body" class="form-control" placeholder="" required></textarea>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" id="create" name="create"/>Create</button>
				</div>
				
				<input type="hidden" name="op" value="create-post">
			</form>
		</div>

		<script type="text/javascript">
			$(document).ready(function($) {
				$('#body').summernote({ 
					height: 350
				});
			});
		</script>
<?php
	include_once("_inc/foot.php");
}

function admin_create_post() {
	global $site;
	
	$o = new stdClass();
	
	$o->author = $site->user->id;
	$o->title = $site->post->title;
	$o->title_url = $site->post->title_url;
	$o->content = $site->post->body;
	
	$o = slash($o);
	
	$r = post_add($o);
	
}

?>