<?php
// Скрываем раздел
defined('AUTH') or die('Restricted access');

$item_id = intval($d[5]); // преобразуем в число

// Находим раздел
$stmt_item = $db->prepare('SELECT section FROM com_shop_item WHERE id = :id');
$stmt_item->execute(array('id' => $item_id));
$item = $stmt_item->fetch();


$stmt_update = $db->prepare('UPDATE com_shop_item SET pub = \'0\' WHERE id = :id');
$stmt_update->execute(array('id' => $item_id));

if(isset($d[6]) && $d[6] == 'frontend'){Header ("Location: /shop/section/".$item['section']);}
else{Header ("Location: /admin/com/shop/section/".$item['section']);}		
exit;
?>