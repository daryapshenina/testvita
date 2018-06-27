<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$content = '
http://5za.ru/files/scroll_images/i1.jpg;
http://5za.ru/files/scroll_images/i10.jpg;
http://5za.ru/files/scroll_images/i2.jpg;
http://5za.ru/files/scroll_images/i3.jpg;
http://5za.ru/files/scroll_images/i5.jpg;
http://5za.ru/files/scroll_images/i6.jpg;
http://5za.ru/files/scroll_images/i4.jpg;
http://5za.ru/files/scroll_images/i8.jpg;
http://5za.ru/files/scroll_images/i9.jpg;
http://5za.ru/files/scroll_images/i7.jpg
';

$stmt_insert = $db->prepare("
	INSERT INTO modules SET
	title = 'Новый - Скроллер',
	module = 'scroll_images',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Модуль прокрутки изображений',
	content = :content,
	content_2 = '',	
	p1 = '5',
	p2 = '200',
	p3 = '1',
	p4 = '1',
	p5 = '1',
	p6 = '',
	p7 = '',
	p8 = '',
	p9 = '',
	p10 = '',
	block = '',
	ordering = '1'
");

$stmt_insert->execute(array('content'=>$content));

$id = $db->lastInsertId();

Header ("Location: /admin/modules/scroll_images/".$id); 
exit;

?>