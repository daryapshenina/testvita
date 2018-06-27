<?php
// DAN 2010
defined('AUTH') or die('Restricted access');

$qiwi_tel_to = $_POST["to"];
$qiwi_com = $_POST["com"];
$qiwi_txn_id = $_POST["txn_id"];

// ####### Вывод содержимого #######################################################
function component()
{
	global $root, $paymethod_out, $site, $email, $ii, $kolich, $fio, $tel, $email_client, $address, $comments, $summa, $ip, $qiwi_tel_to, $qiwi_com, $qiwi_txn_id;

	// ======= ВЫВОД СОДЕРЖИМОГО =============================================================

	$summa = 0;

	if (isset($_SESSION['basket'] ))
	{
		foreach($_SESSION['basket'] as $idb=>$kolichestvo)
		{
			$idb = intval($idb);

			// === Вывод товаров =======================================================
			$tovarbasketsql = mysql_query("SELECT * FROM com_shop_item WHERE id = $idb AND pub = 1 ORDER BY ordering ASC") or die ("Невозможно сделать выборку из таблицы - 1");

			$resulttov = mysql_num_rows($tovarbasketsql); // количество товаров

			if ($resulttov > 0)
				{

				while($b = mysql_fetch_array($tovarbasketsql)):
					$baske_titem_id = $b['id'];
					$basket_item_title = $b['title'];
					$basket_item_price = $b['price'];

					$sum = $kolichestvo * $basket_item_price;

					$summa = $summa + $sum;

				// вывод товарной позиции
				$n = '
				<tr>
					<td class="basket-item-title">'.$basket_item_title.'</td>
					<td class="basket-item-klv"><span id="kol_'.$idb.'">'.$kolichestvo.'</span></td>
					<td class="basket-item-price"><span class="basketform" ><span id="price_'.$idb.'">'.$basket_item_price.'</span></td>
					<td class="basket-item-summa"><span id="summa_'.$idb.'">'.$sum.'</span></td>
				</tr>
				';

				$basket_middle = $basket_middle.$n;

				$m = '
				<tr>
					<td>'.$basket_item_title.'</td>
					<td><span id="kol_'.$idb.'">'.$kolichestvo.'</span></td>
					<td><span class="basketform" ><span id="price_'.$idb.'">'.$basket_item_price.'</span></td>
					<td><span id="summa_'.$idb.'">'.$sum.'</span></td>
				</tr>
				';

				$basket_middle_mail = $basket_middle_mail.$m;

				endwhile;
				} // $resulttov > 0
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
	$email = $_SESSION['shop_basket_email'];
	$address = $_SESSION['shop_basket_address'];
	$comments = $_SESSION['shop_basket_comments'];


	// Подключаем шаблон корзины
	include($root."/components/shop/frontend/tmp/shop_basket_qiwierr_tmp.php");


} // конец функции component

?>
