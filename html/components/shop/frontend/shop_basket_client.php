<?php
// DAN обновление - февраль 2014
// форма ввода данных клиента

include_once __DIR__.'/basket/lang/'.LANG.'.php';

$kolred_arr = $_POST["kolred"];
$id_arr = $_POST["item_id"];
$char_md5_arr = $_POST["char_md5"];

// ======= POST >>> В СЕССИИ ===================================================================
if(isset($id_arr))
{
	foreach($id_arr as $m => $id)
	{
		if(!isset($_SESSION['basket'])){session_start();}

		$id = intval($id);
		$kolich = intval($kolred_arr[$m]);
		$char_md5 = htmlspecialchars($char_md5_arr[$m]);

		// Заносим в сессию
		$_SESSION['basket']["$id"]["$char_md5"]['kolich'] = $kolich;
	}
}

// ####### Вывод содержимого ###############################################################
function component()
{
	global $site, $root, $shopSettings;

	// ======= COOKIES ======================================================================
	if ($shopSettings->getValue('contract') == '1')
	{
		$lico = intval($_COOKIE['shop_lico']);
		if ($lico == 2){$lico_check_2 = 'checked';} else {$lico == 1; $lico_check_1 = 'checked';}

		// ------- Физ. лицо ----------------------------------------------------------------
		$fiz_f = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_f']));
		$fiz_i = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_i']));
		$fiz_o = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_o']));

		$fiz_dr = intval($_COOKIE['shop_fiz_dr']);
		$fiz_dr_selected[$fiz_dr] = 'selected';

		$fiz_mr = intval($_COOKIE['shop_fiz_mr']);
		$fiz_mr_selected[$fiz_mr] = 'selected';

		$fiz_gr = intval($_COOKIE['shop_fiz_gr']);
		$fiz_gr_selected[$fiz_gr] = 'selected';

		$fiz_pasportseries = intval($_COOKIE['shop_fiz_pasportseries']);
		if($fiz_pasportseries == 0){$fiz_pasportseries = '';}
		$fiz_pasportnumber = intval($_COOKIE['shop_fiz_pasportnumber']);
		if($fiz_pasportnumber == 0){$fiz_pasportnumber = '';}

		$fiz_kemvidanpassport = htmlspecialchars(strip_tags($_COOKIE['shop_fiz_kemvidanpassport']));

		$fiz_dv = intval($_COOKIE['shop_fiz_dv']);
		$fiz_dv_selected[$fiz_dv] = 'selected';

		$fiz_mv = intval($_COOKIE['shop_fiz_mv']);
		$fiz_mv_selected[$fiz_mv] = 'selected';

		$fiz_gv = intval($_COOKIE['shop_fiz_gv']);
		$fiz_gv_selected[$fiz_gv] = 'selected';

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
	// ======= / cookies / ===================================================================

	// ======= ВЫВОД СОДЕРЖИМОГО =============================================================

	$kolTovarov = 0;

	if (isset($_SESSION['basket'] ))
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

					// общее количество товаров
					$kolTovarov = $kolTovarov + $kolichestvo;

					// === Вывод товаров =======================================================
					$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$id_b' AND `pub` = '1' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 1");

					$item_result = mysql_num_rows($item_query); // количество товаров

					if ($item_result > 0)
					{
						while($b = mysql_fetch_array($item_query)):

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
							if(isset($_SESSION['basket'][$id_b][$char_md5]['char']))
							{
								$char_out = $_SESSION['basket'][$id_b][$char_md5]['char'];
								$char_out = '<br>'.$char_out;
							}
							else {$char_out = '';}

							// вывод товарной позиции
							$n = '
							<tr>
								<td class="basket-item-title"><div>'.$basket_item_title.'</div>'.$char_out.'</td>
								<td class="basket-item-klv"><span id="kol_'.$id_b.'">'.$kolichestvo.'</span></td>
								<td class="basket-item-price"><span class="basketform" ><span id="price_'.$id_b.'">'.$basket_item_price.'</span></td>
								<td class="basket-item-summa"><span id="summa_'.$id_b.'">'.$sum.'</span></td>
							</tr>
							';

							if(!isset($basket_middle)){$basket_middle = '';}
							$basket_middle .= $n;

						endwhile;
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

	if(isset($_SESSION['shop_basket_fio'])){$fio = $_SESSION['shop_basket_fio'];} else {$fio = '';}
	if(isset($_SESSION['shop_basket_tel'])){$tel = $_SESSION['shop_basket_tel'];} else {$tel = '';}
	if(isset($_SESSION['shop_basket_email'])){$email = $_SESSION['shop_basket_email'];} else{$email = '';}
	if(isset($_SESSION['shop_basket_address'])){$address = $_SESSION['shop_basket_address'];} else{$address = '';}
	if(isset($_SESSION['shop_basket_comments'])){$comments = $_SESSION['shop_basket_comments'];} else{$comments = '';}

	// Подключаем шаблон корзины
	include($root."/components/shop/frontend/tmp/shop_basket_client_tmp.php");

} // конец функции component

?>
