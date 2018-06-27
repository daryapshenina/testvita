<?php
// DAN 2013
// выводит содержимое товара на отдельной странице.
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

$item_id = intval($d[2]);
$order_id = intval($d[3]);
$url_key = $d[4];

// ======= Проверка существования товара =======================================================
$tovarsql = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$item_id' AND `pub` = '1' AND `etext_enabled` = '1' ORDER BY ordering ASC") or die ("Невозможно сделать выборку из таблицы - 1");

$resulttov = mysql_num_rows($tovarsql); // количество товаров

while($m = mysql_fetch_array($tovarsql)):
	$item_id = $m['id'];
	$item_pub = $m['pub'];
	$item_title = $m['title'];
	$item_etext_enabled = $m['etext_enabled'];
	$item_etext = $m['etext'];
	$tag_title = $m['tag_title'];
	$tag_description = $m['tag_description'];
endwhile;

// Если тег тайтл не заполнен то $page_title + $site_title;
$page_title = $item_title;

// если тег тайтл не заполнен - заполняем автоматически
if ($tag_description == ""){$tag_description = $item_title.'. '.LANG_SHOP_ITEM_PRICE.': '.$item_price.' '.$shopSettings->currency.' '.$item_introtext;}



// ------- ВЫЧИСЛЯЕМ КЛЮЧ -------
	// Находим заказ
	$orderssql = mysql_query("SELECT * FROM `com_shop_orders` WHERE  `id` = '$order_id' LIMIT 1 ") or die ("Невозможно сделать выборку из таблицы - 1");

	$resultorders = mysql_num_rows($orderssql);

	while($m = mysql_fetch_array($orderssql)):
		$order_date_payment = $m['date_payment'];
	endwhile;

	$order_key = sha1('dan'.$order_id.$order_date_payment);

	if ($order_key != $url_key){$resulttov = 0;}
// ------- / вычисляем ключ / -------


// если товаров нет
if ($resulttov == "0")
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}


// ####### Вывод товара ###############################################################
function component()
{
	global $site, $item_id, $item_pub, $item_etext_enabled, $item_etext, $item_title;

	// Подключаем шаблон товара
	include("components/shop/frontend/tmp/shop_eitem_tmp.php");

} // конец функции component

?>
