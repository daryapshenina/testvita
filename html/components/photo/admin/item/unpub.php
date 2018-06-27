<?php
// Скрываем раздел
defined('AUTH') or die('Restricted access');

$item_id = intval($d[5]); 

// Находим раздел
$stmt_item = $db->prepare('SELECT section FROM com_photo_items WHERE id = :id LIMIT 1');
$stmt_item->execute(array('id' => $item_id));
$item = $stmt_item->fetch();


$stmt_update = $db->prepare('UPDATE com_photo_items SET pub = \'0\' WHERE id = :id');
$stmt_update->execute(array('id' => $item_id));

Header ("Location: /admin/com/photo/section/".$item['section']."/"); exit;		

?>