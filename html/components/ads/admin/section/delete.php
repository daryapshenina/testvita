<?php
defined('AUTH') or die('Restricted access');

$id = $d[5];

// находим id_menu по id
$stmt_menu = $db->prepare("SELECT id FROM menu WHERE component = 'ads' AND id_com = :id_com AND p1 = 'section' LIMIT 1");
$stmt_menu->execute(array('id_com' => $id));
$menu_id = $stmt_menu->fetchColumn();

$err = '';
if (!isset($menu_id)){$err .= 'Нет раздела! <br>';}

// проверяем - есть ли подразделы внутри раздела
$stmt_submenu = $db->prepare("SELECT id FROM menu WHERE parent = :menu_id LIMIT 1");
$stmt_submenu->execute(array('menu_id' => $menu_id));
if($stmt_submenu->rowCount() > 0){$err .= 'Есть вложенные разделы <br>';}

// проверяем - есть ли объявления внутри раздела
// $stmt_item = $db->query("SELECT id FROM com_ads_item WHERE section = :section LIMIT 1");
// $stmt_item->execute(array('section' => $id));
// if($stmt_item->rowCount() > 0){$err .= 'Раздел нельзя удалить - есть объявления <br>';}

if(is_file($root."/files/ads/sections/".$id.".jpg")) unlink($root."/files/ads/sections/".$id.".jpg");

if ($err != '')
{
	function a_com()
	{	
		echo '
			<div id="main-top">РАЗДЕЛ НЕ ПУСТОЙ!</div>
			<div style="padding: 10px">
				<div>'.$err.'</div>
			</div>
		';
	}
}
else {
	// удаляем пункт меню	
	$stmt_delete = $db->prepare("DELETE FROM menu WHERE id_com = :id_com AND component = 'ads' AND p1 = 'section' ");
	$stmt_delete->execute(array('id_com' => $id));

	// удаляем раздел
	$stmt_delete = $db->prepare("DELETE FROM com_ads_section WHERE id = :id");
	$stmt_delete->execute(array('id' => $id));	

	// удаляем sef
	$stmt_delete = $db->prepare("DELETE FROM url WHERE url = :url");
	$stmt_delete->execute(array('url' => 'ads/section/'.$id));	

	// удаляем фильтр
	// $stmt_delete = $db->prepare("DELETE FROM com_ads_filter WHERE section_id = :section_id");
	// $stmt_delete->execute(array('section_id' => $id));		
	
	Header("Location: /admin"); exit;
}

?>