<!DOCTYPE html>
<html>
	<head>
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-86166541-2"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'UA-86166541-2');
		</script>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="google-signin-client_id" content="895430332496-av93kv5330lbfes7e39kbin7ld5t7bqs.apps.googleusercontent.com">
		
		<title><?=$site->settings->site_name?></title>

		<link href="/assets/css/main.css" rel="stylesheet">
<?php foreach ((array) $site->extra_css as $css) { ?>
		<link href="<?=$css?>" rel="stylesheet">
<?php } ?>
			
		<script src="/assets/js/jquery-3.2.1.min.js"></script>
		<script src="/assets/js/jquery-ui-1.12.1.min.js"></script>
		<script src="/assets/js/bootstrap-3.3.7.min.js"></script>
		<script src="/assets/js/summernote.min.js"></script>
<?php foreach ((array) $site->extra_js as $js) { ?>
		<script src="<?=$js?>"></script>
<?php } ?>
	</head>
	<body>
