<?php
defined('AUTH') or die('Restricted access');

$head->addCode('
<script type="text/javascript">

	DAN_ready(function()
	{
		class_name = "contextmenu_shop_orders";
		var contextmenu_shop_orders = [
			["admin/com/shop/orders/view", "contextmenu_edit", "Просмотр заказа"],			
			["admin/com/shop/orders/status", "contextmenu_unblock", "Статус оплачен"],
			["admin/com/shop/orders/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_shop_orders);
	});

</script>
');


$head->addFile('/components/shop/admin/orders/orders.css');


$page_nav = intval($d[4]);
$quantity = 100; // количество заказов на странице

function a_com()
{ 
	global $db, $domain, $shopSettings, $page_nav, $quantity;

	// Навигация
	$pq = ($page_nav-1)*$quantity;
	if ($pq < 0){$pq = 0;}		
		
	// Находим количество заказов
	$stmt_quantity = $db->query("SELECT * FROM com_shop_orders ORDER BY id DESC LIMIT ".$pq.",".$quantity);
	$orders_quantity = $stmt_quantity->rowCount();
	

	// выводит заголовок	
	echo '<h1>Интернет-магазин / заказы:</h1>
		<table class="admin_table">
			<tr>
				<th style="width:50px;">№</td>
				<th>Заказ</td>
				<th style="width:120px;">Сумма</td>			
				<th style="width:120px;">Статус</td>
				<th style="width:150px;">Вариант оплаты</td>
				<th style="width:120px;">Дата заказа</td>
				<th style="width:120px;">Дата оплаты</td>
				<th>Плательщик</td>			
			</tr>
	';


	if ($orders_quantity > 0) 
	{
		while($m = $stmt_quantity->fetch())
		{
			if($m['status'] == 0)
			{
				$status_out = 'Брошенная корзина';
				$status_class = "order_status_0";
			}			
			elseif($m['status'] == 1)
			{
				$status_out = 'Оплачен';
				$status_class = "order_status_1";
			}
			elseif($m['status'] == 2)
			{
				$status_out = 'Заказ не оплачен';
				$status_class = "order_status_2";
			}
			elseif($m['status'] == 3)
			{
				$status_out = 'Проверьте сумму';
				$status_class = "order_status_3";
			}
			else
			{
				$status_out = 'Неопределённый';
				$status_class = "order_status_0";
			}

			// если не поставлена дата оплаты - выводим форму ввода
			if($m['date_payment'] == '0000-00-00 00:00:00')
			{
				$date_payment_out = '';
			}
			else
			{
				$date_payment_out = $m['date_payment'];
			}			
			
			// выбираем только первые 2 слова (фамилию, имя)
			if(isset($m['payer']) && $m['payer'] != '')
			{
				$m['payer'] = strip_tags($m['payer']);
				$payer_arr = preg_split('[ ]', $m['payer']);
				$payer_out = @$payer_arr[1].' '.@$payer_arr[2];			
			}
			else {$payer_out = '';}
		
			if($m['status'] != 0){$order = strip_tags($m['orders'], '<span><b><br>');}
			else
			{
				$order_id = $m['id'];
				
				$stmt_order_items = $db->prepare("
				SELECT o.id, o.item_id, o.quantity, o.chars, i.title, i.price 
				FROM com_shop_orders_items o
				JOIN com_shop_item i ON i.id = o.item_id
				WHERE order_id = :order_id
				");
				
				$stmt_order_items->execute(array('order_id' => $order_id));
				
				$order = '';
				$summa = 0;
				while($n = $stmt_order_items->fetch())
				{				
					if($n['price'] < 0 || $n['price'] > 999999999) $n['price'] = 0;
					if(intval($n['quantity']) == $n['quantity']){$n['quantity'] = intval($n['quantity']);}
					$sum = $n['quantity'] * $n['price'];
					$summa += $sum;
					$sum_format = number_format($sum, 0, '', ' ');
					$order .= $n['title'].' '.$n['chars'].' '.$n['quantity'].' х '.$n['price'].' = '.$sum_format.' '.$shopSettings->currency.'<br>';
				}				
				
				$summa_format  = number_format($summa, 0, '', ' ');
				$m['sum'] = $summa_format;
				$order = '<span>'.$order.'<b> Сумма: '.$summa_format.' '.$shopSettings->currency.'</b></span>';
			}
			


			echo '
			<tr data-id="'.$m['id'].'" class="contextmenu_shop_orders '.$status_class.'">
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$m['id'].'</a></td>
				<td><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$order.'</a></td>
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$m['sum'].'</a></td>			
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$status_out.'</a></td>
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$m['payment_system'].'</a></td>
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$m['date_order'].'</a></td>
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$date_payment_out.'</a></td>
				<td class="center"><a href="/admin/com/shop/orders/view/'.$m['id'].'" >'.$m['fio'].'</a></td>			
			</tr>';	
		}
	}
	
	echo'</table>';
	
	
	// Находим количество заказов
	$stmt_ordets_all = $db->query("SELECT id FROM com_shop_orders");
	$stmt_ordets_all->rowCount();	
	

	// количество страниц навигации = количество товаров / товаров на страницу - округляем в большую сторону
	$kol_page_nav = ceil($stmt_ordets_all->rowCount()/$quantity);
	
	if ($kol_page_nav > 1) // если колитчество страниц > 1 - выводим навигацию
	{		
		echo '<br/>
		<div align="center">
		<table border="0" cellpadding="0" style="border-collapse: collapse">
			<tr>
				<td>
				<div class="navbg"><div class="navpage-str">Страницы:</div>
		';	
		
		if ($page_nav < 1){$page_nav = 1;}
		
		for ($i = 1; $i <= $kol_page_nav; $i++) 
		{
			if ($i == $page_nav)
			{
				echo '<div class="navpage-active">'.$i.'</div>';
			}
			else 
			{
				echo '<div class="navpage"><a href="/admin/com/shop/orders/'.$i.'">'.$i.'</a></div>';
			}
		}
		
			echo '</div>
				  </td>
			</tr>
		</table>
		</div>';
		
	}		


} // конец функции a_com

?>