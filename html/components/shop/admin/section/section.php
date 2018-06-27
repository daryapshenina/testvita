<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/lib/currency.php');

$head->addFile('/components/shop/admin/section/section.css');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_shop_section";
		var contextmenu_shop_section = [
			["admin/com/shop/item/add", "contextmenu_add", "Добавить товар"],
			["admin/com/shop/item/copy", "contextmenu_copy", "Копировать товар"],			
			["admin/com/shop/item/up", "contextmenu_up", "Вверх"],
			["admin/com/shop/item/down", "contextmenu_down", "Вниз"],
			["admin/com/shop/item/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/shop/item/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/shop/item/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_shop_section);
	});
</script>
');


$section_id = intval($SITE->d[4]);
$page_nav = intval($SITE->d[5]);

function a_com()
{
	global $db, $section_id, $page_nav;

	$stmt_section = $db->prepare("SELECT title FROM com_shop_section WHERE id = :section_id");
	$stmt_section->execute(array('section_id' => $section_id));

	echo '
	<h1>'.$stmt_section->fetchColumn().'</h1>
	<table class="admin_table_2">
		<tr>
			<td style="width:200px;"><a class="section_add" href="/admin/com/shop/section/add/'.$section_id.'">Добавить раздел</a></td>
			<td style="width:200px;"><a class="item_add" href="/admin/com/shop/item/add/'.$section_id.'">Добавить товар</a></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	';

	tovar($section_id);

} // конец функции a_com


// ####### ФУНКЦИИ ##########################################################################################
// ======= ФУНКЦИЯ ВЫВОДА ТОВАРОВ ===========================================================================

function tovar($section_id) // $i = 0 начальный уровень меню, $lvl - уровень меню
{
	global $db, $shopSettings, $page_nav;

	$pq = ($page_nav-1) * $shopSettings->quantity;
	if ($pq < 0){$pq = 0;}

	echo'<div class="menu_body">';
	
	$stmt_item = $db->prepare("SELECT * FROM com_shop_item WHERE section = :section_id ORDER BY ordering ASC LIMIT ".$pq.",".$shopSettings->quantity);
	$stmt_item->execute(array('section_id' => $section_id));

	if($stmt_item->rowCount() > 0)
		{
		// выводит заголовок
		echo'
			<table class="admin_table even_odd">
				<tr>
					<th style="width:50px;"></th>
					<th>Товары:</th>
					<th style="width:100px;">Цена</th>
					<th style="width:50px;" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</th>
				</tr>
		';

		while($m = $stmt_item->fetch())
		{
			$tovar_id = $m['id'];
			$tovar_pub = $m['pub'];
			$tovar_parent = $m['parent'];
			$tovar_ordering = $m['ordering'];
			$tovar_title = $m['title'];
			$tovar_price = $m['price'];
			$tovar_currency = $m['currency'];
			$tovar_photo = $m['photo'];
			$tovar_photobig = $m['photo_big'];

			switch($tovar_currency)
			{
				case CURRENCY_USD:
				{
					$tovar_currency_text = 'USD';
				} break;

				case CURRENCY_EUR:
				{
					$tovar_currency_text = 'EUR';
				} break;

				default:
				{
					$tovar_currency_text = 'Руб.';
				} break;
			}

			// --- условия публикации ---
			if ($tovar_pub == "1") {
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$classmenu = "menu_pub";
				}
				else {
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$classmenu = "menu_unpub";
				}

			echo'
				<tr>
					<td class="contextmenu_shop_section" data-id="'.$tovar_id.'">'.$tovar_ordering.'</td>
					<td class="contextmenu_shop_section" data-id="'.$tovar_id.'"><a class="sitemenuitem '.$classmenu.'" id="'.$tovar_id.'" name="'.$section_id.'" href="/admin/com/shop/item/edit/'.$tovar_id.'"  title = "выводит товар" >'.$tovar_title.'</a></td>
					<td class="contextmenu_shop_section" data-id="'.$tovar_id.'"><a class="sitemenuitem '.$classmenu.'" href="/admin/com/shop/item/edit/'.$tovar_id.'" >'.$tovar_price.' '.$tovar_currency_text.'</a></td>
					<td class="contextmenu_shop_section" data-id="'.$tovar_id.'">'.$pub_x.'</td>
				</tr>
			';			
		}

		echo'</table></div>';


		// ----- НАВИГАЦИЯ -----
		// определяем общее количество товаров
		$stmt_i = $db->prepare("SELECT id FROM com_shop_item WHERE section = :section_id");
		$stmt_i->execute(array('section_id' => $section_id));

		$result_tov_num = $stmt_i->rowCount();

		$kol_page_nav = ceil($result_tov_num/$shopSettings->quantity); // количество страниц навигации = количество товаров / товаров на страницу - округляем в большую сторону

		if ($kol_page_nav > 1) // если колитчество страниц > 1 - выводим навигацию
		{
			echo '<br/>
			<div align="center">
			<table border="0" cellpadding="0" style="border-collapse: collapse">
				<tr>
					<td>
					<div class="navbg"><div class="navpage-str">Страницы:</div>
			';

			if ($page_nav < 1){$page_nav = 1;}

			for ($i = 1; $i <= $kol_page_nav; $i++)
			{
				if ($i == $page_nav)
				{
					echo '<div class="navpage-active">'.$i.'</div>';
				}
				else
				{
					echo '<div class="navpage"><a href="/admin/com/shop/section/'.$section_id.'/'.$i.'">'.$i.'</a></div>';
				}
			}
				echo '</div>
					  </td>
				</tr>
			</table>
			</div>';

		}
		// ----- / навигация -----
	} // $resulttov > 0
	else {echo '<div style="padding: 10px">Раздел пустой, товары отсутствуют</div>';}


} // конец функции вывода товара


?>
