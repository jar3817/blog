<?php

function user_auth_form() {
	global $site;
	
	include_once("_inc/head.php");
	navigation();
?>
	<div class="container">
		<form class="form-signin">
			<h2 class="form-signin-heading">Login</h2>
			<label for="inputEmail" class="sr-only">Email address</label>
			<input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
			<label for="inputPassword" class="sr-only">Password</label>
			<input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
			<div class="checkbox">
				<label>
					<input type="checkbox" value="remember-me"> Remember me
				</label>
			</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		</form>

    </div>
<?php
	include_once("_inc/foot.php");
	die();
}

function user_is_logged_in() {
	return false;
}


?>