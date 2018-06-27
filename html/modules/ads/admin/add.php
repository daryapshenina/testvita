<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT'].'/modules/ads/classes/modAds.php';

$mod_ads = new modAds();
$content = serialize($mod_ads);

$stmt_insert = $db->prepare("
	INSERT INTO modules SET
	title = 'Объявления',
	module = 'ads',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Объявления',
	margin_w = '0',
	margin_h = '0',
	bg_color = '',	
	content = :content,
	content_2 = '',
	p1 = '',
	p2 = '',
	p3 = '',
	p4 = '',
	p5 = '',
	p6 = '',
	p7 = '',
	p8 = '',
	p9 = '',
	p10 = '',
	block = '',
	ordering = '1'
");

$stmt_insert->execute(array('content' => $content));

$id = $db->lastInsertId();

Header ("Location: /admin/modules/ads/".$id); 
exit;

?>