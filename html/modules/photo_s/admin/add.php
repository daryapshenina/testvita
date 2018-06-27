<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$stmt_insert = $db->query("
	INSERT INTO modules SET
	title = 'Изображение с описанием',
	module = 'photo_s',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'При наведении - показывается квадрат с текстом',
	margin_w = '5',
	margin_h = '5',
	bg_color = '#ffffff',	
	content = '<p><span style=\"color:#696969;\"><span style=\"font-size:20px;\">Заголовок</span></span></p>',
	content_2 = '<p><span style=\"color:#696969;\">Тест, тест. тест, тест, тест</span></p>',
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

Header ("Location: /admin/modules/photo_s/".$id); 
exit;

?>