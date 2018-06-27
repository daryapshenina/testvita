<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$stmt_insert = $db->query("
	INSERT INTO modules SET
	title = 'Форма обратной связи',
	module = 'form',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Форма обратной связи',
	content = '',
	content_2 = '',
	p1 = '0',
	p2 = '',
	p3 = '1',
	p4 = 'Сообщение',
	p5 = '1',
	p6 = 'Ваш email',
	p7 = '0',
	p8 = '1',
	p9 = '25;25;50;100',
	p10 = '',
	block = '',
	ordering = '1'
");

$id = $db->lastInsertId();

Header ("Location: /admin/modules/form/".$id); 
exit;

?>