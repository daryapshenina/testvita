<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/orders/orders.css');

$order_id = intval($admin_d5);

function a_com()
{ 
	global $db, $domain, $shopSettings, $order_id;	

	$stmt_order = $db->prepare("SELECT * FROM com_shop_orders WHERE id = :id LIMIT 1");	
	$stmt_order->execute(array('id' => $order_id));

	if ($stmt_order->rowCount() > 0) 
	{	
		while($order = $stmt_order->fetch()):
			
			if($order['status'] == 0)
			{
				$status_out = 'Брошенная корзина';
				$button_out = '<div>&nbsp;&nbsp;<input class="redbutton" type="submit" value="Выход" name="none"></div>';
				$status_class = "order_status_0";
				
				$order_id = $order['id'];
				
				$stmt_order_items = $db->prepare("
				SELECT o.id, o.item_id, o.quantity, o.chars, i.title, i.price 
				FROM com_shop_orders_items o
				JOIN com_shop_item i ON i.id = o.item_id
				WHERE order_id = :order_id
				");
				
				$stmt_order_items->execute(array('order_id' => $order_id));
				
				$items_out = '';
				$summa = 0;
				while($n = $stmt_order_items->fetch())
				{
					if($n['price'] < 0 || $n['price'] > 999999999) $n['price'] = 0;
					if(intval($n['quantity']) == $n['quantity']){$n['quantity'] = intval($n['quantity']);}
					$sum = $n['quantity'] * $n['price'];
					$summa += $sum;
					$sum_format = number_format($sum, 0, '', ' ');
					$items_out .= '<a target="_blank" href="/shop/item/'.$n['item_id'].'">'.$n['title'].' '.$n['chars'].' '.$n['quantity'].' х '.$n['price'].' = '.$sum_format.' '.$shopSettings->currency.'</a><br>';
				}				
				
				$summa_format  = number_format($summa, 0, '', ' ');
				$order['sum'] = $summa_format;
				$order['orders'] = '<span>'.$items_out.'<b> Сумма: '.$summa_format.' '.$shopSettings->currency.'</b></span>';
			}
			elseif($order['status'] == 1)
			{
				$status_out = 'Заказ оплачен';
				$button_out = '';
				$status_class = "order_status_1";
			}
			elseif($order['status'] == 2)
			{
				$status_out = '<input type="checkbox" name="status" value="1"> Если заказ оплачен - поставьте галочку и нажмите &quot;Сохранить&quot;';
				$button_out = '<div>&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"></div>';
				$status_class = "order_status_2";
			}
			elseif($order['status'] == 3)
			{
				$status_out = '<input type="checkbox" name="status" value="1"> Если заказ оплачен - поставьте галочку и нажмите &quot;Сохранить&quot;';
				$button_out = '<div>&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"></div>';
				$status_class = "order_status_3";
			}		
			
			// если не поставлена дата оплаты и заказ не оплачен - выводим форму ввода
			if($order['date_payment'] == '0000-00-00 00:00:00' && $order['status'] != 1)
			{
				$date_payment_out = 'Год:<input class="order_view_input" type="text" name="year" value="'.date("Y").'" size="4"> Месяц:<input class="order_view_input" type="text" name="month" value="'.date("m").'"size="2"> Число:<input class="order_view_input" type="text" name="day" value="'.date("d").'"size="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Часы: <input class="order_view_input" type="text" name="hours"  value="'.date("H").'" size="2">&nbsp;&nbsp; Минуты: <input class="order_view_input" type="text" name="minutes"  value="'.date("i").'" size="2">';
			}
			else
			{
				$date_payment_out = $order['date_payment'];
			}

			echo '
				<h1>Интернет-магазин / заказ:</h1>			
				<form method="POST" action="/admin/com/shop/orders/status/'.$order['id'].'">
				<table class="admin_table_2">
					<tr>
						<td width="120" class="cell-title"><b>№</b></td>
						<td class="cell-title">'.$order['id'].'</td>
					</tr>	
					<tr>	
						<td class="cell-title"><b>Заказ</b></td>
						<td class="cell-title">'.$order['orders'].'</td>	
					</tr>
					<tr>	
						<td class="cell-title"><b>Сумма</b></td>
						<td class="cell-title">'.$order['sum'].' '.$shopSettings->currency.'</td>	
					</tr>
					<tr>
						<td class="cell-title '.$status_class.'"><b>Статус</b></td>
						<td class="cell-title '.$status_class.'"><div>'.$status_out.'</div></td>								
					</tr>
					<tr>
						<td class="cell-title"><b>Вариант оплаты</b></td>
						<td class="cell-title ">'.$order['payment_system'].'</td>							
					</tr>
					<tr>
						<td class="cell-title"><b>Дата заказа</b></td>
						<td class="cell-title ">'.$order['date_order'].'</td>							
					</tr>	
					<tr>							
						<td class="cell-title"><b>Дата оплаты</b></td>
						<td class="cell-title ">'.$date_payment_out.'</td>							
					</tr>
					<tr>							
						<td colspan="2" class="bg_gray"><div>&nbsp;</div><div>&nbsp;</div></td>							
					</tr>
					<tr>							
						<td class="cell-title"><b>ФИО</b></td>
						<td class="cell-title ">'.$order['fio'].'</td>							
					</tr>
					<tr>							
						<td class="cell-title"><b>Телефон</b></td>
						<td class="cell-title ">'.$order['tel'].'</td>							
					</tr>
					<tr>							
						<td class="cell-title"><b>Email</b></td>
						<td class="cell-title ">'.$order['email'].'</td>							
					</tr>
					<tr>							
						<td class="cell-title"><b>Адрес</b></td>
						<td class="cell-title ">'.$order['address'].'</td>							
					</tr>
					<tr>							
						<td class="cell-title"><b>Комментарии</b></td>
						<td class="cell-title ">'.$order['comments'].'</td>							
					</tr>						
					<tr>							
						<td colspan="2"  class="bg_gray"><div>&nbsp;</div><div>&nbsp;</div></td>							
					</tr>							
					<tr>						
						<td class="cell-title"><b>Реквизиты заказчика</b></td>
						<td class="cell-title ">'.$order['payer'].'</td>								
					</tr>					
				</table>
				<br>				
				'.$button_out.'					
				</form>
				';
		endwhile;
	}
	echo'
		</table>
	';



} // конец функции a_com

?>