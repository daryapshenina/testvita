<?php
// Публикуем раздел
defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d5); // преобразуем в число

// Находим раздел
$stmt_item = $db->prepare('SELECT section FROM com_shop_item WHERE id = :id');
$stmt_item->execute(array('id' => $item_id));
$item = $stmt_item->fetch();


$stmt_update = $db->prepare('UPDATE com_shop_item SET pub = \'1\' WHERE id = :id');
$stmt_update->execute(array('id' => $item_id));

Header ("Location: /admin/com/shop/section/".$item['section']."/"); exit;		

?>