<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/shop/frontend/basket/yandex/tmp/style.css');

$utm_arr = $utm->get();
$utm->delete();

// ####### Вывод содержимого #######################################################
function component()
{
	global $db, $root, $domain, $utm_arr, $shopSettings, $paymethod, $fio, $tel, $email, $address, $comments, $ip;
/*
	// Картами (Yandex)
	if($paymethod == 41)
	{
		$paymentType = 'AC';
		$paymethod_out = '
		<tr>
			<td width="170"><div class="shop_basket_yandex_invoice">'.LANG_PAY_CARD.'</div></td>
			<td><img border="0" src="/components/shop/frontend/tmp/images/card.png"></td>
		</tr>
		';
	}

	// Яндекс - Деньги
	if($paymethod == 42)
	{
		$paymentType = 'PC';
		$paymethod_out = '
		<tr>
			<td width="170"><div class="shop_basket_yandex_invoice">'.LANG_PAY_YANDEX.'</div></td>
			<td><img border="0" src="/components/shop/frontend/tmp/images/yd.png"></td>
		</tr>
		';
	}
*/

	$paymentType = 'PC';
	$paymethod_out = '
	<tr>
		<td width="170"><div class="shop_basket_yandex_invoice">'.LANG_PAY_YANDEX.'</div></td>
		<td><img border="0" src="/components/shop/frontend/tmp/images/yd.png"></td>
	</tr>
	';


	// Удаляем из номера телефона " ", "+7", "-" и "8", если она идёт первой
	$arr = array(" ", "+7", "-");
	$tel = str_replace($arr,"",$tel);
	if ($tel[0] == "8"){$tel = substr($tel, 1); };

	
	include_once $root."/components/shop/classes/Orders.php";
	$order_id = Orders::getOrderId();
	$items = Orders::getItems();
	
	if(count($items) > 0)
	{
		$summa = 0;
		$yandex_items = '';
		$items_out = '';
		$items_email_out = '';		
		
		foreach($items as $key => $item)
		{
			if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
			if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}
			$sum = $item['price'] * $item['quantity'];

			$summa += $sum;
			
			$price = number_format($item['price'], 0, '', ' ');
			$sum_format = number_format($sum, 0, '', ' ');			
			if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}

			$items_out .= '<a target="_blank" href="/shop/item/'.$item['item_id'].'">'.$item['title'].' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';
			$items_email_out .= '<a target="_blank" href="http://'.$domain.'/shop/item/'.$item['item_id'].'">'.$item['title'].' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';			
		}

		$summa_format = number_format($summa, 0, '', ' ');		
		$order = "<span>".$items_out."<b>".LANG_SUM.": ".$summa_format." ".$shopSettings->currency."</b></span>";		

		// Подключаем шаблон формы для оплаты счета с помощью Yandex
		include($root."/components/shop/frontend/basket/yandex/tmp/yandex_tmp.php");


		// --- Заказы ---

		if(isset($_POST['tel']))
		{
			$tel = $_POST['tel'];
			if(strlen($tel) < 3){echo "<h1>".LANG_FIELD_PHONE_ERROR."</h1>";exit;}
		}

		$fio = $_POST['fio'];
		$email = $_POST['email'];
		$address = $_POST['address'];
		$comments = $_POST['comments'];		
		
		if(Auth::check()) // Пользователь авторизирован
		{
			Orders::updateOrderStatus(2);
		}
		else // Пользователь не авторизирован
		{
			$stmt_update = $db->prepare("
			UPDATE com_shop_orders
			SET orders = :orders,
			sum = :sum,
			fio = :fio,
			tel = :tel,
			email = :email,
			address = :address,
			comments = :comments,
			status = '2'
			WHERE id = :id
			");

			$stmt_update->execute(array(
			'id' => $order_id,
			'orders' => $order,
			'sum' => $summa,
			'fio' => $fio,
			'tel' => $tel,
			'email' => $email,
			'address' => $address,
			'comments' => $comments
			));
		}

		include_once $root."/components/shop/frontend/basket/tmp/mail.php";

		$utm_source = $utm_arr['utm_source'];
		$utm_medium = $utm_arr['utm_medium'];
		$utm_campaign = $utm_arr['utm_campaign'];
		$utm_content = $utm_arr['utm_content'];
		$utm_term = $utm_arr['utm_term'];
		$utm_date = $utm_arr['utm_date'];
		$utm_counter = $utm_arr['utm_counter'];

		/* Лиды */
		$leads_title = LANG_ORDER_FROM_SHOP.' - '.$summa.' '.$shopSettings->currency;
		$stmt_leads = $db->prepare("
		INSERT INTO com_leads
		SET title = :title,
		text = :text,
		type = '".LANG_ORDER_FROM_SHOP."',
		date = :date,
		utm_source = :utm_source,
		utm_medium = :utm_medium,
		utm_campaign = :utm_campaign,
		utm_content = :utm_content,
		utm_term = :utm_term,
		utm_date = :utm_date,
		utm_counter = :utm_counter,
		status = '0'
		");

		$stmt_leads->execute(array(
		'title' => $leads_title,
		'text' => $order,
		'date' => date("Y-m-d H:i:s"),
		'utm_source' => $utm_source,
		'utm_medium' => $utm_medium,
		'utm_campaign' => $utm_campaign,
		'utm_content' => $utm_content,
		'utm_term' => $utm_term,
		'utm_date' => $utm_date,
		'utm_counter' => $utm_counter
		));

		/* Отправка */
		$email_shop = Settings::instance()->getValue('email');
		$from = 'no-replay@'.$domain;
		$subject = LANG_ORDER_FROM_SHOP." ".$domain;

		include_once $root."/components/shop/frontend/basket/tmp/mail.php";			
		
		classMail::send($email_shop, $from, $subject, $email_content, null);
		classMail::send($email, $from, $subject, $email_content, null);

	}
	else
	{
		echo'
			<div class="main-right-header-1"></div>
			<div class="main-right-header-2">
				<div class="shop-item-title-2">'.LANG_BASKET_IS_EMPTY.'</div>
				<div class="basket-item">'.LANG_YOU_SHOULD_ADD_ITEMS.'</div>
			</div>
		';		
	}

} // конец функции component

?>
