<?php
defined('AUTH') or die('Restricted access');

$item_id = intval($d[5]); // преобразуем в число

// Находим раздел
$stmt_item = $db->prepare('SELECT section FROM com_ads_item WHERE id = :id');
$stmt_item->execute(array('id' => $item_id));
$item = $stmt_item->fetch();


$stmt_update = $db->prepare('UPDATE com_ads_item SET pub = 1 WHERE id = :id');
$stmt_update->execute(array('id' => $item_id));

if(isset($d[6]) && $d[6] == 'frontend') Header ("Location: /ads/section/".$item['section']);
else{Header ("Location: /admin/com/ads/section/".$item['section']);}		
exit;		

?>