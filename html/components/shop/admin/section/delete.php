<?php
// Удаление раздела

defined('AUTH') or die('Restricted access');

$id_com = intval($admin_d5);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------
// сбрасываем значения
$menu_top_selected = "";
$menu_left_selected = "";

// находим id_menu по id_com
$stmt_menu = $db->prepare("SELECT id, menu_type FROM menu WHERE component = 'shop' AND id_com = :id_com AND p1 <> 'all' LIMIT 1");
$stmt_menu->execute(array('id_com' => $id_com));
$menu = $stmt_menu->fetch();

$err = '';
if (!isset($menu['id']) || !isset($menu['menu_type'])){$err .= 'Нет раздела! <br>';}

// проверяем - есть ли подразделы внутри раздела
$stmt_submenu = $db->query("SELECT id FROM menu WHERE parent = '".$menu['id']."' LIMIT 1");
if($stmt_submenu->rowCount() > 0){$err .= 'Есть вложенные разделы <br>';}

// проверяем - есть ли товары внутри раздела
$stmt_item = $db->query("SELECT id FROM com_shop_item WHERE section = '".$id_com."' LIMIT 1");
if($stmt_item->rowCount() > 0){$err .= 'Есть товары <br>';}



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
	$stmt_delete = $db->prepare("DELETE FROM menu WHERE id_com = :id_com AND component = 'shop' AND main <> '1' ");
	$stmt_delete->execute(array('id_com' => $id_com));

	// удаляем раздел
	$stmt_delete = $db->prepare("DELETE FROM com_shop_section WHERE id = :id_com");
	$stmt_delete->execute(array('id_com' => $id_com));	

	// удаляем sef
	$stmt_delete = $db->prepare("DELETE FROM url WHERE url = :url");
	$stmt_delete->execute(array('url' => 'shop/section/'.$id_com));	

	// удаляем фильтр
	$stmt_delete = $db->prepare("DELETE FROM com_shop_filter WHERE section_id = :id_com");
	$stmt_delete->execute(array('id_com' => $id_com));		
	
	Header("Location: /admin"); exit;
}

?>