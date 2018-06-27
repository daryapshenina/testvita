<?php
// DAN 2013
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Настройка оплаты на сайте:</div>
		<div>&nbsp;</div>
		
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v"></td>
				<td class="cell-title-modules" >Параметр</td>
				<td class="cell-desc-modules" >Значение</td>			
			</tr>
		</table>		
	';	
	
		// вывод настроек	
		$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
			
		while($m = mysql_fetch_array($num)):
			$setting_id = $m['id'];
			$setting_name = $m['name'];
			$setting_parameter = $m['parametr'];
			
			// почта
			if ($setting_name == "email")
			{
				$shop_email = '<input type="email" name="shopemail" size="50" value="'.$setting_parameter.'" required >';
			} 	
			
			// размер по "х" малого изображения 
			if ($setting_name == "x_small")
			{
				$x_small= '<input type="number" min="50" max="1000" type="text" name="xsmall" size="3" value="'.$setting_parameter.'">';
			} 	
			
			// размер по "y" малого изображения 
			if ($setting_name == "y_small")
			{
				$y_small= '<input type="number" min="50" max="1000" type="text" name="ysmall" size="3" value="'.$setting_parameter.'">';
			} 				
			
			// метод ресайза
			if ($setting_name == "small_resize_method")
			{
				$small_resize_method_checked = $setting_parameter;
				if ($small_resize_method_checked == "1"){$srm_checked_1 = "checked";} else {$srm_checked_1 = "";}
				if ($small_resize_method_checked == "2"){$srm_checked_2 = "checked";} else {$srm_checked_2 = "";}
				if ($small_resize_method_checked == "3"){$srm_checked_3 = "checked";} else {$srm_checked_3 = "";}
			} 	
			
			// количество товаров на странице 
			if ($setting_name == "quantity")
			{
				$quantity = '<input type="number" min="0" max="100" type="text" name="quantity" size="3" value="'.$setting_parameter.'">';
			} 	
			
			// отображение товаров на странице 
			if ($setting_name == "mapping")
			{
				$mapping_method_checked = $setting_parameter;
				if ($mapping_method_checked == "1"){$mapping_checked_1 = "checked";} else {$mapping_checked_1 = "";}
				if ($mapping_method_checked == "2"){$mapping_checked_2 = "checked";} else {$mapping_checked_2 = "";}
				if ($mapping_method_checked == "3"){$mapping_checked_3 = "checked";} else {$mapping_checked_3 = "";}				
			} 
						
			// сортировка товара на странице
			if ($setting_name == "sorting_items")
			{
				$sorting_cheked = $setting_parameter;
				if ($sorting_cheked == "0"){$sorting_cheked_0 = "selected";}
				elseif ($sorting_cheked == "1"){$sorting_cheked_1 = "selected";}
				elseif ($sorting_cheked == "2"){$sorting_cheked_2 = "selected";}
				elseif ($sorting_cheked == "3"){$sorting_cheked_3 = "selected";}
				elseif ($sorting_cheked == "4"){$sorting_cheked_4 = "selected";}
				elseif ($sorting_cheked == "5"){$sorting_cheked_5 = "selected";}
				else {$sorting_cheked_0 = "selected";}
			} 	
			
			if ($setting_name == "output_un_section")
			{
				if ($setting_parameter == 1)
				{
					$output_un_section_cheked = 'checked="checked"';
				}
				else
				{
					$output_un_section_cheked = '';
				}
			}

			// оплата 
			if ($setting_name == "payment")
			{
				$payment_checked = $setting_parameter;
				if ($payment_checked == "0"){$payment_checked_1 = "checked";} else {$payment_checked_1 = "";}
				if ($payment_checked == "1"){$payment_checked_2 = "checked";} else {$payment_checked_2 = "";}			
			} 	

			// метод оплаты 
			if ($setting_name == "paymentmethod")
			{
				// разбиваем на массив
				$paymentmethod = $setting_parameter;
				$pay_m = explode(",", $paymentmethod);
				if ($pay_m[0] == "1"){$payment_method_checked_1 = "checked";} else {$payment_method_checked_1 = "";}
				if ($pay_m[1] == "1"){$payment_method_checked_2 = "checked";} else {$payment_method_checked_2 = "";}
				if ($pay_m[2] == "1"){$payment_method_checked_3 = "checked";} else {$payment_method_checked_3 = "";}
				if ($pay_m[3] == "1"){$payment_method_checked_4 = "checked";} else {$payment_method_checked_4 = "";}
				if ($pay_m[4] == "1"){$payment_method_checked_5 = "checked";} else {$payment_method_checked_5 = "";}
			} 
			
			// QIWI 
			if ($setting_name == "qiwi_id")
			{
				$qiwi_id = $setting_parameter;
			}

			// Yandex id
			if ($setting_name == "yandex_id")
			{
				$yandex_id = $setting_parameter;
			}

			// Yandex secret
			if ($setting_name == "yandex_secret")
			{
				$yandex_secret = $setting_parameter;
			}			

			// Реквизиты для договора 
			if ($setting_name == "contract")
			{
				$contract_checked = $setting_parameter;
				if ($contract_checked == "0"){$contract_checked_0 = "checked";} else {$contract_checked_0 = "";}
				if ($contract_checked == "1"){$contract_checked_1 = "checked";} else {$contract_checked_1 = "";}			
			} 			

		endwhile;	
		
	echo
	'
		<script type="text/javascript">
		
		function payment_hide() 
		{
			var payment_status = document.getElementsByName("payment");
			
			if (payment_status[0].checked)
			{
				document.getElementById("payment_method").style.display = "none";
			}
			else
			{
				document.getElementById("payment_method").style.display = "table-row";
			}
		}
		
		function qiwi_hide() 
		{
			var qiwi_status = document.getElementsByName("paymentmethod_qiwi");
			
			if (qiwi_status[0].checked)
			{
				document.getElementById("qiwi_number").style.display = "block";
			}
			else
			{
				document.getElementById("qiwi_number").style.display = "none";
			}
		}
		
		function yandex_hide() 
		{
			var qiwi_status = document.getElementsByName("paymentmethod_yandex");
			
			if (qiwi_status[0].checked)
			{
				document.getElementById("yandex_number").style.display = "block";
			}
			else
			{
				document.getElementById("yandex_number").style.display = "none";
			}
		}		
		</script>
	';			
		
	// вывод параметров	
	echo'	
		<form method="POST" action="http://'.$site.'/admin/com/shop/paymentupdate">			
		<table class="w100_bs1">
			<tr>
				<td class="cell-v ">&nbsp;</td>
				<td class="cell-title-modules">&nbsp;</td>
				<td class="cell-desc-modules">&nbsp;</td>
			</tr>
			<tr>
				<td class="cell-v shop-tab-title">&nbsp;</td>
				<td class="cell-title-modules shop-tab-title"><span class="lineheight20"><b>ОПЛАТА:</b></span></td>
				<td class="cell-desc-modules shop-tab-title">&nbsp;</td>
			</tr>			
			<tr>
				<td class="cell-v ">1</td>
				<td class="cell-title-modules"><b>Оплата</b></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="0" '.$payment_checked_1.' name="payment" onclick="payment_hide()">Без выбора варианта оплаты <br/>
						<input type="radio" value="1" '.$payment_checked_2.' name="payment" onclick="payment_hide()">С выбором варианта оплаты <br/>						
					</span>					
				</td>
			</tr>
			<tr id="payment_method" >
				<td class="cell-v ">2</td>
				<td class="cell-title-modules"><b>Варианты оплаты:</b></td>
				<td class="cell-desc-modules">
					<div class="lineheight20">
						<div><input type="checkbox" value="1" '.$payment_method_checked_1.' name="paymentmethod_nal">Наличными при получении </div>
						<div><input type="checkbox" value="1" '.$payment_method_checked_5.' name="paymentmethod_pred">Предоплата </div>
						<div><input type="checkbox" value="1" '.$payment_method_checked_2.' name="paymentmethod_np">Наложенным платежём (почта России) </div>
						<div><input type="checkbox" value="1" '.$payment_method_checked_3.' name="paymentmethod_qiwi" onclick="qiwi_hide()">QIWI - кошелёк </div>
						<div id="qiwi_number" class="lineheight20 settings_payment_systems">
							<div><b>QIWI</b> - настройки:</div>
							<div>id в системе QIWI: <input type="text" value="'.$qiwi_id.'" name="qiwi_id"></div>	
							<div><b><font color="#ff0000">ВАЖНО:</font></b></div>
							<div>1. Обязательно укажите id в системе QIWI </div>
							<div>2. Обязательно пройдите регистрацию и проверку магазина в QIWI: <a href="https://ishop.qiwi.ru/register.action" target="blank">https://ishop.qiwi.ru/register.action</a> - без этого оплата работать не будет!</div>
							<div>3. Укажите в настройках магазина <i>URL для отправки в случае успешной оплаты счёта</i> &nbsp;&nbsp;<b><font color="#0099ff">http://'.$site.'/shop/basket/qiwisuccess</font></b></div>
							<div>4. Укажите в настройках магазина <i>URL для отправки в случае ошибки</i> &nbsp;&nbsp;<b><font color="#0099ff">http://'.$site.'/shop/basket/qiwierr</font></b></div>
						</div>
						<div><input type="checkbox" value="1" '.$payment_method_checked_4.' name="paymentmethod_yandex" onclick="yandex_hide()">Яндекс-Деньги </div>
						<div id="yandex_number" class="lineheight20 settings_payment_systems">
							<div><b>Яндекс</b> (Картами и Яндекс-Деньги)</div>
							<div class="yandex_input_label" >Номер кошелька:</div><div><input type="text" value="'.$yandex_id.'" name="yandex_id"></div>
							';
							
							/*
							echo'
							<div class="yandex_input_label" >СЕКРЕТ:</div><div><input type="text" value="'.$yandex_secret.'" name="yandex_secret"> выдаётся сервисом Яндекс-Деньги</div>
							<div>Адрес для получения уведомления (укажите его в поле HTTP-уведомления) в Яндекс-Деньгах &nbsp;&nbsp;<b><font color="#0099ff">http://'.$site.'/shop/basket/yandexsuccess</font></b></div>
							';
							*/
							
							echo'
						</div>							
					</div>					
				</td>
			</tr>
			<tr>
				<td class="cell-v ">&nbsp;</td>
				<td class="cell-title-modules">&nbsp;</td>
				<td class="cell-desc-modules">&nbsp;</td>
			</tr>				
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;			
		</form>
		<script type="text/javascript">
			payment_hide();
			qiwi_hide();
			yandex_hide();
		</script>			
	';	
}
?>