<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$stmt_insert = $db->query("
	INSERT INTO modules SET
	title = 'Иконка',
	module = 'icon',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Иконка в формате UTF-8',
	content = 'diamond',
	content_2 = '<p><span style=\"font-size:20px;\">Текст под иконкой</span><br><br></p><p><span style=\"font-size:14px;\">Нажмите на данный текст для редактирования.</span></p>',
	p1 = '1',
	p2 = '1',
	p3 = '25;33;50;100',
	p4 = '250;250',
	p5 = '10;10',
	p6 = '20;20',
	p7 = '100',
	p8 = '#e95097',
	p9 = '',
	p10 = '',
	block = '',
	ordering = '1'
");

$id = $db->lastInsertId();

Header ("Location: /admin/modules/icon/".$id); 
exit;

?>