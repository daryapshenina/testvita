<?php
define("AUTH", TRUE);
include("../../../config.php");

session_start();

$root = $_SERVER['DOCUMENT_ROOT'];

// Получаем id товара
$shop_item_id = $_POST['shop_item_id'];

// Получаем кол-во товаров
$shop_item_num = intval($_POST['shop_item_num']);

// === MySQL ======================================================
$conn = mysql_connect ($host, $user, $passwd) or die ("Соединение с MySQL не установлено!");
mysql_select_db($dbname) OR die ("Соединение с базой данных не установлено");
mysql_query('SET CHARACTER SET utf8');

// md5 будем использовать как индекс массива - он уникальный для каждого варианта сочетаний значений характеристик
$char_md5 = md5('');
// сколько товаров добавить при обращении к странице
$kolich = $shop_item_num;


if (isset($_SESSION['basket']))
{
	$klv = $_SESSION['basket']["$shop_item_id"]["$char_md5"]['kolich']; // достаём количество
	$klv = $klv + $kolich;

	$_SESSION['basket']["$shop_item_id"]["$char_md5"]['kolich'] = $klv;

	// заносим характеристики
	$_SESSION['basket']["$ii"]["$char_md5"]['char_1'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_2'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_3'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_4'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_5'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_6'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_7'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_8'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_9'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_10'] = '';
}
else
{
	$_SESSION['basket'] = array();
	$_SESSION['basket']["$shop_item_id"]["$char_md5"]['kolich'] = $kolich; // массив с количеством товаров и характеристиками

	// заносим характеристики
	$_SESSION['basket']["$ii"]["$char_md5"]['char_1'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_2'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_3'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_4'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_5'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_6'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_7'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_8'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_9'] = '';
	$_SESSION['basket']["$ii"]["$char_md5"]['char_10'] = '';
}

// ===== Смотрим подключен ли модуль корзины и выводиться ли она =======================
if($root.'/modules/cart/frontend/main.php')
{
	include_once('../../../modules/cart/frontend/main.php');
	echo cart_view();
}

?>
