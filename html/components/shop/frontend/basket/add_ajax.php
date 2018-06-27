<?php
defined('AUTH') or die('Restricted access');

include_once($root."/lib/currency.php");
include_once $root."/components/shop/classes/Orders.php";
include_once $root.'/modules/cart/frontend/lang/'.LANG.'.php';

$stmt_mod_cart = $db->query("SELECT p1 FROM modules WHERE module = 'cart' LIMIT 1");
$m['p1'] = $stmt_mod_cart->fetchColumn();

$id = $_POST['id'];
$quantity = $_POST['quantity'];
if(isset($_POST['char'])){$chars_arr = $_POST['char'];} else{$chars_arr = '';}

$chars = '';

if(is_array($chars_arr))
{
	foreach ($chars_arr as $key => $value)
	{
		$chars .= $key.': '.$value.'; ';
	}
}

// Добавляем товар к заказу.
Orders::addItem($id, $quantity, $chars);

include $root.'/modules/cart/frontend/'.$m['p1'].'.php';

exit;
?>