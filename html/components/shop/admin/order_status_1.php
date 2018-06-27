<?php
// DAN 2013
// Выводит заказы интернет - магазина.
defined('AUTH') or die('Restricted access');

$order_id = intval($admin_d4);
$status = intval($_POST["status"]);
$year = intval($_POST["year"]);
$month = intval($_POST["month"]);
$day = intval($_POST["day"]);
$hours = intval($_POST["hours"]);
$minutes = intval($_POST["minutes"]);
$save = $_POST["save"]; // кнопка 'Сохранить'
$none = $_POST["none"]; // кнопка 'Отменить'



// --- Условия ---
// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/shop/shoporders"); exit;} 

// Если кнопка сохранить = получено методом POST иначе GET из контекстного меню
if ($save == "Сохранить")
{
	// проверка времени
	if($year < 2013 || $year > date(Y) || $month < 1 || $month > 12 || $day < 1 || $day > 31 || $hours < 0 || $hours > 24 || $minutes < 0 || $minutes > 60)
	{
		die ("Неверно указано дата или время оплаты заказа <br> $year-$month-$day $hours:$minutes:00");
	}

	if($status == 1)
	{
		$date = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':00';
		// Обновляем данные в таблице "com_shop_orders"
		$query_update_shop = "UPDATE `com_shop_orders` SET  `status` =  '1', `date_payment` = '$date' WHERE  `id` = '$order_id' LIMIT 1 ;";
		// echo $query_update_shop;
		$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 1");

		order_send_email();		
	}
}
else 
{
	// Обновляем данные в таблице "com_shop_orders"
	$query_update_shop = "UPDATE `com_shop_orders` SET  `status` =  '1', `date_payment` = NOW() WHERE  `id` = '$order_id' AND `status` != '1' LIMIT 1 ;";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 2");
	
	order_send_email();
}

// ======== ОТПРАВЛЯЕМ ПИСЬМО НА EMAIL =================================================================================
function order_send_email()
{ 
	global $site, $order_id;
	
	// Находим заказ
	$orderssql = mysql_query("SELECT * FROM `com_shop_orders` WHERE  `id` = '$order_id' LIMIT 1 ") or die ("Невозможно сделать выборку из таблицы - 1");	

	$resultorders = mysql_num_rows($orderssql);	
	
	while($m = mysql_fetch_array($orderssql)):
		$order_id = $m['id'];
		$order = $m['order'];
		$order_items = $m['items'];
		$order_sum = $m['sum'];
		$order_status = $m['status'];		
		$order_payment_system = $m['payment_system'];	
		$order_date_order = $m['date_order'];
		$order_date_payment = $m['date_payment'];
		$order_fio = $m['fio'];
		$order_email = $m['email'];		
	endwhile;	
	

	
	// ------- РАЗБИРАЕМ ЗАКАЗ НА ТОВАРЫ -------
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
				$email_tmp_items .= '<hr/>';
				$email_tmp_items .= '<div>&nbsp;</div>';
				$email_tmp_items .= '<div><b>'.$item_title.'</b></div>';					
				$email_tmp_items .= '<div>Ссылка на страницу получения товара: <a href="http://'.$site.'/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'">http://'.$site.'/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'</a></div>';
				$email_tmp_items .= '<div>&nbsp;</div>';
			}
		endwhile;				
	}
	// -----------------------------------------

	
	
	// шаблон для отправки на email
	$email_tmp = '
	<div><b>ВАШЕМУ ЗАКАЗУ ПРИСВОЕН СТАТУС &quot;ОПЛАЧЕН&quot;</b></div>
	<div>&nbsp;</div>
	<div><b>Ваш заказ:</b></div>
	<div>&nbsp;</div>
	<div>'.$order.'</div>
	<div>&nbsp;</div>	
	<div>Вариант оплаты: '.$order_payment_system.'</div>
	<div>&nbsp;</div>	
	'.$email_tmp_items;
	
	
// === Отправка на почту ==================================================
	// вывод настроек	
	$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
		
	while($m = mysql_fetch_array($num)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parameter = $m['parametr'];
		
		// почта
		if ($setting_name == "email")
		{
			$shop_email = $setting_parameter;
		} 	
	endwhile;


	// SUBJECT тема
	$subject = "Сообщение с сайта www.".$site." ";
	
	$site_code = '=?UTF-8?B?'.base64_encode($site).'?=';
	
	/* Для отправки HTML-почты Content-type. */
	$headers  = "MIME-Version: 1.0 \r\n";
	$headers .= "Content-type: text/html; charset=UTF-8 \r\n";
	$headers .= "From: www.".$site_code." <".$shop_email."> \r\n"; 
	
	/* сообщение */
	$message = $email_tmp;
	
	// = MAIL = 
	mail($order_email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);	
	
	// делаем паузу 1 секунду и отправляем ещё одно письмо
	sleep (1);
	mail($shop_email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);	
}


Header ("Location: http://".$site."/admin/com/shop/shoporders"); exit;
	
?>