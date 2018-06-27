<?php
// DAN обновление - февраль 2014
// QIWI оплата
defined('AUTH') or die('Restricted access');

// Форма ввода данных для оплаты с помощью QIWI
echo
'
<form action="http://w.qiwi.ru/setInetBill_utf.do" method="POST">
	<div class="shop-item-title-2">QIWI</div>
	<div>&nbsp;</div>
	<div align="center">
		<div class="shop_basket_qiwi_form">
			<table class="shop_basket_qiwi_tab">
				<tr>
					<td width="50%"><img border="0" src="http://'.$site.'/components/shop/frontend/tmp/images/qiwi_logo.jpg"></td>
					<td class="shop_basket_qiwi_invoice">Выставить счёт за покупку <input type="hidden" name="from" value="'.$qiwi_id.'"></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td colspan="2" class="shop_basket_qiwi_invoice"><div align="center"><a href="http://'.$site.'/shop/basket/qiwihelp" target="blank">Как платить через QIWI кошелёк или в терминале QIWI</a></div></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Мобильный телефон <br/><font color="#aaaaaa">(пример: 9057772233)</font></td>
					<td>+7 <input type="number" min="1000000000" max="9999999999" name="to" size="10" maxlength="10"  shop_basket_qiwi_input" value="'.$tel.'"></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Сумма</td>
					<td>'.$summa.' '.$shopSettings->getValue('currency').'<input type="hidden" name="summ" value="'.$summa.'"></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Комментарий</td>
					<td><textarea name="com" rows="5" cols="30">'.$comments_qiwi.'</textarea></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Время действия счёта</td>
					<td>3 суток<input type="hidden" name="lifetime" value="72"></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td class="shop_basket_qiwi_invoice">Номер (код) заказа</td>
					<td>'.$qiwi_nm.'<input type="hidden" name="check_agt" value="false"><input type="hidden" name="txn_id" value="'.$qiwi_nm.'"></td>
				</tr>
				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<tr>
					<td colspan="2"><div align="center"><input type="submit" value="Выставить счёт на оплату" name="button" class="shop_basket_qiwi_button"></div></td>
				</tr>
			</table>
		</div>
	</div>
</form>
';

?>