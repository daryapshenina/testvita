<?php
// Добавляем новое изображение
defined('AUTH') or die('Restricted access');

// Перед тем как добавить товар - проверяем - есть ли разделы
$stmt_section = $db->query('SELECT id FROM com_photo_section');
if($stmt_section->rowCount() == 0)
{
	function a_com()
	{
		echo
		'
		<div id="main-top">Отсутствуют разделы</div>
		<div style="padding: 10px">Отсутствуют разделы. Необходимо завести хотя бы один раздел.</div>
		';
	}
}
else
{
	function a_com()
	{
		global $db, $root, $domain, $d, $photo_settings;

		$act = 'insert';
	
		$pub_checked = 'checked';
		$item['title'] = '';
		$item['text'] = '';
		$item['section'] = $d[5];
		$item['link'] = '';	
		$photo_out = '';
		$input_hidden = '';
		$item['tag_title'] = '';
		$item['tag_description'] = '';
		$sef = '';

		include_once($root."/components/photo/admin/item/tmp/edit.php"); // шаблон вывода
	} // конец функции
}



?>