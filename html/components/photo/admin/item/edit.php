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
		global $db, $root, $domain, $d, $photo_settings, $input_hidden;

		$act = 'update';
		$item_id = $d[5];
		
		$stmt = $db->prepare("SELECT * FROM com_photo_items WHERE id = :id LIMIT 1");
		$stmt->execute(array('id' => $item_id));
		$item = $stmt->fetch();
		
		if($item['pub'] == 1){$pub_checked = 'checked';}else{$pub_checked = '';}
		
		$photo_out = '<img style="margin:5px;" class="photo" src="/files/photo/'.$item['section'].'/'.$item['name'].'.jpg">';
		
		$input_hidden = '<input name="id" type="hidden" value="'.$item_id.'">';

		include_once($root."/components/photo/admin/item/tmp/edit.php"); // шаблон вывода
	} // конец функции
}



?>