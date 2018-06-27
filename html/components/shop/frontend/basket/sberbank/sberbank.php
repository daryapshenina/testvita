<?php
defined('AUTH') or die('Restricted access');
include_once $root."/components/shop/classes/Orders.php";
include_once $root."/components/shop/frontend/basket/sberbank/sberbankPay.class.php";

$order_id = Orders::getOrderId();
$items = Orders::getItems();

$utm_arr = $utm->get();
$utm->delete();


if(count($items) > 0)
{
	$items_out = '';
	$items_email_out = '';
	$summa = 0;

	foreach($items as $key => $item)
	{
		if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
		if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}
		$sum = $item['price'] * $item['quantity'];

		$summa += $sum;

		$s = array("'", '"');
		$item_title = str_replace($s, "", $item['title']);
		
		$price = number_format($item['price'], 0, '', ' ');
		$sum_format = number_format($sum, 0, '', ' ');

		if(intval($item['quantity']) == $item['quantity']) $item['quantity'] = intval($item['quantity']); // Приведение типов

		$items_out .= '<a target="_blank" href="/shop/item/'.$item['item_id'].'">'.$item['title'].' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';
		$items_email_out .= '<a target="_blank" href="http://'.$SITE->domain.'/shop/item/'.$item['item_id'].'">'.$item_title.' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';
	}

	$summa_format = number_format($summa, 0, '', ' ');		
	$order = "<span>".$items_out."<b>".LANG_SUM.": ".$summa_format." ".$shopSettings->currency."</b></span>";
}
else
{
	exit('Нет товаров в корзине');
}

$currency = $shopSettings->currency;

// Orders::updateOrderStatus(2);

$sber = new sberbankPay();
$response = $sber->registerOrder($order_id, $summa, $email, $tel);

$err = '';

if($response)
{
    if(isset($response['orderId']) && isset($response['formUrl']))
    {
    	$sber_order_id = $response['orderId'];

		$stmt_update = $db->prepare("
		UPDATE com_shop_orders SET 
		orders = :orders,
		sum = :sum,
		fio = :fio,
		tel = :tel,
		email = :email,
		address = :address,
		comments = :comments,
		payment_system = 'sberbank',
		payer = :payer
		WHERE id = :order_id
		");

		$stmt_update->execute(array(
		'orders' => $order,
		'sum' => $summa,
		'fio' => $fio,
		'tel' => $tel,
		'email' => $email,
		'address' => $address,
		'comments' => $comments,
		'payer' => $sber_order_id,
		'order_id' => $order_id,
		));


		$utm_source = $utm_arr['utm_source'];
		$utm_medium = $utm_arr['utm_medium'];
		$utm_campaign = $utm_arr['utm_campaign'];
		$utm_content = $utm_arr['utm_content'];
		$utm_term = $utm_arr['utm_term'];
		$utm_date = $utm_arr['utm_date'];
		$utm_counter = $utm_arr['utm_counter'];

		// --- Лиды ---
		$leads_title = LANG_ORDER_FROM_SHOP.' - '.$summa_format.' '.$currency.' '.LANG_ORDER_NUMBER.' - '.$order_id;
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

    	Header ("Location: ".$response['formUrl']); 
    	exit;
    }

    $err = 'Код ошибки: '.$response['errorCode'].'<br>'.$response['errorMessage'];
    Orders::updateOrderStatus(2);
}



// Если не сработало перенаправление - вызываем функцию
$SITE->errLog($response);

function component()
{
	global $err;

	echo '<h1>Ошибка операции</h1>';
	echo $err;
}

?>