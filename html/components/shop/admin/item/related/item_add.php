<?php
// получаем список товаров на ajax
defined('AUTH') or die('Restricted access');

$item_id = intval($d[5]);
$related_id = intval($d[6]);

$stmt = $db->prepare("SELECT id, title, price, pub, photo FROM com_shop_item WHERE id = :id LIMIT 1");
$stmt->execute(array('id' => $related_id));
$item = $stmt->fetch();

$id = $related_id; // По умолчанию, для режима работы add / insert

// Для режима работы edit / update
if($item_id != 0)
{
	// Находим максимальное значение orders
	$stmt_orders = $db->prepare("SELECT MAX(ordering) FROM com_shop_related_item WHERE item_id = :item_id");
	$stmt_orders->execute(array('item_id' => $item_id));
	$max_ordering = $stmt_orders->fetchColumn();
	$max_ordering++;
	
	$stmt_insert = $db->prepare("INSERT INTO com_shop_related_item SET item_id = :item_id, related_id = :related_id, ordering = :ordering");
	$stmt_insert->execute(array('item_id' => $item_id, 'related_id' => $related_id, 'ordering' => $max_ordering));
	
	$id = $db->lastInsertId();
}

$arr['id'] = $id;
$arr['item_id'] = $item['id'];
$arr['title'] = $item['title'];
$arr['price'] = $item['price'];
$arr['pub'] = $item['pub'];
$arr['photo'] = $item['photo'];

echo json_encode($arr);

exit;
?>