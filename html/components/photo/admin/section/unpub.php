<?php
// Скрываем раздел
defined('AUTH') or die('Restricted access');

$item_id = intval($d[5]); 

$stmt_menu = $db->prepare("UPDATE menu SET pub = '0' WHERE id_com = :id_com AND p1 = 'section'");
$stmt_menu->execute(array('id_com' => $item_id));


$stmt_section = $db->prepare("UPDATE com_photo_section SET pub = '0' WHERE id = :id");
$stmt_section->execute(array('id' => $item_id));

Header ("Location: http://".$domain."/admin"); exit;		

?>