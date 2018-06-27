<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/frontend/basket/yandex/tmp/style.css');

$utm_arr = $utm->get();
$utm->delete();

// ####### Вывод содержимого #######################################################
function component()
{
	global $db, $root, $SITE->domain, $tel, $fio, $address, $comments, $utm_arr, $shopSettings;

	include_once $root."/components/shop/classes/Orders.php";

	if(Auth::check())
	{
		$user = Auth::getUser();
		$email = $user['email'];
	}
	else
	{
		$user['id'] = 0;
		$email = $_POST['email'];
	}

	$order_id = Orders::getOrderId();
	$items = Orders::getItems();


	if(count($items) > 0)
	{
		$summa = 0;
		$yandex_items = '';
		$items_out = '';
		$items_email_out = '';
		$items_arr = array();

        $json_data = array(
			'customerContact' => $tel,
            'items' => array(),
        );
		
		foreach($items as $key => $item)
		{
			if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
			if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}
			$sum = $item['price'] * $item['quantity'];

			if(isset($item['nds'])){$nds = $item['nds'];} else{$nds = 1;}

			$summa += $sum;

			$s = array("'", '"');
			$item_title = str_replace($s, "", $item['title']);
			
			$price = number_format($item['price'], 0, '', ' ');
			$sum_format = number_format($sum, 0, '', ' ');			
			if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);} // Приведение типов

			$items_out .= '<a target="_blank" href="/shop/item/'.$item['item_id'].'">'.$item['title'].' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';
			$items_email_out .= '<a target="_blank" href="http://'.$SITE->domain.'/shop/item/'.$item['item_id'].'">'.$item_title.' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';

            $json_data['items'][] = array(
                'quantity' => $item['quantity'],
                'price' => array(
                    'amount' => number_format($item['price'], 2, '.', ''),
                    ),
                'tax' => $nds,
				'text' => strip_tags(mb_substr($item_title.' '.$item['chars'], 0, 128))
                );
		}

		$summa_format = number_format($summa, 0, '', ' ');		
		$order = "<span>".$items_out."<b>".LANG_SUM.": ".$summa_format." ".$shopSettings->currency."</b></span>";



		$json = json_encode($json_data, JSON_UNESCAPED_UNICODE);



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



		$utm_source = $utm_arr['utm_source'];
		$utm_medium = $utm_arr['utm_medium'];
		$utm_campaign = $utm_arr['utm_campaign'];
		$utm_content = $utm_arr['utm_content'];
		$utm_term = $utm_arr['utm_term'];
		$utm_date = $utm_arr['utm_date'];
		$utm_counter = $utm_arr['utm_counter'];

		// --- Лиды ---
		$leads_title = LANG_ORDER_FROM_SHOP.' - '.$summa_format.' '.$shopSettings->currency.' '.LANG_ORDER_NUMBER.' - '.$order_id.'. '.LANG_ORDER_YANDEX_CASHBOX_SELECT.'.';
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


		$dt = getdate();

		if($shopSettings->yandex_cashbox_test == 1) // Тестовый режим
		{
			$yandex_url = 'https://demomoney.yandex.ru/eshop.xml';
		}
		else // Рабочий режим
		{
			$yandex_url = 'https://money.yandex.ru/eshop.xml';
		}

		echo
		'
		<h1>Оплатить с помощью Яндекс-Кассы</h1>	
		<div align="center">
			<div class="shop_basket_yandex_form">
				<table class="shop_basket_yandex_tab">
					<tr>
						<td class="shop_basket_yandex_td_1">Оплата за</td>
						<td>
							<div>'.$items_out.'</div>
						</td>
					</tr>
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
					<tr>
						<td class="shop_basket_yandex_td_1">Сумма</td>
						<td>'.$summa_format.' руб.</td>
					</tr>
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
					<tr>
						<td class="shop_basket_yandex_td_1">Комментарий</td>
						<td>'.$comments.'</td>
					</tr>
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
				</table>

				<form action="'.$yandex_url.'" method="post">
				    <input name="shopId" value="'.$shopSettings->yandex_cashbox_shop_id.'" type="hidden">
				    <input name="scid" value="'.$shopSettings->yandex_cashbox_scid.'" type="hidden">
				    <input name="sum" value="'.$summa.'" type="hidden">
				    <input name="customerNumber" value="'.$user['id'].'" type="hidden">
				    <input name="paymentType" value="" type="hidden"/>
				    <input name="orderNumber" value="'.$order_id.'" type="hidden">
				    <input name="cps_phone" value="'.$tel.'" type="hidden">
				    <input name="cps_email" value="'.$email.'" type="hidden">
				    <input name="ym_merchant_receipt" value=\''.$json.'\' type="hidden">
				  	<input class="button_green" type="submit" value="Оплатить"/>
				</form>
			</div>
		</div>
		';
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