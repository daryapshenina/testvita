<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/shop/frontend/basket/tmp/client.css');
$head->addFile('/components/shop/frontend/basket/tmp/client.js');

include_once($root."/classes/Auth.php");
include_once $root."/components/shop/classes/Orders.php";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['quantity']))
	{
		foreach($_POST['quantity'] as $id => $quantity)
		{
			// Обновляем количество товара.
			Orders::updateItem($id, $quantity);
		}
	}
	Header ("Location: /shop/basket/client"); exit;
}


function component()
{
	global $domain, $root, $shopSettings;

	// Проверка подключения оплаты
	if($shopSettings->payment_method_cash == '1' || $shopSettings->payment_method_prepayment == '1' || $shopSettings->payment_method_сash_on_delivery == '1' || $shopSettings->payment_method_yandex > 0 || $shopSettings->payment_method_sberbank == '1')
	{
		$button = '<input type="submit" value="'.LANG_PAYMENT.'" class="button_green_light" name="shopbutton"/>';
		$url = '/shop/basket/pay';
	}
	else
	{
		$button = '<input type="submit" value="'.LANG_CHECKOUT.'" class="button_green_light" name="shopbutton"/>';
		$url = '/shop/basket/mail';
	}


	// Получаем массив товаров в заказе
	$items = Orders::getItems();

	if(count($items) > 0) // Если есть товары в корзине
	{
		$email_out = '<input class="input" type="email" name="email" value="">';

		if(Auth::check())
		{
			$user = Auth::getUser();
			$email_out = '<span style="line-height:40px;font-size:16px;font-weight:bold;">'.$user['email'].'</span>';
		}

		if(Settings::instance()->getValue('personal_information') == 1)
		{
			$personal_information = '<tr><td class="basket-client-text"></td><td colspan="2"><input required checked title="Вы должны дать согласие перед отправкой" type="checkbox">Согласен на <a href="/personal-information" target="_blank">обработку персональных данных</a></td></tr>';
		}
		else{$personal_information = '';}

		echo '
			<h1>'.LANG_CONTACT_DETAILS.':</h1>
			<form method="POST" action="'.$url.'">
				<table class="basket-item-tab">
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_FULL_NAME.':</span></td>
						<td class="basket-client-input"><input class="input" type="text" name="fio" value="" ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PHONE.':</span></td>
						<td class="basket-client-input"><input id="tel" class="input" type="tel" name="tel" value="+7" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_EMAIL.':</span></td>
						<td class="basket-client-input">'.$email_out.'</td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_ADDRESS.':</span></td>
						<td class="basket-client-input"><input class="input" type="text" name="address" value="" ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_COMMENT.':</span></td>
						<td class="basket-client-input"><textarea class="input" rows="5" name="comments"></textarea></td>
					</tr>
					'.$personal_information.'
					<tr>
						<td class="basket-client-text"></td>
						<td class="basket-client-input">'.$button.'</td>
					</tr>
				</table>
			</form>
		';
	}
	else // Если нет товаров в корзине
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

?>