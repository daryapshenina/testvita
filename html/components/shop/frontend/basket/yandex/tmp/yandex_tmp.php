<?php
defined('AUTH') or die('Restricted access');

$dt = getdate();

if($shopSettings->yandex_cashbox_test == 1) $yandex_url = 'https://demomoney.yandex.ru/reg/'; // Тестовый режим
	else $yandex_url = 'https://money.yandex.ru/quickpay/confirm.xml'; // Рабочий режим

// Форма ввода данных для оплаты с помощью QIWI
echo
'
<div align="center">
	<div class="shop_basket_yandex_form">
		<form method="POST" action="'.$yandex_url.'">
			<table class="shop_basket_yandex_tab">
				'.$paymethod_out.'
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Оплата за</td>
					<td>
						<div>'.$items_out.'</div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Сумма</td>
					<td>'.$summa_format.' руб.</td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Комментарий</td>
					<td>'.$comments.'</td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
			</table>

			<input type="hidden" name="receiver" value="'.$shopSettings->yandex_id.'">
			<input type="hidden" name="formcomment" value="Заказ на сумму: '.$summa.' руб. Дата: '.$dt['mday'].'.'.$dt['mon'].'.'.$dt['year'].'">
			<input type="hidden" name="short-dest" value="Заказ на сумму: '.$summa.' руб. Дата: '.$dt['mday'].'.'.$dt['mon'].'.'.$dt['year'].'">
			<input type="hidden" name="quickpay-form" value="shop">
			<input type="hidden" name="targets" value="Платёж от '.$fio.'">
			<input type="hidden" name="sum" value="'.$summa.'" data-type="number" >
			<input type="hidden" name="paymentType" value="'.$paymentType.'">
			<input type="hidden" name="comment" value="'.$comments.'" >
			<input type="hidden" name="need-fio" value="false">
			<input type="hidden" name="need-email" value="false" >
			<input type="hidden" name="need-phone" value="true">
			<input type="hidden" name="need-address" value="false">

			<input type="submit" value="Оплатить" class="shop-button" name="shopbutton">
		</form>
	</div>
</div>
';

$data = date("Y-m-d H:i:s");

$email_content = '
<h1>Заказ из интернет-магазина с сайта <a target="_blank" href="">'.$domain.'</a></h1>
<div>'.$data.'</div>
<div>&nbsp;</div>
<div>'.$items_out.'</div>
<div><b>Итого: '.$summa_format.' '.$shopSettings->currency.'</b></div>
<div>&nbsp;</div>
<div>ФИО: &nbsp; <b>'.$fio.'</b></div>
<div>Телефон контакта: &nbsp; <b>'.$tel.'</b></div>
<div>Email: &nbsp; <b>'.$email.'</b></div>
<div>Адрес доставки: &nbsp; <b>'.$address.'</b></div>
<div>Комментарии: &nbsp; <b>'.$comments.'</b></div>
<div>Метод оплаты: &nbsp; <b>'.$paymethod_type.'</b></div>
';


?>
