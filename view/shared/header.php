<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"><!--<![endif]-->

<head>
	<title>BGI</title>

	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta charset="UTF-8" />

	<link rel="stylesheet" type="text/css" href="<?php echo flake_path('css/all.css'); ?>">
</head>

<body>
	<!--[if lt IE 7]>
		<p class="chromeframe" style="background:#eee; padding:10px; width:100%">Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p>
	<![endif]-->

	<div class="flakes-frame"><!-- closed in footer -->
		<div class="flakes-navigation">
			<a href="/index.php" class="logo">
				<h1 width="120">BGI</h1>
			</a>

			<ul>
				<li class="title">Navigation</li>
				<?php if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']): ?>
					<li><a href="/home">Home</a></li>
					<li><a href="#">Tracking</a></li>
					<li><a href="#">Metrics</a></li>
					<li><a href="#">Goals</a></li>
					<li><a href="/api/logout">Logout</a></li>
				<?php else: ?>
					<li><a href="/register">Register</a></li>
					<li><a href="/login">Login</a></li>
				<?php endif; ?>
			</ul>

			<p class="foot">
				Created by <a href="http://ethanjoachimeldridge.info">Ethan</a></br>
				© Ethan J. Eldridge 2014 <?php if( date('Y') != '2014' ){ echo '-' . date('Y'); } ?>
			</p>
		</div>

		<div class="flakes-content"><!-- closed in footer -->

			<div class="flakes-mobile-top-bar">
				<a href="/index.php" class="logo-wrap">
					<h1 width="120" height="30px">BGI</h1>
				</a>

				<a href="" class="navigation-expand-target">
					<img src="<?php echo flake_path('img/site-wide/navigation-expand-target.png'); ?>" height="26px">
				</a>
			</div>

			<div class="view-wrap"><!-- closed in footer -->
