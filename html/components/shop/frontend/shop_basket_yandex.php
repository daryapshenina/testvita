<?php
// DAN обновление - февраль 2014
// Яндекс - Деньги
defined('AUTH') or die('Restricted access');

// Картами (Yandex)
if($paymethod == 41 || $paymethod == 42)
{
	$paymentType = 'AC';
	$paymethod_out = '
	<tr>
		<td width="170"><div class="shop_basket_yandex_invoice">'.LANG_PAY_CARD.'</div></td>
		<td><img border="0" src="http://'.$domain.'/components/shop/frontend/tmp/images/card.png"></td>
	</tr>
	';
}

// Яндекс - Деньги
if($paymethod == 42)
{
	$paymentType = 'PC';
	$paymethod_out = '
	<tr>
		<td width="170"><div class="shop_basket_yandex_invoice">'.LANG_PAY_YANDEX.'</div></td>
		<td><img border="0" src="http://'.$domain.'/components/shop/frontend/tmp/images/yd.png"></td>
	</tr>
	';
}


// ####### Вывод содержимого #######################################################
function component()
{
	global $root, $domain, $dbname, $paymethod_out, $yandex_id, $email, $kolich, $fio, $tel, $email_client, $address, $comments, $summa, $paymentType, $ip, $shopSettings;

	// массив месяцев
	$mes[1] = 'января';
	$mes[2] = 'февраля';
	$mes[3] = 'марта';
	$mes[4] = 'апрель';
	$mes[5] = 'мая';
	$mes[6] = 'июня';
	$mes[7] = 'июля';
	$mes[8] = 'августа';
	$mes[9] = 'сентября';
	$mes[10] = 'октября';
	$mes[11] = 'ноября';
	$mes[12] = 'декабря';

	// ======= COOKIES ======================================================================
	if ($shopSettings->getValue('contract') == '1')
	{
		$lico = intval($_COOKIE['shop_lico']);

		// ------- Физ. лицо ----------------------------------------------------------------
		$fiz_f = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_f']));
		$fiz_i = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_i']));
		$fiz_o = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_o']));

		$fiz_dr = intval($_COOKIE['shop_fiz_dr']);
		$fiz_mr = intval($_COOKIE['shop_fiz_mr']);
		$fiz_gr = intval($_COOKIE['shop_fiz_gr']);

		$fiz_pasportseries = intval($_COOKIE['shop_fiz_pasportseries']);
		if($fiz_pasportseries == 0){$fiz_pasportseries = '';}
		$fiz_pasportnumber = intval($_COOKIE['shop_fiz_pasportnumber']);
		if($fiz_pasportnumber == 0){$fiz_pasportnumber = '';}

		$fiz_kemvidanpassport = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_kemvidanpassport']));

		$fiz_dv = intval($_COOKIE['shop_fiz_dv']);
		$fiz_mv = intval($_COOKIE['shop_fiz_mv']);
		$fiz_gv = intval($_COOKIE['shop_fiz_gv']);

		$fiz_propiska = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_propiska']));

		$fiz_indeks = intval($_COOKIE['shop_fiz_indeks']);
		if($fiz_indeks == 0){$fiz_indeks = '';}
		$fiz_oblast = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_oblast']));
		$fiz_gorod = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_gorod']));
		$fiz_adres = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_adres']));

		// ------- Юр. лицо -----------------------------------------------------------------
		$ur_naimenovanie = htmlspecialchars(strip_tags($_COOKIE['shop_ur_naimenovanie']));
		$ur_inn = htmlspecialchars(strip_tags($_COOKIE['shop_ur_inn']));
		$ur_kpp = htmlspecialchars(strip_tags($_COOKIE['shop_ur_kpp']));
		$ur_ogrn = htmlspecialchars(strip_tags($_COOKIE['shop_ur_ogrn']));

		$ur_urindeks = intval($_COOKIE['shop_ur_urindeks']);
		if($ur_urindeks == 0){$ur_urindeks = '';}
		$ur_uroblast = htmlspecialchars(strip_tags($_COOKIE['shop_ur_uroblast']));
		$ur_urgorod = htmlspecialchars(strip_tags($_COOKIE['shop_ur_urgorod']));
		$ur_uradres = htmlspecialchars(strip_tags($_COOKIE['shop_ur_uradres']));


		$ur_faktindeks = htmlspecialchars(strip_tags($_COOKIE['shop_ur_faktindeks']));
		if($ur_faktindeks == 0){$ur_faktindeks = '';}
		$ur_faktoblast = htmlspecialchars(strip_tags($_COOKIE['shop_ur_faktoblast']));
		$ur_faktgorod = htmlspecialchars(strip_tags($_COOKIE['shop_ur_faktgorod']));
		$ur_faktadres = htmlspecialchars(strip_tags($_COOKIE['shop_ur_faktadres']));
		$ur_fakttel = htmlspecialchars(strip_tags($_COOKIE['shop_ur_fakttel']));
		$ur_faktemail = htmlspecialchars(strip_tags($_COOKIE['shop_ur_faktemail']));

		$ur_direktor_f = htmlspecialchars(strip_tags($_COOKIE['shop_ur_direktor_f']));
		$ur_direktor_i = htmlspecialchars(strip_tags($_COOKIE['shop_ur_direktor_i']));
		$ur_direktor_o = htmlspecialchars(strip_tags($_COOKIE['shop_ur_direktor_o']));
	}
	// ======= / cookies / ==================================================================



	// ------- Физическое лицо -------
	$fiz_lico = '
		<table class="basket-item-tab basket-client-fizlico">
			<tr>
				<td  colspan="2" class="basket-client-text"><b>Физическое лицо:</b></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Фамилия:</span></td>
				<td class="basket-client-input">'.$fiz_f.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Имя:</span></td>
				<td class="basket-client-input">'.$fiz_i.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Отчество:</span></td>
				<td class="basket-client-input">'.$fiz_o.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Дата рождения:</span></td>
				<td class="basket-client-input">'.$fiz_dr.' '.$mes[$fiz_mr].' '.$fiz_gr.'
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="basket-client-text"><b>Паспорт:</b></td>
				<td class="basket-client-input"></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Паспорт - серия:</span></td>
				<td class="basket-client-input">'.$fiz_pasportseries.' номер: '.$fiz_pasportnumber.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Кем выдан паспорт:</span></td>
				<td class="basket-client-input">'.$fiz_kemvidanpassport.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Дата выдачи паспорта:</span></td>
				<td class="basket-client-input">'.$fiz_dv.' '.$mes[$fiz_mv].' '.$fiz_gv.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Прописка:</span></td>
				<td class="basket-client-input">'.$fiz_propiska.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="basket-client-text"><b>Почтовый адрес:</b></td>
				<td class="basket-client-input"></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Индекс:</span></td>
				<td class="basket-client-input">'.$fiz_indeks.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Область:</span></td>
				<td class="basket-client-input">'.$fiz_oblast.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Город:</span></td>
				<td class="basket-client-input">'.$fiz_gorod.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Адрес:</span></td>
				<td class="basket-client-input">'.$fiz_adres.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	';

	// ------- / физ. лицо / -------



	// ------- Юридическое лицо -------
	$ur_lico = '
		<table class="basket-item-tab basket-client-urlico">
			<tr>
				<td  colspan="2" class="basket-client-text"><b>Юридическое лицо:</b></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Наименование организации:</span></td>
				<td class="basket-client-input">'.$ur_naimenovanie.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">ИНН:</span></td>
				<td class="basket-client-input">'.$ur_inn.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">КПП:</span></td>
				<td class="basket-client-input">'.$ur_kpp.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">ОГРН:</span></td>
				<td class="basket-client-input">'.$ur_ogrn.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="basket-client-text"><b>Юридический адрес:</b></td>
				<td class="basket-client-input"></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Индекс:</span></td>
				<td class="basket-client-input">'.$ur_urindeks.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Область:</span></td>
				<td class="basket-client-input">'.$ur_uroblast.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Город:</span></td>
				<td class="basket-client-input">'.$ur_urgorod.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Адрес:</span></td>
				<td class="basket-client-input">'.$ur_uradres.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="basket-client-text"><b>Фактический адрес:</b></td>
				<td class="basket-client-input"></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Индекс:</span></td>
				<td class="basket-client-input">'.$ur_faktindeks.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Область:</span></td>
				<td class="basket-client-input">'.$ur_faktoblast.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Город:</span></td>
				<td class="basket-client-input">'.$ur_faktgorod.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Адрес:</span></td>
				<td class="basket-client-input">'.$ur_faktadres.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Телефон:</span></td>
				<td class="basket-client-input">'.$ur_fakttel.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Email:</span></td>
				<td class="basket-client-input">'.$ur_faktemail.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td  colspan="2" class="basket-client-text"><b>Директор:</b></td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Фамилия:</span></td>
				<td class="basket-client-input">'.$ur_direktor_f.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Имя:</span></td>
				<td class="basket-client-input">'.$ur_direktor_i.'</td>
			</tr>
			<tr>
				<td class="basket-client-text"><span class="fr_10">Отчество:</span></td>
				<td class="basket-client-input">'.$ur_direktor_o.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	';

	// ------- / юр. лицо / -------




	// ======= ВЫВОД СОДЕРЖИМОГО =============================================================

	$summa = 0;

	if (isset($_SESSION['basket'] ))
	{
		// Комментарии для Yandex
		$comments_yandex = 'Оплата за: ';

		foreach($_SESSION['basket'] as $id_b=>$item_arr_md5)
		{
			$id_b = intval($id_b);

			// перебераем по `char_md5` массив с количеством и характеристиками
			foreach($item_arr_md5 as $char_md5=>$item_value)
			{
				if ($id_b > 0)
				{
					// количество
					$kolichestvo = $_SESSION['basket']["$id_b"]["$char_md5"]['kolich'];

					// характеристики
					for($i = 1; $i < 11; $i++)
					{
						$char[$i] = $_SESSION['basket']["$id_b"]["$char_md5"]['char_'.$i];
					}



					// === Вывод товаров =======================================================
					$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$id_b' AND `pub` = '1' LIMIT 1 ") or die ("Невозможно сделать выборку из таблицы - 2");

					$item_result = mysql_num_rows($item_query); // количество товаров

					if ($item_result > 0)
					{
						while($b = mysql_fetch_array($item_query))
						{

							$basket_item_id = $b['id'];
							$basket_section_id = $b['section'];
							$basket_item_title = $b['title'];
							$basket_item_price = $b['price'];

							$items .= $basket_item_id.'; ';

							// Если цена равна 0 то ставим прочерк
							if($basket_item_price == 0)
							{
								$basket_item_price = "-";
								$sum = "-";
							}
							else
							{
								$sum = $kolichestvo * $basket_item_price;
								if(!isset($summa)){$summa = 0;}
								$summa = $summa + $sum;
							}

							// Характеристики
							$kolichestvo = $_SESSION['basket'][$id_b][$char_md5]['kolich'];
							if($_SESSION['basket'][$id_b][$char_md5]['char'])
							{
								$char_out = $_SESSION['basket'][$id_b][$char_md5]['char'];
								$char_out = '<br>'.$char_out;
							}
							else {$char_out = '';}

							// вывод товарной позиции
							$n = '
							<tr>
								<td class="basket-item-title"><div>'.$basket_item_title.'</div>'.$char_out.'</td>
								<td class="basket-item-klv"><span id="kol_'.$idb.'">'.$kolichestvo.'</span></td>
								<td class="basket-item-price"><span class="basketform" ><span id="price_'.$idb.'">'.$basket_item_price.'</span></td>
								<td class="basket-item-summa"><span id="summa_'.$idb.'">Сумма</span></td>
							</tr>
							';

							$basket_middle = $basket_middle.$n;

							$shopping_cart .= $basket_item_title.'   '.$kolichestvo.' x '.$basket_item_price.''.$shopSettings->getValue('currency').' = '.$sum.''.$shopSettings->getValue('currency').'     ';

							$o = '<span>'.$basket_item_title.'&nbsp;&nbsp;&nbsp;&nbsp; '.$kolichestvo.' х '.$basket_item_price.' '.$shopSettings->getValue('currency').' = '.$sum.' '.$shopSettings->getValue('currency').'</span><br>' ;
							$order_middle = $order_middle.$o.$char_out.'<br>';

						}

						$shopping_cart_urlencode = urlencode($shopping_cart);

					} // $item_result > 0
				}
			}
		}
	} // конец проверки существования сессии
	else
	{
		$n = '
		<tr>
			<td>Заказ не может быть оформлен - у вашего браузера отключены сессии. <br/> Попробуйте заказать товар ещё раз с включёнными сессиями.</td>
		</tr>
		';
	}

	// Сессии
	$fio = $_SESSION['shop_basket_fio'];
	$tel = $_SESSION['shop_basket_tel'];

	// Удаляем из номера телефона " ", "+7", "-" и "8", если она идёт первой
	$arr = array(" ", "+7", "-");
	$tel = str_replace($arr,"",$tel);
	if ($tel[0] == "8"){$tel = substr($tel, 1); };

	$email_client = $_SESSION['shop_basket_email'];
	$address = $_SESSION['shop_basket_address'];
	$comments = $_SESSION['shop_basket_comments'];

	$order = $order_middle.'<div><b>Сумма '.$summa.' '.$shopSettings->getValue('currency').'</b></div>';

	// Случайное число
	// $yandex_nm = mt_rand(10000, 99999);

	$yandex_nm_session = intval($_SESSION['shop_basket_nm']);

	if ($yandex_nm_session == 0) // если наш номер заказа не записан в сессии >>> определяем номер заказа
	{
		// Находим id следующей записи заказа
		$yandex_nm_sql = "SHOW TABLE STATUS FROM `".$dbname."` LIKE 'com_shop_orders'";
		$yandex_nm_query = mysql_query($yandex_nm_sql) or die ("Невозможно сделать выборку из таблицы - cso1");
		$yandex_nm = mysql_result($yandex_nm_query , 0, 'Auto_increment');

		// заносим номер в сессию
		$_SESSION['shop_basket_nm'] = $yandex_nm;

		// === Заносим заказ в базу данных ========================================
		// --- Физическое или юридическое лицо ---
		if ($contract == '1')
		{
			if ($lico == 1){$payer = $fiz_lico;}
			if ($lico == 2){$payer = $ur_lico;}
		}
		else
		{
			$payer = '';
		}



		// Вставляем в таблицу "com_shop_orders"
		$query_insert_orders = "INSERT INTO  `com_shop_orders` (`id`, `order`, `items`, `sum`, `status`, `payment_system`, `date_order`, `date_payment`, `fio`, `tel`, `email`, `address`, `comments`, `payer`)
		VALUES (NULL,  '$order', '$items', '$summa',  '0',  'Yandex',  NOW(),  '0000-00-00 00:00:00', '$fio', '$tel', '$email_client', '$address', '$comments', '$payer');";

		$sql_orders = mysql_query($query_insert_orders) or die ("Невозможно обновить данные O-1");

	}
	else // берём номер из сессии
	{
		$yandex_nm = $yandex_nm_session;

		// Находим заказ
		$orderssql = mysql_query("SELECT * FROM `com_shop_orders` WHERE `id` = '$yandex_nm' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 5");

		$resultorders = mysql_num_rows($orderssql);

		// Находим статус заказа
		if ($resultorders > 0)
		{
			while($m = mysql_fetch_array($orderssql)):
				$status = $m['status'];
			endwhile;


			if($status == 0) // заказ не оплачен
			{
				// Обновляем данные
				$query_update_orders = "UPDATE `com_shop_orders` SET `order` = '$order', `items` = '$items', `sum` = '$summa', `payment_system` = 'Yandex', `date_order` = NOW(), `fio` = '$fio', `tel` = '$tel', `email` = '$email_client', `address` = '$address', `comments` = '$comments' WHERE  `id` = '$yandex_nm' AND `status` = '0' LIMIT 1;";
				// echo $query_update_shop;
				$sql_orders = mysql_query($query_update_orders) or die ("Невозможно обновить данные О-2");
			}
			else // заказ оплачен
			{
				// Находим id следующей записи заказа
				$yandex_nm_sql = "SHOW TABLE STATUS FROM `".$dbname."` LIKE 'com_shop_orders'";
				$yandex_nm_query = mysql_query($yandex_nm_sql) or die ("Невозможно сделать выборку из таблицы - cso1");
				$yandex_nm = mysql_result($yandex_nm_query , 0, 'Auto_increment');

				// заносим номер в сессию
				$_SESSION['shop_basket_nm'] = $yandex_nm;

				// Вставляем в таблицу "com_shop_orders"
				$query_insert_orders = "INSERT INTO  `com_shop_orders` (`id`, `order`, `items`, `sum`, `status`, `payment_system`, `date_order`, `date_payment`, `fio`, `tel`, `email`, `address`, `comments`, `payer`)
				VALUES (NULL,  '$order', '$items', '$summa',  '0',  'Yandex',  NOW(),  '0000-00-00 00:00:00', '$fio', '$tel', '$email_client', '$address', '$comments', '$payer');";

				$sql_orders = mysql_query($query_insert_orders) or die ("Невозможно обновить данные O-2");
			}
		}
	}



	// Подключаем шаблон формы для оплаты счета с помощью Yandex
	include($root."/components/shop/frontend/tmp/shop_basket_yandex_tmp.php");


} // конец функции component

?>
