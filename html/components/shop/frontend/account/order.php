<?php
// Аккаунт пользователя
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/frontend/account/account.css');

function component()
{
	global $root, $domain, $frontend_edit;

	$user_id = Auth::check();

	$d = explode('/', $_SERVER['REQUEST_URI']);

	$order_id = Auth::decode($d[4]);
	$order = Orders::getOrder($order_id, $user_id);

	if($order['status'] == 1){$status = 'оплачен';}
	if($order['status'] == 2){$status = 'в обработке';}	

	if($user_id)
	{
		echo '<h1>Заказ</h1>';
		echo '<div style="margin-bottom:20px">от '.$order['date_order'].' &nbsp;&nbsp;&nbsp;Статус: '.$status.'</div>';
		echo '<div>'.$order['orders'] .'</div>';
	}
	else
	{
		echo '<div class="account_form_container">'.Auth::formLogin().'</div>';
	}
} 

?>