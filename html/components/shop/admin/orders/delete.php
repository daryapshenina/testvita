<?php
// Выводит заказы интернет - магазина.
defined('AUTH') or die('Restricted access');

$order_id = intval($admin_d5);

$stmt_orders = $db->prepare("DELETE FROM com_shop_orders WHERE id = :order_id LIMIT 1");
$stmt_orders->execute(array('order_id' => $order_id));

$stmt_items = $db->prepare("DELETE FROM com_shop_orders_items WHERE order_id = :order_id");
$stmt_items->execute(array('order_id' => $order_id));
	

Header ("Location: /admin/com/shop/orders"); exit;
	
?>