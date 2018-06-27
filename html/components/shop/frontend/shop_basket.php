<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/basket/lang/'.LANG.'.php';

if(!isset($shopSettings))
{
	include_once($root."/components/shop/classes/classShopSettings.php");
	$shopSettings = new classShopSettings();
}

if(isset($_POST["item_id"])){$ii = intval($_POST["item_id"]);} // id

$char_data = '';

if (isset($_POST['char']))
{
	foreach ($_POST['char'] as $char_mame => $char_value)
	{
		$char_data .= $char_mame.': '.$char_value.'<br>';
	}
}

$kolich = 1; // сколько товаров добавить при обращении к странице

// md5 будем использовать как индекс массива - он уникальный для каждого варианта сочетаний значений характеристик
$char_md5 = md5($char_data);


// ======= СЕССИИ ========================================================================


if (isset($_SESSION['basket']))
{
	// Если пришли с POST - запроса - вносим в корзину.
	if(isset($ii) && $ii != 0)
	{
		if (isset($_SESSION['basket']["$ii"]["$char_md5"]['kolich']))
		{
			$klv = $_SESSION['basket']["$ii"]["$char_md5"]['kolich']; // достаём количество
		}
		else {$klv = 0;}

		$klv += $kolich;

		$_SESSION['basket']["$ii"]["$char_md5"]['kolich'] = $klv;

		// заносим характеристики
		$_SESSION['basket']["$ii"]["$char_md5"]['char'] = $char_data;
	}
}
else
{
	$_SESSION['basket'] = array();
	$_SESSION['basket']["$ii"]["$char_md5"]['kolich'] = $kolich; // массив с количеством товаров и характеристиками

	// заносим характеристики
	$_SESSION['basket']["$ii"]["$char_md5"]['char'] = $char_data;
}


// ####### Вывод содержимого #######################################################
function component()
{
	global $root, $site, $ii, $char_md5, $shopSettings;

	// ======= КАЛЬКУЛЯТОР ===================================================================

	echo
	'
		<script type="text/javascript">

		function raschet()
		{
			var summa = 0;
	';
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

						echo
						'
							var kol = document.getElementById("kolred_'.$id_b.'_'.$char_md5.'").value;
							if (kol =="" || kol == 0 || kol == null)
							{
								document.getElementById("kolred_'.$id_b.'_'.$char_md5.'").value = 1;
								kol = 1;
							}
							var price = document.getElementById("price_'.$id_b.'_'.$char_md5.'").innerHTML;
							var sum_str = kol*price;
							if(!isNaN(sum_str))
							{
								var summa = summa + sum_str;
								document.getElementById("summa_'.$id_b.'_'.$char_md5.'").innerHTML = sum_str;
							}
							else
							{
								document.getElementById("summa_'.$id_b.'_'.$char_md5.'").innerHTML = "-";
							}
						';
					}
				}
			}
	echo
	'
			if(isNaN(summa) || summa == 0){ summa = "-"; }
			document.getElementById("summa").innerHTML = summa;
		}

		function del(id,chm)
		{
			alert(id + " " + chm);
		}
		</script>
	';

	// ======= ВЫВОД СОДЕРЖИМОГО =============================================================


	$summa = 0;

	if (isset($_SESSION['basket'] ))
	{
		// перебераем по `id` товара, получаем массив с `char_md5`с массивом количества и характеристик
		foreach($_SESSION['basket'] as $id_b=>$item_arr_md5)
		{
			$id_b = intval($id_b);

			// перебераем по `char_md5` массив с количеством и характеристиками
			foreach($item_arr_md5 as $char_md5 => $value_c5)
			{
				// === Вывод товаров =======================================================
				$item_query = mysql_query("SELECT * FROM com_shop_item WHERE id = ".intval($id_b)." AND pub = 1 ORDER BY ordering ASC") or die ("Невозможно сделать выборку из таблицы - 1");

				$item_result = mysql_num_rows($item_query); // количество товаров

				if ($item_result > 0)
				{
					while($b = mysql_fetch_array($item_query))
					{
						$baske_titem_id = $b['id'];
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

						// Если цена 0 то ставим прочерк
						if($basket_item_price == 0)
						{
							$basket_item_price = '-';
						}

						// количество
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
							<td class="basket-item-title"><a href="/shop/item/'.$baske_titem_id.'/" target="_blank">'.$basket_item_title.'</a>'.$char_out.'</td>
							<td class="basket-item-klv"><input id="kolred_'.$id_b.'_'.$char_md5.'" name="kolred[]" onkeyup="raschet()" class="right" type="text" size="3" value="'.$kolichestvo.'"><input type="hidden" name="item_id[]" value="'.$baske_titem_id.'" ><input type="hidden" name="char_md5[]" value="'.$char_md5.'" ></td>
							<td class="basket-item-price"><span class="basketform" ><span id="price_'.$id_b.'_'.$char_md5.'">'.$basket_item_price.'</span></td>
							<td class="basket-item-summa"><span id="summa_'.$id_b.'_'.$char_md5.'">Сумма</span></td>
							<td class="basket-item-delete"><a href="http://'.$site.'/shop/basket/del/'.$id_b.'/'.$char_md5.'" rel="nofollow"><img align="absmiddle" src="http://'.$site.'/components/shop/frontend/tmp/images/delete.png" border="0" alt=""></a></td>
						</tr>
						';

						if(!isset($basket_middle)){$basket_middle = '';}
						$basket_middle .= $n;

					}
				} // $item_result > 0

			}
		}
	} // конец проверки существования сессии


	// Подключаем шаблон корзины
	include($root."/components/shop/frontend/tmp/shop_basket_tmp.php");

} // конец функции component

?>
