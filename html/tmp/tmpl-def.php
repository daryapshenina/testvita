<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><? set_title(); ?></title>
<meta name="description" content="<? set_description(); ?>" />
<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>
<link rel="stylesheet" href="/tmp/main.css" type="text/css" />
<link rel="stylesheet" href="/tmp/<? set_theme() ?>style.css" type="text/css" />
<link rel="stylesheet" href="/tmp/adaptive.css" type="text/css" />
<script type="text/javascript" src="/js/jquery.min.js" ></script>
<script src="/js/dan.framework.js" type="text/javascript"></script>
<script src="/tmp/template.js" type="text/javascript"></script>
<script src="/js/dan_lightbox/dan.lightbox.js" type="text/javascript"></script>
<link rel="stylesheet" href="/js/dan_lightbox/dan.lightbox.css" type="text/css" />
<link rel="stylesheet" href="/js/dan.framework.css" type="text/css" />
<link href="/modules/calltoorder/frontend/dan_cto.css" type="text/css" media="all" rel="stylesheet" />
<script type="text/javascript" src="/modules/calltoorder/frontend/dan_cto.js"></script>
<? $head->out(); ?>
<script src="/js/dan/dan.js" type="text/javascript"></script>
<link rel="stylesheet" href="/js/dan/dan.css" type="text/css" />
</head>
<body class="body-def">
<div class="white">
	<div id="top">
		<div id="block_two_main">
			<div id="block_two_left">
				<div id="block_two_left_table">
					<? block('top-left'); ?>
				</div>
			</div>
			<div id="block_two_right">
				<div id="block_two_right_table">
					<? block('top-right'); ?>
				</div>
			</div>
		</div>
	</div>

	<div id="header" class="header-def">
		<div id="block_two_main">
			<div id="block_two_left">
				<div id="block_two_left_table">
					<? block('header'); ?>
				</div>
			</div>
			<div id="block_two_right">
				<div id="block_two_right_table">
					<? block('topmenu'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="component_main">
		<div class="component_padding">
			<? block('breadcrumbs'); ?>
			<? component(); ?>
		</div>
	</div>


</div>


<div id="footer"></div>


<!--
<? component(); ?><
-->
<? echo $statistics; ?>
</body>
</html>
