<?php
defined('AUTH') or die('Restricted access');

$id = intval($d[5]);

$stmt = $db->prepare('DELETE FROM com_shop_price_type WHERE id = :id');
$stmt->execute(array('id' => $id));

$stmt = $db->prepare('DELETE FROM com_shop_price_user WHERE price_type_id = :price_type_id');
$stmt->execute(array('price_type_id' => $id));

$stmt = $db->prepare('DELETE FROM com_shop_price_item WHERE price_type_id = :price_type_id');
$stmt->execute(array('price_type_id' => $id));
	
Header ("Location: /admin/com/shop/price_type");
exit;

?>