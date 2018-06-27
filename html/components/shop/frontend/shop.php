<?php
// выводит разделы интернет - магазина
defined('AUTH') or die('Restricted access');

$section_id = intval($d[2]);

// ======= Вывод подразделов ==================================================
// --- Находим `menu_id` для нашего `$section_id` ---
$stmt_select = $db->query("SELECT id, pub FROM menu WHERE component = 'shop' AND p1 = 'all' AND main = '1' LIMIT 1");
$menu = $stmt_select->fetch();

$menu_id = $menu['id'];

// если товаров нет
if ($menu['pub'] != 1)
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}

// ####### Функция вывода ##########################################################
function component()
{
	global $root, $site, $section_id, $shopSettings, $menu_id;

	echo'
	<h1 class="title">'.$shopSettings->shop_title.'</h1>
	<div>&nbsp;</div>
	<div>'.$shopSettings->shop_description.'</div>
	';

	// Запускаем рекурсию по разделам
	section_tree($menu_id);

} // конец функции component


// ####################################################################
// ####### ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ #######

function section_tree($parent_id)
{
	global $section_name_arr, $section_num_arr, $site, $section_level, $number_shop_sum;

	// инкремент уровня
	$section_level++;

	// Получаем все подразделы текущего раздела
	$stmt_menu = $db->prepare("SELECT id, name, id_com FROM menu WHERE component = 'shop' AND p1 = 'section' AND parent = :parent AND pub = '1'");
	$stmt_menu->execute(array('parent' => $parent_id));

	// Если разделы есть то начинаем вывод подпунктов и создаем вызовы этой функции для получения следующих подпунктов
	if($stmt_menu->rowCount() > 0)
	{
		while($s = $stmt_menu->fetch())
		{
			$menu_id = $s['id'];
			$menu_name = $s['name'];
			$menu_id_com = $s['id_com'];

			// У текущего раздела смотрим кол-во товаров
			$stmt_item = $db->prepare("SELECT id FROM com_shop_item WHERE section = :section AND pub = 1");
			$stmt_item->execute(array('section' => $menu_id_com));
			
			$number_shop_ss = $stmt_item->rowCount();

			// echo $menu_name." (".$number_shop_ss.') '.$number_shop_sum.' '.$section_level.'<br />';

			$menu_name = preg_replace('/ /', '&nbsp', $menu_name);

			// отступ слева у пункта меню
			$otstup = str_repeat("&nbsp;-&nbsp;",($section_level -1));

			// выводим название раздела до входа в рекурсию для 1 уровня
			echo '<div>'.$otstup.'<a href="/shop/section/'.$menu_id_com.'">'.$menu_name.'</a><span class="shop_sections_number">('.$number_shop_ss.')</span> </div>';

			section_tree($menu_id);

			// декремент уровня
			$section_level--;
		}
	}
}

?>
