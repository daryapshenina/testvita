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
<body>
<? frontend_edit(); ?>
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

	<div id="header">
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

	<div id="slider"><? block('slider'); ?></div>

	<div class="module_single">
		<div class="module_single_padding">
			<? block('module-single-1'); ?>
		</div>
	</div>

	<div><? block('img4r'); ?></div>

	<div class="module_single">
		<div class="module_single_padding">
			<? block('module-single-2'); ?>
		</div>
	</div>

	<div id="module_four">
		<div class="module_four_table_main">
			<div class="module_four_table_main_cell_1">
				<div class="module_four_table_1">
					<div class="module_four_1"><div id="module_four_1"><? block('module-four-1'); ?></div></div>
					<div class="module_four_2"><div id="module_four_2"><? block('module-four-2'); ?></div></div>
				</div>
			</div>
			<div class="module_four_table_main_cell_2">
				<div class="module_four_table_2">
					<div class="module_four_3"><div id="module_four_3"><? block('module-four-3'); ?></div></div>
					<div class="module_four_4"><div id="module_four_4"><? block('module-four-4'); ?></div></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="module_paralax_1">
	<div class="module_single">
		<div class="module_single_padding">
			<? block('module-paralax-1'); ?>
		</div>
	</div>
</div>

<div class="white">
	<div class="module_single">
		<div class="module_single_padding">
			<? block('module-single-3'); ?>
		</div>
	</div>

	<div class="module_two">
		<div class="module_two_table_main">
			<div class="module_two_1"><div id="mt_1"><? block('module-two-1'); ?></div></div>
			<div class="module_two_2"><div id="mt_2"><? block('module-two-2'); ?></div></div>
		</div>
	</div>
</div>

<div class="module_paralax_2">
	<div class="module_single">
		<div class="module_single_padding">
			<? block('module-paralax-2'); ?>
		</div>
	</div>
</div>

<div class="white">

	<div class="module_single">
		<div class="module_single_padding">
			<? block('module-single-4'); ?>
		</div>
	</div>

	<div id="module_four">
		<div class="module_four_table_main">
			<div class="module_four_table_main_cell_1">
				<div class="module_four_table_1">
					<div class="module_four_1"><? block('module-four-bottom-1'); ?></div>
					<div class="module_four_2"><? block('module-four-bottom-2'); ?></div>
				</div>
			</div>
			<div class="module_four_table_main_cell_2">
				<div class="module_four_table_2">
					<div class="module_four_3"><? block('module-four-bottom-3'); ?></div>
					<div class="module_four_4"><? block('module-four-bottom-4'); ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="module_two">
		<div class="module_two_table_main">
			<div class="module_two_1"><? block('module-two-3'); ?></div>
			<div class="module_two_2"><? block('module-two-4'); ?></div>
		</div>
	</div>
</div>
<div id="footer"></div>

<? echo $statistics; ?>
</body>
</html>
