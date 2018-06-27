<?php
// Аккаунт пользователя
defined('AUTH') or die('Restricted access');
include_once($root."/components/shop/classes/Orders.php");
include_once($root."/classes/Auth.php");

switch ($d[2]) {
	case "order": include_once($root.'/components/shop/frontend/account/order.php');break;			
	default: include_once($root.'/components/shop/frontend/account/account.php');
}
?>