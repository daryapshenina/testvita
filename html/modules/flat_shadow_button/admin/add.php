<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$content = '
<p>
	<img alt="" src="http://5za.ru/files/cms/modules/flat_shadow_button/4.jpg" style="width: 640px; height: 480px;">
</p>
<p>
	<br>
</p>
<p style="text-align: center;">
	<span style="color:#444444;">
		<span style="font-size:20px;">Модуль с затемнением</span>
	</span>
</p>
<ul>
	<li>
		<span style="color:#444444;">текст</span>
	</li>
	<li>
		<span style="color:#444444;">изображение</span>
	</li>
	<li>
		<span style="color:#444444;">видео</span>
	</li>
</ul>
';

$stmt_insert = $db->prepare("
	INSERT INTO modules SET
	title = 'Плашка с затемнением и кнопкой',
	module = 'flat_shadow_button',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Плашка с затемнением и кнопкой',
	content = :content,
	content_2 = '/',
	p1 = '1',
	p2 = '320',
	p3 = '250',
	p4 = '25;25;50;100',
	p5 = '10;20',
	p6 = '0',
	p7 = 'Подробнее',
	p8 = '#ff9a35',
	p9 = '#000000',
	p10 = '0.6',
	block = '',
	ordering = '1'
");

$stmt_insert->execute(array('content'=>$content));

$id = $db->lastInsertId();

Header ("Location: /admin/modules/flat_shadow_button/".$id);
exit;

?>