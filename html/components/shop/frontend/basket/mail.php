<?php
defined('AUTH') or die('Restricted access');

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['tel']))
	{
		$tel = $_POST['tel'];
		if(strlen($tel) < 3){echo "<h1>".LANG_FIELD_PHONE_ERROR."</h1>";exit;}
	}

	include_once $root."/components/shop/classes/Orders.php";

	$fio = $_POST['fio'];
	if(Auth::check()){$user = Auth::getUser(); $email = $user['email'];}else{$email = $_POST['email'];}	
	$address = $_POST['address'];
	$comments = $_POST['comments'];

	$order_id = Orders::getOrderId();
	$items = Orders::getItems();

	$summa = 0;
	$items_out = '';
	$items_email_out = '';

	foreach($items as $key => $item)
	{
		if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
		if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}
		$sum = $item['price'] * $item['quantity'];
		if($item['price'] == 0){$price = '';}
		else {$price = number_format($item['price'], 0, '', ' ');}

		$summa += $sum;
		$sum_format = number_format($sum, 0, '', ' ');

		$items_out .= '<a target="_blank" href="/shop/item/'.$item['item_id'].'">'.$item['title']." ".$item['chars']." ".$item['quantity']." x ".$price." ".$shopSettings->currency." = ".$sum_format." ".$shopSettings->currency."</a></br>";
		$items_email_out .= '<div><a target="_blank" href="http://'.$domain.'/shop/item/'.$item['item_id'].'">'.$item['title']." ".$item['chars']." ".$item['quantity']." x ".$price." ".$shopSettings->currency." = ".$sum_format." ".$shopSettings->currency."</a></div>";
	}

	if(count($items) > 0)
	{
		$summa_format  = number_format($summa, 0, '', ' ');

		$order = '<span>'.$items_out.'<b>'.LANG_SUM.': '.$summa_format.' '.$shopSettings->currency.'</b></span>';

		$user_id = Auth::check();
		if(!$user_id){$user_id = 0;}

		$stmt_update = $db->prepare("
		UPDATE com_shop_orders
		SET 
		user_id = :user_id,
		orders = :orders,
		sum = :sum,
		fio = :fio,
		tel = :tel,
		email = :email,
		address = :address,
		comments = :comments,
		status = '2',
		date_order = :date_order
		WHERE id = :id
		");

		$stmt_update->execute(array(
		'id' => $order_id,
		'user_id' => $user_id,
		'orders' => $order,
		'sum' => $summa,
		'fio' => $fio,
		'tel' => $tel,
		'email' => $email,
		'address' => $address,
		'comments' => $comments,
		'date_order' => date("Y-m-d H:i:s")
		));


		$utm_arr = $utm->get();
		$utm->delete();

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
		include_once $root."/components/shop/frontend/basket/tmp/mail.php";		
		$email_shop = Settings::instance()->getValue('email');

		if(isset($domain_idn)){$dmn = $domain_idn;}else{$dmn = $domain;}
		$from = 'no-replay@'.$dmn;
		$subject = LANG_ORDER_FROM_SHOP." ".$dmn;

		classMail::send($email_shop, $from, $subject, $email_content, null);
		classMail::send($email, $from, $subject, $email_content, null);	

		Header ("Location: /shop/basket/mail"); exit;
	}
	else
	{
		function component()
		{		
			echo'
				<div class="main-right-header-1"></div>
				<div class="main-right-header-2">
					<div class="shop-item-title-2">'.LANG_BASKET_IS_EMPTY.'</div>
					<div class="basket-item">'.LANG_YOU_SHOULD_ADD_ITEMS.'</div>
				</div>
			';
		}
	}
}
else // GET
{
	function component()
	{
		echo '<h1>Ваш заказ отправлен</h1>';	
	}
}

?>