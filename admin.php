<?php
include_once("_inc/main.php");

if (!user_is_manager()) {
	include_once("_inc/head.php");
	navigation();
	echo "<div>Only managers can access this page.</div>";
	include_once("_inc/foot.php");
	die();
}

switch ((isset($post->op)) ? $post->op : "") {
	case "create-post":
	admin_create_post();
	break;
	
	case "edit-post":
	admin_save_post();
	break;
	
	case "post-delete":
	admin_delete_post();
	break;
}

switch ((isset($get->op)) ? $get->op : "") {
	case "new-post":
	admin_new_post();
	break;
	
	case "edit-post":
	admin_edit_post();
	break;
	
	default:
	admin_index();
}	

function admin_index() {
	global $site;
	
	include_once("_inc/head.php");
	navigation();
	$offset = (isset($site->get->page)) ? $site->get->page * $site->settings->posts_per_page : 0;
	$p = post_list(0, $offset, 50);
	
	$p = (isset($p->result)) ? $p->result : null;
?>
	<div class="container">
		<div class="row row-title">
			<!-- <div class="col-md-1"></div> -->
			<div class="col-xs-6 col-sm-6 col-md-3">Title</div>
			<div class="hidden-xs hidden-sm col-md-2">Comments</div>
			<div class="col-xs-3 col-sm-3 col-md-2">Published</div>
			<div class="hidden-xs hidden-sm col-md-2">Created</div>
		</div>
<?php 
	foreach ((array)$p as $post) {
		$post = unslash($post);
		$post->title = (strlen($post->title) > 30) ? substr($post->title, 0, 27) . "..." : $post->title;
?>
		<div class="row">
			<!-- <div class="col-md-1"></div> -->
			<div class="col-xs-6 col-sm-6 col-md-3"><input type="checkbox" name="<?=$post->id?>"> <a href="<?=$site->settings->uri_man_edit_post?>/<?=$post->title_url?>"><?=$post->title?></a></div>
			<div class="hidden-xs hidden-sm col-md-2"><?=$post->comments?></div>
			<div class="col-xs-3 col-sm-3 col-md-2"><?=$post->published?></div>
			<div class="hidden-xs hidden-sm col-md-2"><?=format_date($post->date_created)?></div>
		</div>
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
			<h2>Create Post</h2>
			<form role="form" action="<?=$site->settings->uri_man_new_post?>" method="post" id="addform">
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
					<button type="submit" class="btn btn-primary" id="create" name="create">Create</button>
				</div>
				
				<input type="hidden" name="op" value="create-post">
			</form>
		</div>

		<script type="text/javascript">
			$(document).ready(function($) {
				$('#body').summernote({ 
					height: 350,
					callbacks: {
						onImageUpload: function(files) {
							sendFile(files[0]);
						}
					}
				});
				
				function sendFile(file) {
					data = new FormData();
					data.append("file", file);
					$.ajax({
						data: data,
						type: "POST",
						url: "<?=$site->settings->uri_postimg?>",
						cache: false,
						contentType: false,
						processData: false,
						success: function(url) {
							var node = document.createElement("img");
							node.setAttribute("src", url);
							$("#body").summernote('insertNode', node);
							
							$('<input>').attr({
								type: 'hidden',
								name: 'media[]',
								value: url
							}).appendTo($("#addform"));
						}
					});
				}
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
	
	// insert each media item
	$m = new stdClass();
	foreach ($site->post->media as $media) {
		$m->post = $r->id;
		$m->filename = get_filename($media);
		post_media_add($m);
	}
	
	redirect($site->settings->uri_manager);
}

function admin_edit_post() {
	global $site;
	
	$p = post_get_by_name($site->get->post);
	$p = unslash($p);
	$published = ($p->published) ? " checked=\"checked\"" : "";
	
	include_once("_inc/head.php");
	navigation();
?>
		<div class="container">
			<h2>Edit Post</h2>
			<form role="form" action="<?=$site->settings->uri_man_edit_post?>/<?=$p->title_url?>" method="post" id="editform">
				<div class="form-group">
					<input type="text" id="title" name="title" class="form-control" placeholder="Post Title" required autofocus value="<?=$p->title?>">
				</div>
				<div class="form-group">
					<input type="text" id="title_url" name="title_url" class="form-control" placeholder="post-url-title" required value="<?=$p->title_url?>">
				</div>
				<div class="form-group">
					<textarea id="body" name="body" class="form-control" placeholder="" required><?=$p->content?></textarea>
				</div>
				<div class="form-group">
					<input type="checkbox" name="published"<?=$published?>> Published
				</div>
				<div class="form-group pull-right">
					<button type="submit" class="btn btn-primary" id="create" name="create">Save</button>
					<button type="button" class="btn btn-danger" id="delete" name="delete" data-toggle="modal" data-target="#confirmdelete">Delete</button>
				</div>
				
				<input type="hidden" name="op" value="edit-post">
				<input type="hidden" name="id" value="<?=$p->id?>">
			</form>
		</div>

		<script type="text/javascript">
			$(document).ready(function($) {
				$('#body').summernote({ 
					height: 350,
					callbacks: {
						onImageUpload: function(files) {
							sendFile(files[0]);
						}
					}
				});
				
				function sendFile(file) {
					data = new FormData();
					data.append("file", file);
					$.ajax({
						data: data,
						type: "POST",
						url: "<?=$site->settings->uri_postimg?>",
						cache: false,
						contentType: false,
						processData: false,
						success: function(url) {
							var node = document.createElement("img");
							node.setAttribute("src", url);
							$("#body").summernote('insertNode', node);
							
							$('<input>').attr({
								type: 'hidden',
								name: 'media[]',
								value: url
							}).appendTo($("#editform"));
						}
					});
				}
				
				$("#cancel").click(function() {
					window.location.href="<?=$site->settings->uri_manager?>";
				});
			});
		</script>
		
		<div id="confirmdelete" class="modal fade">
			<form role="form" action="<?=$site->settings->uri_postdelete?>" method="post" id="commentform">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Delete this post?</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<p>You are about to delete this post and any associated comments and media.</p>
								<p>This can't be undone, are you sure you want to proceed?</p>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary" name="">Yes, delete it!</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
				<input type="hidden" name="op" value="post-delete"/>
				<input type="hidden" name="post" value="<?=$p->id?>"/>
			</form>
		</div>
<?php
	include_once("_inc/foot.php");
}

function admin_save_post() {
	global $site;
	
	$o = post_get($site->post->id);
	
	// wasn't, is now
	if ($site->post->published == "on" && !$o->published) {
		$o->published = 1;
		$o->date_published = now();
	}
	
	// was, isn't now
	if (!isset($site->post->published) && $o->published) {
		$o->published = 0;
		$o->date_published = "0000-00-00 00:00:00";
	}
	$o->edited = 1;
	$o->date_edited = now();
	$o->editor = $site->user->id;
	$o->title = $site->post->title;
	$o->title_url = $site->post->title_url;
	$o->content = $site->post->body;
	$o = slash($o);
	
	post_edit($o);
	
	// insert each media item
	$m = new stdClass();
	foreach ($site->post->media as $media) {
		$m->post = $o->id;
		$m->filename = get_filename($media);
		post_media_add($m);
	}
	
	redirect($site->settings->uri_manager);
}

function admin_delete_post() {
	global $site;
	
	post_delete($site->post->post);
	redirect($site->settings->uri_manager);
}

?>