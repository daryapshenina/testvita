<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$content = '
<p style="text-align:center">
	<span style="font-size:26px"><img alt="" src="http://5za.ru/files/cms/modules/flat_rotate/1.jpg" style="height:240px; width:240px"></span>
</p>
<p style="text-align:center">&nbsp;</p>
<p style="text-align:center">
	<span style="font-size:20px">
		<span style="color:#555555">Заголовок</span>
	</span>
</p>
<p style="text-align:center">
	<span style="color:#555555">
		<span style="font-size:36px">18 000</span>
		<span style="font-size:20px"> руб</span>
	</span>
</p>
';

$content_2 = '
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p style="text-align:center">
	<span style="font-size:72px">Demo</span>
	<br><br>
</p>
<p style="text-align:center">
	<span style="font-size:24px">Разместите здесь<br>свой текст</span>
</p>
';

$stmt_insert = $db->prepare("
	INSERT INTO modules SET
	title = 'Новый - Плашка с вращением',
	module = 'flat_rotate',
	module_csssuf = '',
	pub = '1',
	titlepub = '0',
	enabled = '1',
	description = 'Плашка с вращением',
	content = :content,
	content_2 = :content_2,
	p1 = '280',
	p2 = '400',
	p3 = '#ff9900',
	p4 = 'http://5za.ru',
	p5 = '5',
	p6 = '20',
	p7 = '',
	p8 = '',
	p9 = '',
	p10 = '',
	block = '',
	ordering = '1'
");

$stmt_insert->execute(array('content'=>$content, 'content_2'=>$content_2));

$id = $db->lastInsertId();

Header ("Location: /admin/modules/flat_rotate/".$id); 
exit;

?>