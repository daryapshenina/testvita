<?php
defined('AUTH') or die('Restricted access');

// шапка корзины
echo
'
<form method="POST" action="/shop/basket/pay2">
	<div class="main-right-header-1"></div>
	<div class="main-right-header-2">
		<div class="shop-item-title-2">ВАРИАНТЫ ОПЛАТЫ</div>
	</div>
	<div>
		<div>&nbsp;</div>
		<table class="pay-tab">
';


if($shopSettings->payment_method_cash == 1) // Наличными при получении
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_cash" class="input" type="radio" value="cash" name="paymethod"><label for="paymethod_cash"></label></td>
			<td class="pay-paymethod">Наличными при получении</td>
			<td class="pay-paymethod-images"><img border="0" src="/components/shop/frontend/basket/tmp/images/cash.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}


if($shopSettings->payment_method_prepayment == 1) // Предоплата
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_prepayment" class="input" type="radio" value="prepayment" name="paymethod"><label for="paymethod_prepayment"></label></td>
			<td class="pay-paymethod">Предоплата</td>
			<td class="pay-paymethod-images"><img border="0" src="/components/shop/frontend/basket/tmp/images/cashpred.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}


if($shopSettings->payment_method_сash_on_delivery == 1) // Наложным платежом
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_сash_on_delivery" class="input" type="radio" value="сash_on_delivery" name="paymethod"><label for="paymethod_сash_on_delivery"></label></td>
			<td class="pay-paymethod">Наложенным платежём</td>
			<td class="pay-paymethod-images"><img border="0" src="/components/shop/frontend/basket/tmp/images/delivery.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}


if($shopSettings->payment_method_yandex == 1) // Яндекс Касса
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_yandex_cashbox" class="input" type="radio" value="yandex_cashbox" name="paymethod"><label for="paymethod_yandex_cashbox"></label></td>
			<td class="pay-paymethod">Яндекс-касса</td>
			<td class="pay-paymethod-images"><img border="0" src="/components/shop/frontend/basket/tmp/images/yandex_cashbox.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}


if($shopSettings->payment_method_yandex == 2) // Яндекс Деньги
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_yandex_money"  class="input" type="radio" value="yandex_money" name="paymethod"><label for="paymethod_yandex_money"></label></td>
			<td class="pay-paymethod">Яндекс-Деньги</td>
			<td class="pay-paymethod-images"><img border="0" src="/components/shop/frontend/basket/tmp/images/yd.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}


if($shopSettings->payment_method_sberbank == 1) // Сбербанк
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_sberbank" class="input" type="radio" value="sberbank" name="paymethod"><label for="paymethod_sberbank"></label></td>
			<td class="pay-paymethod">Сбербанк</td>
			<td class="pay-paymethod-images"><img style="border:0;" src="/components/shop/frontend/basket/tmp/images/sber.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}




/*
if ($payment_method_6 == 1) // Счёт
{
	echo'
		<tr>
			<td class="pay-radio"><input id="paymethod_6" class="input" type="radio" value="6" name="paymethod"><label for="paymethod_6"></label></td>
			<td class="pay-paymethod">Выписать счёт на оплату</td>
			<td class="pay-paymethod-images"><img border="0" src="/components/shop/frontend/basket/tmp/images/invoice.png"></td>
			<td class="pay-paymethod-help">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><hr/></td>
		</tr>
	';
}
*/

echo'
		</table>
		<div>&nbsp;</div>
		<input type="hidden" name="fio" value="'.$fio.'">
		<input type="hidden" name="tel" value="'.$tel.'">
		<input type="hidden" name="email" value="'.$email.'">
		<input type="hidden" name="address" value="'.$address.'">
		<input type="hidden" name="comments" value="'.$comments.'">
		<input type="submit" value="Далее" name="submit" class="button_green_light">
	</div>

</form>
';

?>