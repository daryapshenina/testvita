<?php
// DAN 2013
// Выводит заказы интернет - магазина.
defined('AUTH') or die('Restricted access');

$order_id = intval($admin_d4);

mysql_query("DELETE FROM `com_shop_orders` WHERE `id` = '$order_id'");	

Header ("Location: http://".$site."/admin/com/shop/shoporders"); exit;
	
?>