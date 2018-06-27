<?php
// Аккаунт пользователя
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/frontend/account/account.css');

function component()
{
	global $domain, $root, $db, $item, $frontend_edit;

	$user_id = Auth::check();
	
	if($user_id)
	{
		$orders = Orders::getOrders($user_id);

		$out_tr = '';

		foreach($orders as $order_arr)
		{
			$items = Orders::getItems($order_arr['id']);
			
			$summa = 0;		
			$quantity = 0;		
			$status = '';
			
			// Считаем сумму заказа и количество товаров
			foreach($items as $key => $item)
			{
				if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
			//	if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}
				$sum = $item['price'] * $item['quantity'];
				if($item['price'] == 0){$price = '';}
				else {$price = number_format($item['price'], 0, '', ' ');}
				
				$quantity += $item['quantity'];
				$summa += $sum;
			}			
		
			if($order_arr['status'] == 0)
			{
				$status = 'В процессе заказа';
				$url = '/shop/basket';
			}

			$order_id_encode = Auth::encode($order_arr['id']);	
			
			if($order_arr['status'] == 1)
			{
				$status = 'Оплачен';
				$summa = $order_arr['sum'];
				$url = '/shop/account/order/'.$order_id_encode;
			}

			if($order_arr['status'] == 2)
			{
				$status = 'В обработке';
				$summa = $order_arr['sum'];
				$url = '/shop/account/order/'.$order_id_encode;				
			}

			$summa_out  = number_format($summa, 0, '', ' ');
			
			$out_tr .= '<tr><td>'.$order_arr['date_order'].'</td><td>'.$quantity.'</td><td>'.$summa_out.'</td><td><a href="'.$url.'">'.$status.'</a></td></tr>';
		}

		echo '<h1>Заказы</h1>';
		echo '<table class="account_orders_tab">';
		echo '<tr>';
		echo '<th>Дата заказа</th><th>Кол-во товаров</th><th>Сумма заказа</th><th>Статус</th>';
		echo '</tr>';
		echo $out_tr;
		echo '</table>';	
	}
	else
	{
		echo '<div class="account_form_container">'.Auth::formLogin().'</div>';
	}
} 

?>