<?php
// DAN 2013
// Выводит заказы интернет - магазина.
defined('AUTH') or die('Restricted access');

$page_nav = intval($admin_d4);
$quantity = 100; // количество заказов на странице

function a_com()
{ 
	global $site, $page_nav, $quantity;

	// Контекстное меню
	echo " 
	<script type=\"text/javascript\">
	$(document).ready(function() {
		$('a.sitemenuitem').contextMenu('myMenu2', {
			bindings: {
			  'orderview': function(t) {
			  top.location.href='http://$site/admin/com/shop/orderview/'+t.name; 
			  },			
			  'orderstatus1': function(t) {
			  top.location.href='http://$site/admin/com/shop/orderstatus1/'+t.name; 
			  },
			  'orderdelete': function(t) {
			  top.location.href='http://$site/admin/com/shop/orderdelete/'+t.name;  
			  } 
			}
		}); 
	});
	</script>
	";

	echo '
	<div class="menu_body">
		<table id="main-top-tab">
			<tr>
				<td class="imshop"><span class="desctitle">Интернет-магазин / заказы: </span></td>
			</tr>
		</table>	
	';	
	
	// Навигация
	$pq = ($page_nav-1)*$quantity;
	if ($pq < 0){$pq = 0;}		
		
	// Находим количество заказов
	$orderssql = mysql_query("SELECT * FROM `com_shop_orders` ORDER BY `id` DESC LIMIT $pq,$quantity") or die ("Невозможно сделать выборку из таблицы - 1");	

	$resultorders = mysql_num_rows($orderssql);

	// выводит заголовок	
	echo'
		<table class="order_tab">
			<tr class="menuheader">
				<td width="50" class="cell-title">№</td>
				<td class="cell-title">Оплата</td>
				<td width="50" class="cell-title">Сумма</td>			
				<td width="100" class="cell-title">Статус</td>
				<td width="100" class="cell-title">Вариант оплаты</td>
				<td width="120" class="cell-title">Дата заказа</td>
				<td width="120" class="cell-title">Дата оплаты</td>
				<td class="cell-title">Плательщик</td>			
			</tr>
	';	

	if ($resultorders> 0) 
	{	
		while($m = mysql_fetch_array($orderssql)):
			$id = $m['id'];
			$order = $m['order'];
			$sum = $m['sum'];
			$status = $m['status'];		
			$payment_system = $m['payment_system'];	
			$date_order = $m['date_order'];
			$date_payment = $m['date_payment'];
			$fio = $m['fio'];
			$payer = $m['payer'];
			
			if($status == 1)
			{
				$status_out = 'Оплачен';
				$status_class = "order_status_1";
			}
			elseif($status == 2)
			{
				$status_out = 'Ошибка оплаты';
				$status_class = "order_status_2";
			}
			elseif($status == 3)
			{
				$status_out = 'Проверьте сумму';
				$status_class = "order_status_3";
			}
			else
			{
				$status_out = 'Ждёт оплаты';
				$status_class = "order_status_0";
			}				
			
			// если не поставлена дата оплаты - выводим форму ввода
			if($date_payment == '0000-00-00 00:00:00')
			{
				$date_payment_out = '';
			}
			else
			{
				$date_payment_out = $date_payment;
			}			
			
			// выбираем только первые 2 слова (фамилию, имя)
			$payer = strip_tags($payer);
			$payer_arr = preg_split('[ ]', $payer);
			$payer_out = $payer_arr[1].' '.$payer_arr[2];
			
			echo '
			<tr class="'.$status_class.'">
				<td width="50" class="cell-title"><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$id.'</a></td>
				<td class="cell-title"><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$order.'</a></td>
				<td width="50" class="cell-title"><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$sum.'</a></td>			
				<td width="100" class="cell-title"><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$status_out.'</a></td>
				<td width="100" class="cell-title "><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$payment_system.'</a></td>
				<td width="120" class="cell-title "><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$date_order.'</a></td>
				<td width="120" class="cell-title "><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$date_payment_out.'</a></td>
				<td class="cell-title "><a class="sitemenuitem" name="'.$id.'" href="http://'.$site.'/admin/com/shop/orderview/'.$id.'" >'.$fio.'</a></td>			
			</tr>';
		endwhile;
	}
	echo'
		</table>
	</div>
	';
	
	// Контекстное меню

	echo "
	  <div class=\"contextMenu\" id=\"myMenu2\">
		<ul>
		  <li id=\"orderview\"><img src=\"http://".$site."/administrator/tmp/images/edit.png\" /> Просмотр заказа</li>	 
		  <li id=\"orderstatus1\"><img src=\"http://".$site."/administrator/tmp/images/unblock.png\" /> Статус \"Оплачен\"</li>
		  <li id=\"orderdelete\"><img src=\"http://".$site."/administrator/tmp/images/delete.png\" /> Удалить</li>
		</ul>
	  </div>  
	";
	
	// Находим общее количество заказов
	$ordersallsql = mysql_query("SELECT * FROM `com_shop_orders`") or die ("Невозможно сделать выборку из таблицы - 1");	

	$resultordersall = mysql_num_rows($ordersallsql);	
	
	// количество страниц навигации = количество товаров / товаров на страницу - округляем в большую сторону
	$kol_page_nav = ceil($resultordersall/$quantity);
	
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
				echo '<div class="navpage"><a href="http://'.$site.'/admin/com/shop/shoporders/'.$i.'">'.$i.'</a></div>';
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