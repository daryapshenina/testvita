<?php
defined('AUTH') or die('Restricted access');

$id_com = intval($d[5]);

$stmt_menu = $db->prepare("SELECT id, menu_type FROM menu WHERE component = 'photo' AND id_com = :id_com AND p1 = 'section' LIMIT 1");
$stmt_menu->execute(array('id_com' => $id_com)); 
$menu = $stmt_menu->fetch();
if (!$menu['id']){die('Нет раздела!');}

$stmt_parent = $db->prepare("SELECT id FROM menu WHERE menu_type = :menu_type AND component = 'photo' AND p1 = 'section' AND parent = :parent");
$stmt_parent->execute(array('menu_type' => $menu['menu_type'], 'parent' => $menu['id'])); 
if($stmt_parent->rowCount() > 0){die('Раздел не может быть удалён, т.к. содержит подразделы!');}

$stmt_item = $db->prepare("SELECT id FROM com_photo_items WHERE section = :section");
$stmt_item->execute(array('section' => $id_com));
if($stmt_item->rowCount() > 0){die('Раздел не может быть удалён, т.к. раздел не пустой! Сначала удалите изображения!');}

// удаляем пункт меню
$stmt_menu_delete = $db->prepare("DELETE FROM menu WHERE id_com = :id_com AND component = 'photo' AND p1 = 'section' LIMIT 1");
$stmt_menu_delete->execute(array('id_com' => $id_com));

// удаляем раздел
$stmt_section_delete = $db->prepare("DELETE FROM com_photo_section WHERE id = :id LIMIT 1");
$stmt_section_delete->execute(array('id' => $id_com));

// удаляем sef
$stmt_sef_delete = $db->prepare("DELETE FROM url WHERE url = :url LIMIT 1");
$stmt_sef_delete->execute(array('url' => "photo/section/$id_com"));

Header("Location: http://".$domain."/admin"); exit;
?>