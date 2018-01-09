<?php

function user_auth_form() {
	global $site;
	
	$site->extra_js[] = "/assets/js/firebaseui-2.5.1.js";
	$site->extra_css[] = "/assets/css/firebaseui-2.5.1.css";
	
	include("_inc/head.php");
	navigation();

?>
<div class="container">	
	<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
	<script>
	  // Initialize Firebase
	  var config = {
		apiKey: "AIzaSyABYaquSByTS66-xX4EIOOP_3JiElzG_O0",
		authDomain: "joe-reid-blog.firebaseapp.com",
		databaseURL: "https://joe-reid-blog.firebaseio.com",
		projectId: "joe-reid-blog",
		storageBucket: "",
		messagingSenderId: "909475805036"
	  };
	  firebase.initializeApp(config);
	</script>

	<script type="text/javascript">
		var uiConfig = {
			signInSuccessUrl: '/process-signin',
			signInFlow: 'redirect',
			signInOptions: [
				{
					provider: firebase.auth.GoogleAuthProvider.PROVIDER_ID,
					authMethod: 'https://accounts.google.com',
					clientId: '895430332496-av93kv5330lbfes7e39kbin7ld5t7bqs.apps.googleusercontent.com'
				},
				{
					provider: firebase.auth.FacebookAuthProvider.PROVIDER_ID,
					scopes: ['public_profile','email'],
					customParameters: {	
						auth_type: 'reauthenticate' // Forces password re-entry.
					}
				},
				{
					provier: firebase.auth.EmailAuthProvider.PROVIDER_ID,
					requireDisplayName: true
				}
			],

			tosUrl: '/terms'
		};

		var ui = new firebaseui.auth.AuthUI(firebase.auth()); 
		ui.start('#firebaseui-auth-container', uiConfig); // will wait for DOM to load
	</script>
	
	<p>Login using one of these:</p>
	<div id="firebaseui-auth-container"></div>
	<div id="login-pending"></div>
</div>
<?php

include("_inc/foot.php");
}

function user_is_logged_in() {
	global $site;
	return isset($site->user->id) && is_numeric($site->user->id) && $site->user->id > 0;
}

function user_login($o) {
	global $site;
	
	try {
		$sql = "INSERT INTO login (
					user, ip, date_added
				) VALUES (
					?,INET_ATON(?),NOW()
				)";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $o->id, PDO::PARAM_INT);
		$q->bindValue(2, "10.1.1.1", PDO::PARAM_STR);
		$q->execute();
	
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
	
	// update the login and expire times
	user_edit($o);
	
	$_SESSION['user']		= $o->id;
	$_SESSION['user_id']	= $o->user_id;
	$_SESSION['expires']	= time() + $site->settings->login_ttl;
	
	return return_obj_success();
}

function user_logout() {
	global $site;
	
	setcookie(session_name(), '', 100);
	session_unset();
	$_SESSION = array();
	session_destroy();
}

function user_setup() {
	$u = new stdClass();
	
	if (isset($_SESSION['expires']) && $_SESSION['expires'] <= time()) {
		user_logout();
	}
	
	if (isset($_SESSION['user']) && is_numeric($_SESSION['user'])){
		$u = user_get($_SESSION['user']);
	} else {
		$u->id = 0;
		$u->name = "nobody";
		$u->email = "nobody@example.net";
	}
	
	return $u;
}

function user_get($id) {
	global $site;
	
	try {
		$sql = "SELECT u.*, m.id AS manager 
				FROM user u
				LEFT OUTER JOIN manager m ON m.user = u.id
				WHERE u.id = ? 
				LIMIT 1";
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

function user_get_by_userid($id) {
	global $site;
	
	try {
		$sql = "SELECT * FROM user WHERE user_id = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $id, PDO::PARAM_STR);
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

function user_get_by_email($email) {
	global $site;
	
	try {
		$sql = "SELECT * FROM user WHERE email = ? LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $email, PDO::PARAM_STR);
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

function user_edit($obj) {
	global $site;
	
	try {
		$sql = "UPDATE user SET 
					email = ?, 
					name = ?, 
					user_id = ?,
					provider = ?,
					picture_url = ?,
					auth_time = ?,
					expire_time = ?
				WHERE id = ?
				LIMIT 1";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $obj->email, PDO::PARAM_STR);
		$q->bindValue(2, $obj->name, PDO::PARAM_STR);
		$q->bindValue(3, $obj->user_id, PDO::PARAM_STR);
		$q->bindValue(4, $obj->provider, PDO::PARAM_STR);
		$q->bindValue(5, $obj->picture_url, PDO::PARAM_STR);
		$q->bindValue(6, $obj->auth_time, PDO::PARAM_INT);
		$q->bindValue(7, $obj->expire_time, PDO::PARAM_INT);
		$q->bindValue(8, $obj->id, PDO::PARAM_INT);
		$q->execute();
		
		return return_obj_success();
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

function user_create($obj) {
	global $site;
	
	try {
		$sql = "INSERT INTO user (
					email, name, user_id, provider, picture_url, auth_time, expire_time
				) VALUES (
					?,?,?,?,?,?,?
				)";
		$q = $site->db->prepare($sql);
		$q->bindValue(1, $obj->email, PDO::PARAM_STR);
		$q->bindValue(2, $obj->name, PDO::PARAM_STR);
		$q->bindValue(3, $obj->user_id, PDO::PARAM_STR);
		$q->bindValue(4, $obj->provider, PDO::PARAM_STR);
		$q->bindValue(5, $obj->picture_url, PDO::PARAM_STR);
		$q->bindValue(6, $obj->auth_time, PDO::PARAM_STR);
		$q->bindValue(7, $obj->expire_time, PDO::PARAM_STR);
		$q->execute();
		
		return return_obj_success();
	} catch (PDOException $e) {
		return return_obj_fail($e->getMessage());
	}
}

// called after successful authentication
function user_auth_callback() {
	global $site;
	include_once("_inc/head.php");
	navigation();
?>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
<script>
	var config = {
		apiKey: "AIzaSyABYaquSByTS66-xX4EIOOP_3JiElzG_O0",
		authDomain: "joe-reid-blog.firebaseapp.com",
		databaseURL: "https://joe-reid-blog.firebaseio.com",
		projectId: "joe-reid-blog",
		storageBucket: "",
		messagingSenderId: "909475805036"
	};
	firebase.initializeApp(config);
	
	firebase.auth().onAuthStateChanged(function(user) {
		user.getIdToken().then(function(token) { 
			$.post(
				"/process-signin2", 
				{"email": user.email, "name": user.displayName, "uid" : user.uid, "provider" : user.providerId, "token" : token},
				function(result) {
					//alert(result);
					window.location.href = "/";
				}
			);
		});
	});
</script>
<?php
	include_once("_inc/foot.php");
}

// decode the user token from firebase and if it checks out, create a user if needed and start session for them
function user_auth_callback2() {
	global $site;
	set_include_path(get_include_path() . PATH_SEPARATOR . "_inc/php-jwt/src");

	require("JWT.php");
	require("BeforeValidException.php");
	require("ExpiredException.php");
	require("SignatureInvalidException.php");

	//die(var_dump($site->post));
	
	$certurl = "https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com";
	$c = file_get_contents($certurl);
	$certs = json_decode($c, true);

	try {
		$u = \Firebase\JWT\JWT::decode($site->post->token, $certs, array('RS256'));
	} catch (\Firebase\JWT\ExpiredException $e) {
		die($e->getMessage() . "\n");
	} catch (\Firebase\JWT\SignatureInvalidException $e) {
		die($e->getMessage() . "\n");
	} catch (\Firebase\JWT\BeforeValidException $e) {
		die($e->getMessage() . "\n");
	}

	$valid = true;
	$now = time();

	// issued-at-time must be in the past
	if ($u->iat >= $now)
		$valid = false;

	// expire time must be in the future
	if ($u->exp <= $now)
		$valid = false;

	// audience must be my project
	if ($u->aud != "joe-reid-blog")
		$valid = false;

	// issuer just be this
	if ($u->iss != "https://securetoken.google.com/joe-reid-blog")
		$valid = false;

	// subject and uid have to match	
	if ($u->sub != $u->user_id)
		$valid = false;

	if (!$valid) {
		echo "token not valid";
		return;
	} else {
		echo "token valid!";
	}
	
	$user = user_get_by_userid($site->post->uid);
	if (!$user) {
		user_create(
			(object) array(
				"email" => $u->email, 
				"name" => $u->name, 
				"user_id" => $u->user_id,
				"provider" => $u->firebase->sign_in_provider,
				"picture_url" => $u->picture,
				"auth_time" => $u->iat,
				"expire_time" => $u->exp
			)
		);
		$user = user_get_by_userid($u->user_id);
	} else {
		$user->auth_time = $u->iat;
		$user->expire_time = $u->exp;
	}
	
	user_login($user);
}

function user_is_manager() {
	global $site;
	
	return user_is_logged_in() && is_numeric($site->user->manager);
}
?>