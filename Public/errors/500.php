<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title></title>
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="stylesheet" href="<?php echo \Supernova\Route::getPublicUrl(); ?>/errors/style.css" type="text/css" media="screen">
</head>
<body>
	<div id="wrapper">
		<header id="header">
			<!-- Header Left : Logo and Site name -->
			<div class="header-left">
				<!-- Header Logo --><img src='<?php echo \Supernova\Route::getPublicUrl(); ?>/errors/img/snf_logo_t.png' />
				<!-- Site name --><h1></h1>
			</div>
		</header><!-- #header-->
		<section id="middle">
			<div id="container">
				<div id="content">
                    <div style="float:left; width: 120px"><img src='<?php echo \Supernova\Route::getPublicUrl(); ?>/errors/img/error500.jpg' /></div>
                    <div style="float:left; width: 80%">
                        <h2>Error 500</h2>
                        <h3><?php echo __("OOPS, Something went wrong"); ?></h3>
                        <p><?php echo __("The page you are trying to access seems to no longer exists."); ?></p>
                        <p><?php echo __("Check you address bar, meaby you got a mistaken address"); ?></p>
                    </div>
                    <div style="clear:both;"></div>
                    <br/><br/>
<?php
if (!empty($encodedError && defined("CONTACT_EMAIL"))) {
    echo "<p>".__("If the problem persist, please send this text to the administrator email:").\Supernova\Helper::createLink(["href" => "mailto:".CONTACT_EMAIL, "text" => CONTACT_EMAIL, "class" => ["adminemail"]])."</p>";
    echo "<pre class='encodedError'>";
    echo $encodedError;
    echo "</pre>";
}
?>
				</div><!-- #content-->
			</div><!-- #container-->
		</section><!-- #middle-->
	</div><!-- #wrapper -->
	<footer id="footer">
	</footer><!-- #footer -->
</body>
</html>
