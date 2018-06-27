<?php
// DAN 2013
// Выводит заказы интернет - магазина.
defined('AUTH') or die('Restricted access');

$order_id = intval($admin_d4);

function a_com()
{ 
	global $site, $order_id ;

	echo '
	<div class="menu_body">
		<table id="main-top-tab">
			<tr>
				<td class="imshop"><span class="desctitle">Интернет-магазин / заказ: </span></td>
			</tr>
		</table>	
	';		
		
	// Находим количество товаров
	$ordersql = mysql_query("SELECT * FROM `com_shop_orders` WHERE `id` = '$order_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");	

	$resultorder = mysql_num_rows($ordersql);

	if ($resultorder > 0) 
	{	
		while($m = mysql_fetch_array($ordersql)):
			$order_id = $m['id'];
			$order = $m['order'];
			$order_items = $m['items'];			
			$sum = $m['sum'];
			$status = $m['status'];		
			$payment_system = $m['payment_system'];	
			$date_order = $m['date_order'];
			$order_date_payment = $m['date_payment'];
			$fio = $m['fio'];
			$tel = $m['tel'];	
			$email = $m['email'];
			$address = $m['address'];
			$comments = $m['comments'];			
			$payer = $m['payer'];
			
			if($status == 0)
			{
				$status_out = '<input type="checkbox" name="status" value="1"> Если заказ оплачен - поставьте галочку и нажмите &quot;Сохранить&quot;';
				$button_out = '<div>&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"></div>';
				$status_class = "order_status_0";
			}
			elseif($status == 1)
			{
				$status_out = 'Заказ оплачен';
				$status_class = "order_status_1";
			}
			elseif($status == 2)
			{
				$status_out = '<input type="checkbox" name="status" value="1"> Если заказ оплачен - поставьте галочку и нажмите &quot;Сохранить&quot;';
				$button_out = '<div>&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"></div>';
				$status_class = "order_status_2";
			}
			elseif($status == 3)
			{
				$status_out = '<input type="checkbox" name="status" value="1"> Если заказ оплачен - поставьте галочку и нажмите &quot;Сохранить&quot;';
				$button_out = '<div>&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"></div>';
				$status_class = "order_status_3";
			}			
			
			// если не поставлена дата оплаты и заказ не оплачен - выводим форму ввода
			if($order_date_payment == '0000-00-00 00:00:00' && $status != 1)
			{
				$date_payment_out = 'Год:<input class="order_view_input" type="text" name="year" value="'.date(Y).'" size="4"> Месяц:<input class="order_view_input" type="text" name="month" value="'.date(m).'"size="2"> Число:<input class="order_view_input" type="text" name="day" value="'.date(d).'"size="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Часы: <input class="order_view_input" type="text" name="hours"  value="'.date(H).'" size="2">&nbsp;&nbsp; Минуты: <input class="order_view_input" type="text" name="minutes"  value="'.date(i).'" size="2">';
			}
			else
			{
				$date_payment_out = $order_date_payment;
			}

			echo '
				<form method="POST" action="http://'.$site.'/admin/com/shop/orderstatus1/'.$order_id.'">
				<div class="menu_body">
					<table class="w100_bs1">
						<tr>
							<td width="120" class="cell-title"><b>№</b></td>
							<td class="cell-title">'.$order_id.'</td>
						</tr>	
						<tr>	
							<td class="cell-title"><b>Оплата</b></td>
							<td class="cell-title">'.$order.'</td>	
						</tr>
						<tr>	
							<td class="cell-title"><b>Сумма</b></td>
							<td class="cell-title">'.$sum.'</td>	
						</tr>
				';
				
			
				// ------- ПРОВЕРЯЕМ, ЕСТЬ ЛИ ЭЛЕКТРОННЫЕ ТОВАРЫ -------
				$order_items = trim($order_items);
				$items_arr = preg_split('[;]', $order_items);
				
				$items_sum = count($items_arr);				
				
				for ($i=0; $i<$items_sum; $i++)
				{
					// Находим товары
					$item_sql = mysql_query("SELECT * FROM `com_shop_item` WHERE  `id` = '$items_arr[$i]' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");	
					
					while($m = mysql_fetch_array($item_sql)):
						$item_id = $m['id'];
						$item_title = $m['title'];			
						$item_etext_enabled = $m['etext_enabled'];
						$item_etext = $m['etext'];
						
						$order_key = sha1('dan'.$order_id.$order_date_payment);
						
						if($item_etext_enabled == 1) // признак того, что товар электронный
						{
							echo'
								<tr>
									<td class="cell-title bg_green"><b>Электронный товар:</b></td>
									<td class="cell-title bg_green"><div><a target="_blank" href="http://'.$site.'/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'">'.$item_title.'</a></div></td>								
								</tr>
							';
						}
					endwhile;				
				}				
				
				// ------- / проверяем, есть ли электронные товары / -------
				
				
				
			echo'
						<tr>
							<td class="cell-title '.$status_class.'"><b>Статус</b></td>
							<td class="cell-title '.$status_class.'"><div>'.$status_out.'</div></td>								
						</tr>
						<tr>
							<td class="cell-title"><b>Вариант оплаты</b></td>
							<td class="cell-title ">'.$payment_system.'</td>							
						</tr>
						<tr>
							<td class="cell-title"><b>Дата заказа</b></td>
							<td class="cell-title ">'.$date_order.'</td>							
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
							<td class="cell-title ">'.$fio.'</td>							
						</tr>
						<tr>							
							<td class="cell-title"><b>Телефон</b></td>
							<td class="cell-title ">'.$tel.'</td>							
						</tr>
						<tr>							
							<td class="cell-title"><b>Email</b></td>
							<td class="cell-title ">'.$email.'</td>							
						</tr>
						<tr>							
							<td class="cell-title"><b>Адрес</b></td>
							<td class="cell-title ">'.$address.'</td>							
						</tr>
						<tr>							
							<td class="cell-title"><b>Комментарии</b></td>
							<td class="cell-title ">'.$comments.'</td>							
						</tr>						
						<tr>							
							<td colspan="2"  class="bg_gray"><div>&nbsp;</div><div>&nbsp;</div></td>							
						</tr>							
						<tr>						
							<td class="cell-title"><b>Реквизиты заказчика</b></td>
							<td class="cell-title ">'.$payer.'</td>								
						</tr>					
					</table>	
				</div>
				<div>&nbsp;</div>
				'.$button_out.'					
				</form>
				';
		endwhile;
	}
	echo'
		</table>
	</div>
		<br/>
	';



} // конец функции a_com

?>