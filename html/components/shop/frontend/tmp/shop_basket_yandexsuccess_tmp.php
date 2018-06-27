<?php
// DAN 2013
// выводит ответ при удачном платеже Яндекса
defined('AUTH') or die('Restricted access');


// шаблон корзины
echo 
'
	<div class="main-right-header-1"></div>
	<div class="main-right-header-2">
		<div class="shop-item-title-2">ВАШ ЗАКАЗ ОПЛАЧЕН '.$data.'</div>
		<div class="basket-item">Для обработки заказа возможно потребуется некоторое время.</div>
		'.$err.'
		<div class="basket-item"><b>Вы:</b> '.$order_fio.'</div>
		<div class="basket-item"><b>Ваш заказ:</b></div>		
		<div class="basket-item">'.$order.'</div>	
		<div>&nbsp;</div>
		'.$eitems_tmp.'			
		<div>&nbsp;</div>
		
		<div class="w-sep"></div>				
	</div>
';





// шаблон корзины
$basket_mail_tmp = 
'
<div align="center"><b>ВАШ ЗАКАЗ ОПЛАЧЕН '.$data.'</b></div>				
<div>&nbsp;</div>	
<div class="basket-item">'.$order.'</div>
<div>&nbsp;</div>
'.$eitems_tmp.'
<div>&nbsp;</div>
<table border="1" width="100%" style="border-collapse: collapse" cellspacing="0" cellpadding="5">			
	<tr>
		<td><b>ФИО:</b></td>	
		<td>'.$order_fio.'</td>						
	</tr>
	<tr>
		<td><b>Телефон контакта:</b></td>	
		<td>'.$order_tel.'</td>						
	</tr>
	<tr>
		<td><b>Email:</b></td>	
		<td>'.$order_email_client.'</td>						
	</tr>
	<tr>
		<td><b>Адрес доставки:</b></td>	
		<td>'.$order_address.'</td>						
	</tr>
	<tr>
		<td><b>Комментарии:</b></td>	
		<td>'.$order_comments.'</td>						
	</tr>
</table>
<div>&nbsp;</div>
Платёжная система: Яндекс-Деньги
<div>&nbsp;</div>
'.$order_payer.'
';

?>