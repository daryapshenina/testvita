<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/frontend/basket/yandex/tmp/style.css');

function component()
{
	global $db, $root, $domain;

	include_once $root."/components/shop/classes/Orders.php";


	if (Auth::check())
	{
		$user = Auth::getUser();
		$user_id = $user['id'];

		// Ищем оплаченный заказ в БД
		$stmt_order_user = $db->prepare("SELECT id, orders, sum, comments, status FROM com_shop_orders WHERE user_id = :user_id AND ORDER BY id DESC LIMIT 1");
		$stmt_order_user->execute(array('user_id' => $user_id));
		$order = $stmt_order_user->fetch();
	}
	else // Ищем заказ по хешу
	{
		$hash = $_COOKIE['shop_order_hash'];

		// Ищем оплаченный заказ в БД
		$stmt_order_hash = $db->prepare("SELECT id, orders, sum, comments, status FROM com_shop_orders WHERE hash = :hash ORDER BY id DESC LIMIT 1");
		$stmt_order_hash->execute(array('hash' => $hash));
		$order = $stmt_order_hash->fetch();
	}



	echo'<h1>Оплата с помощью Яндекс-Кассы - завершена успешно!</h1>';

	if(count($order) > 0)
	{
		$summa_format = number_format($order['sum'], 0, '', ' ');

		if($order['status'] == 1){$status = 'Заказ оплачен!';}
		if($order['status'] == 2){$status = 'Обработка платежа.';}		

		echo
		'
		<div align="center">
			<div class="shop_basket_yandex_form">
				<table class="shop_basket_yandex_tab">
					<tr>
						<td class="shop_basket_yandex_td_1">Статус заказа</td>
						<td>
							<div>'.$status.'</div>
						</td>
					</tr>				
					<tr>
						<td class="shop_basket_yandex_td_1">Оплата за</td>
						<td>
							<div>'.$order['orders'].'</div>
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
						<td>'.$order['comments'].'</td>
					</tr>
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
				</table>
			</div>
		</div>
		';
	}
}
?>