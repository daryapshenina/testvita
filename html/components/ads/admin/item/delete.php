<?php
defined('AUTH') or die('Restricted access');

$ads_id = intval($d[5]);

// Обработка изображения
$stmt_select = $db->prepare("SELECT section, user_id FROM com_ads_item WHERE id = :id");
$stmt_select->execute(array('id' => $ads_id));
$item = $stmt_select->fetch();

$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
$path = '/files/ads/'.$floor_id.'/'.$item['user_id'].'/';

if(is_file($root.$path.$ads_id.'.jpg')) unlink($root.$path.$ads_id.'.jpg');
if(is_file($root.$path.$ads_id.'_.jpg')) unlink($root.$path.$ads_id.'_.jpg');

$stmt_delete = $db->prepare("DELETE FROM com_ads_item WHERE id = :id");
$stmt_delete->execute(array('id' => $ads_id));

Header("Location: /admin/com/ads/section/".$item['section']);
exit;

?>