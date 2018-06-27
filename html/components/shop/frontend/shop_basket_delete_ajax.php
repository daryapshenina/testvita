<?php
// DAN обновление - январь 2014
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/basket/lang/'.$LANG.'.php';

$basket_item_id = intval($d[3]);
$char_md5 = htmlspecialchars($d[4]);

// ======= СЕССИИ ========================================================================
if(!isset($_SESSION)){session_start();}

$itog = 0;
$i = 0;
$summa = 0;
$kolMain = 0;

// Смотрим была ли получена сессия
if (isset($_SESSION['basket']))
{
	// удаляем строчку товара из сессии
	unset($_SESSION['basket']["$basket_item_id"]["$char_md5"]);
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

						$sum = $kolichestvo * $basket_item_price;
						$summa = $summa + $sum;
						$kolMain += $kolichestvo;
					}
				} // $item_result > 0
			}
		}
	}

	echo '
	<table id="modcart_button_table">
		<tr>
			<td style="width:10px;">&nbsp;</td>
			<td style="height:20px;">'.LANG_IN_BASKET.' <span id="modcart_button_kolvo">'.$kolMain.'</span> '.LANG_ITEMS.'</td>
			<td style="width:10px;">&nbsp;</td>
			<td rowspan="2" id="modcart_button_img"></td>
			<td style="width:10px;">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>'.LANG_WORTH.' <span id="modcart_button_itog">'.$summa.'</span> руб</td>
			<td>&nbsp;</td>
			<td style="width:10px;">&nbsp;</td>
		</tr>
	</table>
	';
}

exit;

?>
