<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html> <!--<![endif]-->
<head>

	<meta charset="utf-8">
	<title><?php echo $title; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!--link type="text/css" href="<?php //echo $this->url('css_assets'); ?>/base.css" rel="stylesheet" />
	<link type="text/css" href="<?php //echo $this->url('css_assets'); ?>/skeleton.css" rel="stylesheet" />
	<link type="text/css" href="<?php //echo $this->url('css_assets'); ?>/layout.css" rel="stylesheet" /-->	
	<link type="text/css" href="<?php echo $this->url('css_assets'); ?>/style.css" rel="stylesheet"/>

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>
	<div id="#container" class="container">		
        <?php echo $content; ?>
	</div>
</body>
</html>