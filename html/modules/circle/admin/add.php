<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$stmt_insert = $db->query("
	INSERT INTO modules SET
	title = 'Круг с анимацией',
	module = 'circle',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Круг с анимацией при наведении',
	width_f = '250',
	height_f = '350',
	padding_w = '20',
	padding_h = '10',
	color = '#ffffff',
	bg_color = '#e95097',	
	content = 'ТЕСТ',
	content_2 = '<p style=\"text-align: center;\"><span style=\"font-size:24px;\"><strong>Заголовок</strong></span></p><p style=\"text-align: center;\"><br></p><p style=\"text-align: center;\">Тест, текст, текст, текст, текст.</p>',
	p1 = '24',
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

Header ("Location: /admin/modules/circle/".$id); 
exit;

?>