<?php
// DAN обновление - февраль 2014
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/basket/lang/'.LANG.'.php';

// === Магические кавычки - если магические кавычки не включены - добавляем кавычки через функцию
if (!get_magic_quotes_gpc()) {
  $_GET = addslashes_array($_GET);
  $_POST = addslashes_array($_POST);
  $_COOKIE = addslashes_array($_COOKIE);
}

$ii = $_POST['item_id'];
$fio = $_POST['fio'];
$tel = $_POST['tel'];
$email_client = $_POST['email'];
$address = $_POST['address'];
$comments = $_POST['comments'];

if(strlen($tel) < 3)
{
	$err_mail = 1;
	$err_mail_out .= LANG_FIELD_PHONE_ERROR;
}

// ======= ЗАНОСИМ ДАННЫЕ В СЕССИЮ =================================================
// Если получаем данные из POST >>> заносим данные в сессию иначе берём данные из сессии
if( isset($_POST['fio'])){$_SESSION['shop_basket_fio'] = $fio;} else {$fio = $_SESSION['shop_basket_fio'];}
if( isset($_POST['tel'])){$_SESSION['shop_basket_tel'] = $tel;} else {$tel = $_SESSION['shop_basket_tel'];}
if( isset($_POST['email'])){$_SESSION['shop_basket_email'] = $email_client;} else {$email_client = $_SESSION['shop_basket_email'];}
if( isset($_POST['address'])){$_SESSION['shop_basket_address'] = $address;} else {$address = $_SESSION['shop_basket_address'];}
if( isset($_POST['comments'])){$_SESSION['shop_basket_comments'] = $comments;} else {$comments = $_SESSION['shop_basket_comments'];}
// ======= / заносим данные в сессию / =============================================

// ####### Вывод содержимого #######################################################
function component()
{
	global $root, $paymethod, $paymethod_out, $site, $email, $shopSettings, $ii, $kolich, $fio, $tel, $email_client, $address, $comments, $summa, $ip;

	// массив месяцев
	$mes[1] = LANG_JANUARY;
	$mes[2] = LANG_FEBRUARY;
	$mes[3] = LANG_MARCH;
	$mes[4] = LANG_APRIL;
	$mes[5] = LANG_MAY;
	$mes[6] = LANG_JUNE;
	$mes[7] = LANG_JULY;
	$mes[8] = LANG_AUGUST;
	$mes[9] = LANG_SEPTEMBER;
	$mes[10] = LANG_OCTOBER;
	$mes[11] = LANG_NOVEMBER;
	$mes[12] = LANG_DECEMBER;

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



	// ======= ВЫВОД СОДЕРЖИМОГО =============================================================

	$summa = 0;

	if (isset($_SESSION['basket']))
	{
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
							$basket_item_currency = $b['currency'];

							switch($basket_item_currency)
							{
								case CURRENCY_USD:
								{
									$basket_item_price = CCurrency::usdToRub($basket_item_price);
								} break;

								case CURRENCY_EUR:
								{
									$basket_item_price = CCurrency::eurToRub($basket_item_price);
								} break;
							}

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
								<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;"><div><a target="_blank" href="http://'.$site.'/shop/item/'.$id_b.'">'.$basket_item_title.'</a></div>'.$char_out.'</td>
								<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle; text-align:center;"><span id="kol_'.$id_b.'">'.$kolichestvo.'</span></td>
								<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle; text-align:center;"><span id="price_'.$id_b.'">'.$basket_item_price.'</span></td>
								<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle; text-align:center;"><span id="summa_'.$id_b.'">'.$sum.'</span></td>
							</tr>
							';

							$basket_middle = $basket_middle.$n;

							$o = '<span>'.$basket_item_title.'&nbsp;&nbsp;&nbsp;&nbsp; '.$kolichestvo.' х '.$basket_item_price.' '.$shopSettings->getValue('currency').' = '.$sum.' '.$shopSettings->getValue('currency').'</span><br>' ;
							$order_middle = $order_middle.$o.$char_out.'<br>';

						}
					} // $item_result > 0
				}
			}
		}
	} // конец проверки существования сессии
	else
	{
		$err_mail = 1;
		$err_mail_out .= LANG_BASKET_CLEANED;
	}

	// Подключаем шаблон корзины
	include($root."/components/shop/frontend/tmp/shop_basket_mail_tmp.php");


	if($err_mail != 1)
	{
		// === Отправка на почту ==================================================

		$data = date( d.'.'.m.'.'.Y );

		$ip=GetUserIP();

		$to1 = $email_client;

		// SUBJECT тема
		$subject = "Заявка с сайта www.".$site." ";

		$site_code = '=?UTF-8?B?'.base64_encode($site).'?=';

		/* Для отправки HTML-почты Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8 \r\n";
		$headers .= "From: www.".$site_code." <".$shopSettings->getValue('email')."> \r\n";

		/* сообщение */
		$message = $basket_mail_tmp;

		// = MAIL =
		mail($to1, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);

		// делаем паузу 3 секунды и отправляем ещё одно письмо

		$message = $message.'<p>IP '.$ip.'</p>';

		sleep (1);
		mail($shopSettings->getValue('email'), '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);



		// === Заносим заказ в базу данных ========================================

		$order = $order_middle.'<div><b>'.LANG_SUM.' '.$summa.' '.$shopSettings->getValue('currency').'</b></div>';

		// --- Физическое или юридическое лицо ---
		if ($shopSettings->getValue('contract') == '1')
		{
			if ($lico == 1){$payer = $fiz_lico;}
			if ($lico == 2){$payer = $ur_lico;}
		}
		else
		{
			$payer = '';
		}


		// --- Метод оплаты ---
		// Наличными при получении:
		if ($paymethod == 1)
		{
			$paymethod_order = LANG_PAY_CASH;
		}

		// Наложенным платежём
		if ($paymethod == 2)
		{
			$paymethod_order = LANG_PAY_IMPOSED;
		}

		// Предоплата
		if ($paymethod == 5)
		{
			$paymethod_order = LANG_PREPAYMENT;
		}

		$summa = intval($summa);

		// Вставляем в таблицу "com_shop_orders"
		$query_insert_orders = "INSERT INTO  `com_shop_orders` (`id`, `order`, `items`, `sum`, `status`, `payment_system`, `date_order`, `date_payment`, `fio`, `tel`, `email`, `address`, `comments`, `payer`)
		VALUES (NULL ,  '$order',  '$items', '$summa',  '0',  '$paymethod_order',  NOW(),  '0000-00-00 00:00:00', '$fio', '$tel', '$email_client', '$address', '$comments', '$payer');";

		$sql_orders = mysql_query($query_insert_orders) or die ("Невозможно обновить данные O-1");

		// Чистим корзину и удаляем номер заказа
		unset($_SESSION['basket']);
		unset($_SESSION['shop_basket_nm']);

	}

} // конец функции component

?>
