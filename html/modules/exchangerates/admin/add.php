<?php
// Курс валют >>> добавить
defined('AUTH') or die('Restricted access');

$stmt_insert = $db->query("
	INSERT INTO modules SET
	title = 'Курс валют',
	module = 'exchangerates',
	module_csssuf = '',
	pub = '1',
	titlepub = '1',
	enabled = '1',
	description = 'Курс с сайта Центрального Банка России',
	content = '',
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

$id = $db->lastInsertId();

Header ("Location: /admin/modules/exchangerates/".$id); 
exit;

?>