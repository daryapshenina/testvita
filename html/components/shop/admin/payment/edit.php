<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/payment/payment.css');
$head->addFile('/components/shop/admin/payment/payment.js');

function a_com()
{
	global $db, $domain, $item_section_id, $shopSettings;

	$payment_method_yandex_checked = '';
	$payment_method_ym_checked = '';
	$payment_method_yc_checked = '';
	$payment_method_yandex_cashbox_test_checked = '';

	if ($shopSettings->payment_method_cash == '1'){$payment_cash_checked = 'checked';} else {$payment_cash_checked = '';}
	if ($shopSettings->payment_method_prepayment == '1'){$payment_prepayment_checked = 'checked';} else {$payment_prepayment_checked = '';}
	if ($shopSettings->payment_method_сash_on_delivery == '1'){$payment_сash_on_delivery_checked = 'checked';} else {$payment_сash_on_delivery_checked = '';}
	if ($shopSettings->payment_method_yandex == '1'){$payment_method_yc_checked = "checked"; $payment_method_yandex_checked = "checked";}
	if ($shopSettings->payment_method_yandex == '2'){$payment_method_ym_checked = "checked"; $payment_method_yandex_checked = "checked";}
	if ($shopSettings->payment_method_sberbank == '1'){$payment_sberbank_checked = 'checked';} else {$payment_sberbank_checked = '';}
	if ($shopSettings->sberbank_test == '1'){$payment_sberbank_test_checked = 'checked';} else {$payment_sberbank_test_checked = '';}


	if($payment_method_ym_checked == ''){$payment_method_yc_checked = "checked";}

	if($shopSettings->yandex_cashbox_test == 1) $payment_method_yandex_cashbox_test_checked = "checked";

	$yandex_money_id = $shopSettings->yandex_money_id;
	$yandex_secret = $shopSettings->yandex_secret;

	$nds = $shopSettings->nds;
	$nds_selected = array_fill(1, 6, '');
	$nds_selected[$nds] = 'selected';

	echo '
		<h1>Настройка оплаты на сайте:</h1>
		<form method="POST" action="/admin/com/shop/payment/update">
					<div>&nbsp;</div>
					<hr class="hr_admin">
					<div class="lineheight20">
						<div class="flex m_h"><input id="payment_cash" class="input" type="checkbox" value="1" '.$payment_cash_checked.' name="payment_cash"><label for="payment_cash"></label> &nbsp; Наличными при получении </div>
						<hr class="hr_admin">
						<div class="flex m_h"><input id="payment_prepayment" class="input" type="checkbox" value="1" '.$payment_prepayment_checked.' name="payment_prepayment"><label for="payment_prepayment"></label> &nbsp; Предоплата </div>
						<hr class="hr_admin">
						<div class="flex m_h"><input id="payment_сash_on_delivery" class="input" type="checkbox" value="1" '.$payment_сash_on_delivery_checked.' name="payment_сash_on_delivery"><label for="payment_сash_on_delivery"></label> &nbsp; Наложенным платежём (почта России) </div>
						<hr class="hr_admin">
								<div>&nbsp;</div>
								<div>НДС для эквайринга</div>
								<div class=" flex">
									<select name="nds" class="input">
										<option '.$nds_selected[1].' value="1">без НДС</option>
										<option '.$nds_selected[2].' value="2">НДС по ставке 0%</option>
										<option '.$nds_selected[3].' value="3">НДС чека по ставке 10%</option>
										<option '.$nds_selected[4].' value="4">НДС чека по ставке 18%</option>
										<option '.$nds_selected[5].' value="5">НДС чека по расчетной ставке 10/110</option>
										<option '.$nds_selected[6].' value="6">НДС чека по расчетной ставке 18/118</option>
									</select>&nbsp;Ставка НДС (данные для формирования чека)
								</div>
								<div>&nbsp;</div>
						<hr class="hr_admin">												
						';
/*
						echo '
						<div><input type="checkbox" value="1" '.$payment_method_checked_3.' name="paymentmethod_qiwi" onclick="qiwi_hide()">QIWI - кошелёк </div>
						<hr class="hr_admin">
						<div id="qiwi_number" class="lineheight20 settings_payment_systems">
							<div><b>QIWI</b> - настройки:</div>
							<div>id в системе QIWI: &nbsp;<input type="text" value="'.$qiwi_id.'" name="qiwi_id" class="input"></div>
							<div><b>ВАЖНО:</b></div>
							<div>1. Обязательно укажите id в системе QIWI </div>
							<div>2. Обязательно пройдите регистрацию и проверку магазина в QIWI: <a href="https://ishop.qiwi.ru/register.action" target="blank">https://ishop.qiwi.ru/register.action</a> - без этого оплата работать не будет!</div>
							<div>3. Укажите в настройках магазина <i>URL для отправки в случае успешной оплаты счёта</i> &nbsp;&nbsp;<b><font color="#0099ff">/shop/basket/qiwisuccess</font></b></div>
							<div>4. Укажите в настройках магазина <i>URL для отправки в случае ошибки</i> &nbsp;&nbsp;<b><font color="#0099ff">/shop/basket/qiwierr</font></b></div>
						</div>
						<hr class="hr_admin">
						';
*/
						echo '
						<div class="flex m_h"><input id="payment_yandex" class="input" type="checkbox" value="1" '.$payment_method_yandex_checked.' name="payment_yandex" onchange="yandex();"><label for="payment_yandex"></label> &nbsp; Яндекс деньги / Яндекс касса </div>
						<div id="yandex_container">
							<div class="flex"><input id="payment_yandex_yc" type="radio" value="1" '.$payment_method_yc_checked.' name="payment_yandex_type" class="input" onchange="yandex();"><label for="payment_yandex_yc"></label>Яндекс-Касса</div>
							<div id="yandex_container_yc">
								<div class="flex"><input type="checkbox" value="1" name="yandex_cashbox_test" id="yandex_cashbox_test" '.$payment_method_yandex_cashbox_test_checked.'>&nbsp;Тестовый режим</div>
								<div class="flex"><input type="text" value="https://'.$domain.$shopSettings->yandex_cashbox_check_url.'" class="input payment_read_only" readonly>&nbsp;Проверочный URL (checkUrl / avisoUrl)</div>
								<div class="flex"><input type="text" value="https://'.$domain.$shopSettings->yandex_cashbox_success_url.'" class="input payment_read_only" readonly>&nbsp;Страница успешной оплаты (shopSuccessUrl)</div>
								<div class="flex"><input type="text" value="https://'.$domain.$shopSettings->yandex_cashbox_fail_url.'" class="input payment_read_only" readonly>&nbsp;Страница неудачной оплаты (shopFailUrl)</div>
								<hr class="hr_admin">
								<div>&nbsp;</div>
								<div class="flex"><input type="text" value="'.$shopSettings->yandex_cashbox_shop_id.'" name="yandex_cashbox_shop_id" class="input">&nbsp;ID магазина (shopId)</div>
								<div class="flex"><input type="text" value="'.$shopSettings->yandex_cashbox_scid.'" name="yandex_cashbox_scid" class="input">&nbsp;ID витрины магазина (scid)</div>
								<div class="flex"><input type="text" value="'.$shopSettings->yandex_cashbox_password.'" name="yandex_cashbox_password" class="input">&nbsp;Пароль магазина (shopPassword)</div>
							</div>
							<div class="flex"><input id="payment_yandex_ym" type="radio" value="2" '.$payment_method_ym_checked.' name="payment_yandex_type" class="input" onchange="yandex();"><label for="payment_yandex_ym"></label>Яндекс-Деньги</div>
							<div id="yandex_container_ym" class="flex"><input type="text" value="'.$yandex_money_id.'" name="yandex_money_id" class="input">&nbsp;Номер кошелька</div>
						</div>
						<hr class="hr_admin">
						<div class="flex m_h"><input id="payment_sberbank" class="input" type="checkbox" value="1" '.$payment_sberbank_checked.' name="payment_sberbank" onchange="sber();"><label for="payment_sberbank"></label> &nbsp; Сбербанк </div>
						<div id="sberbank_container">
							<div class="flex"><input type="checkbox" value="1" name="sberbank_test" '.$payment_sberbank_test_checked.'>&nbsp;Тестовый режим</div>
							<div class="flex"><input class="input" type="text" value="'.$shopSettings->sberbank_login.'" name="sberbank_login" placeholder="Логин"></div>
							<div class="flex"><input class="input" type="text" value="'.$shopSettings->sberbank_password.'" name="sberbank_password" placeholder="Пароль"></div>							
						</div>
						<hr class="hr_admin">
						<div>&nbsp;</div>
					</div>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
		<br/>
		&nbsp;
		</form>
	';
}
?>