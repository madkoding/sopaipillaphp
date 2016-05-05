<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title></title>
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="stylesheet" href="<?php echo \Supernova\Route::getPublicUrl(); ?>/Default/bootstrap.css" type="text/css" media="screen">
	<title>Supernova Framework</title>
</head>

<body>
    <header>
        
    </header>
    <div class="container">
    <?php echo \Supernova\View::getMessage(); ?>
    <?php echo $content_for_layout; ?>
    </div>
</body>
</html>
