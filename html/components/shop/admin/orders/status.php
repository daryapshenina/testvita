<?php
defined('AUTH') or die('Restricted access');

include_once $root."/components/shop/classes/Orders.php";
$order_id = $SITE->d[5];

$checkout = Orders::checkout($order_id);

// Если кнопка сохранить = получено методом POST иначе GET из контекстного меню
if ($_SERVER["REQUEST_METHOD"] == 'POST')
{
	$status = intval($_POST["status"]);
	$year = intval($_POST["year"]);
	$month = intval($_POST["month"]);
	$day = intval($_POST["day"]);
	$hours = intval($_POST["hours"]);
	$minutes = intval($_POST["minutes"]);
	$save = $_POST["save"]; // кнопка 'Сохранить'
	$none = $_POST["none"]; // кнопка 'Отменить'

	if ($none == "Отменить" || $none == "Выход"){Header ("Location: /admin/com/shop/orders"); exit;}

	// проверка времени
	if($year < 2013 || $year > date(Y) || $month < 1 || $month > 12 || $day < 1 || $day > 31 || $hours < 0 || $hours > 24 || $minutes < 0 || $minutes > 60)
	{
		die ("Неверно указано дата или время оплаты заказа <br> $year-$month-$day $hours:$minutes:00");
	}

	if($status == 1)
	{
		$date = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':00';
		// Обновляем данные в таблице "com_shop_orders"
		$stmt_update = $db->prepate("UPDATE com_shop_orders SET status = '1', date_payment = :date_payment WHERE id = :order_id LIMIT 1");
		$stmt_update->execute(array('date_payment' => $date, 'order_id' => $order_id));

		order_send_email($order_id);
	}
}
else // Метод GET
{
	// Обновляем данные в таблице "com_shop_orders"
	$stmt_update = $db->prepare("UPDATE com_shop_orders SET status = '1', date_payment = NOW() WHERE id = :order_id AND status != '1' LIMIT 1");
	$stmt_update->execute(array('order_id' => $order_id));

	order_send_email($order_id);
}



// ======== ОТПРАВЛЯЕМ ПИСЬМО НА EMAIL =================================================================================
function order_send_email($order_id)
{
	global $db, $SITE;

	// Находим заказ
	$stmt_orders = $db->prepare("SELECT * FROM com_shop_orders WHERE  id = :order_id LIMIT 1");
	$stmt_orders->execute(array('order_id' => $order_id));

	while($m = $stmt_orders->fetch())
	{
		$order_id = $m['id'];
		$order = $m['orders'];
		$order_items = $m['items'];
		$order_sum = $m['sum'];
		$order_status = $m['status'];
		$order_payment_system = $m['payment_system'];
		$order_date_order = $m['date_order'];
		$order_date_payment = $m['date_payment'];
		$order_fio = $m['fio'];
		$order_email = $m['email'];
	}


	// ------- РАЗБИРАЕМ ЗАКАЗ НА ТОВАРЫ -------
	$order_items = trim($order_items);
	$items_arr = preg_split('[;]', $order_items);

	$items_sum = count($items_arr);

	for ($i=0; $i<$items_sum; $i++)
	{
		// Находим товары
		$stmt_items = $db->prepare("SELECT * FROM com_shop_item WHERE  id = :id LIMIT 1");
		$stmt_items->execute(array('id' => $items_arr[$i]));

		while($m = $stmt_items->fetch())
		{
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
				$email_tmp_items .= '<div>Ссылка на страницу получения товара: <a href="/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'">/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'</a></div>';
				$email_tmp_items .= '<div>&nbsp;</div>';
			}			
		}
	}
	// -----------------------------------------



	// шаблон для отправки на email
	$email_content = '
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

	// SUBJECT тема
	$from = 'no-replay@'.$SITE->domainIdn;
	$subject = "Сообщение с сайта www.".$SITE->domainIdn." ";
	
	classMail::send($order_email, $from, $subject, $email_content, null);
}


Header ("Location: /admin/com/shop/orders"); exit;

?>