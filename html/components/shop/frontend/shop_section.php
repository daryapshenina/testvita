<?php
defined('AUTH') or die('Restricted access');

if($frontend_edit == 1){$head->addFile('http://'.$domain.'/components/shop/frontend/section_edit.js');}

$section_id = intval($d[2]);

// Если используем ЧПУ страницы получаем как ЧПУ/&page = 777    в нормальном режиме получаем как shop/section/1/777
if(isset($_GET['page']) && $_GET['page'] != ''){$page_nav = intval($_GET['page']);} else{$page_nav = intval($d[3]);}

// ID активного меню
$active_menu = $section_id;

// --- Включённый фильтр, кнопка ---
if(isset($_POST["shop_filter_set"])){$filter_post = strip_tags($_POST["shop_filter_set"]);} else {$filter_post = '';}

// Разбиваем запрос на массив по признаку "="
$qs_arr = preg_split('/[\=]/', $qs);

// получаем значение пременной; адрес страницы = он же индекс ассоциативного массива, смотри .htaccess
if(isset($_GET[$qs_arr[0]])){$filter_get = $_GET[$qs_arr[0]];} else {$filter_get = '';}



// ------- Сброс фильтра -------
if(isset($_POST["shop_filter_reset"]))
{
	unset($_SESSION['shop_filter'][$section_id]);
	unset ($filter_n1_arr);
	unset ($filter_n2_arr);
	unset ($filter_s_arr);
}


// ------- Получаем данные из $_POST или $_SESSION -------
if(!isset($_POST["shop_filter_reset"]))
{
	// String
	if(isset($_REQUEST['char_s']))
	{
		$filter_s_arr = $_REQUEST['char_s'];

		//if(!isset($_SESSION['shop_filter'])){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_s'] = $filter_s_arr;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['char_s'])){$filter_s_arr = $_SESSION['shop_filter'][$section_id]['char_s'];}
	}

	// number 1
	if(isset($_REQUEST['char_n1']))
	{
		$filter_n1_arr = str_replace(',', '.', $_REQUEST['char_n1']);

		if(!isset($_SESSION['shop_filter'])){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_n1'] = $filter_n1_arr;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['char_n1'])){$filter_n1_arr = $_SESSION['shop_filter'][$section_id]['char_n1'];}
	}

	// number 2
	if(isset($_REQUEST['char_n2']))
	{
		$filter_n2_arr = str_replace(',', '.', $_REQUEST['char_n2']);

		if(!isset($_SESSION['shop_filter'])){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_n2'] = $filter_n2_arr;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['char_n2'])){$filter_n2_arr = $_SESSION['shop_filter'][$section_id]['char_n2'];}
	}

}




// --- Загрузка настроек сортировки товаров ---
// Смотрим как выводить

switch($shopSettings->getValue('sorting_items'))
{
	case 0:
		$sorting_sql = 'ordering ASC';
		break;

	case 1:
		$sorting_sql = 'price ASC';
		break;

	case 2:
		$sorting_sql = 'price DESC';
		break;

	case 3:
		$sorting_sql = 'title ASC';
		break;

	case 4:
		$sorting_sql = 'title DESC';
		break;

	case 5:
		$sorting_sql = 'cdate DESC';
		break;
}

// ======= РАЗДЕЛ ==================================================
$stmt_section = $db->prepare('SELECT * FROM com_shop_section WHERE id = :id AND pub = 1 ORDER BY ordering ASC');
$stmt_section->execute(array('id' => $section_id));

// если товаров нет
if ($stmt_section->rowCount() == "0")
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}

while($m = $stmt_section->fetch()):
	$section_id = $m['id'];
	$section_pub = $m['pub'];
	$section_parent = $m['parent'];
	$section_ordering = $m['ordering'];
	$section_title = $m['title'];
	$section_description = $m['description'];
	$tag_title = $m['tag_title'];
	$tag_description = $m['tag_description'];
endwhile;

// Если тег тайтл не заполнен то $page_title + $site_title;
$page_title = $section_title;

// ======= Фильтры ====================================================
$stmt_filter = $db->prepare("
SELECT f.id, f.char_id, f.value_1, f.value_2, n.name, n.unit, n.type
FROM com_shop_filter f
JOIN com_shop_char_name n ON n.id = f.char_id
WHERE f.section_id = :section_id
ORDER BY f.ordering
");

$stmt_filter->execute(array('section_id' => $section_id));

if($stmt_filter->rowCount() > 0)
{
	$filter_sql = '';
	$filter_tr = '';

	$s_arr = array();
	while($m = $stmt_filter->fetch())
	{
		$id = $m['id'];
		$char_id = $m['char_id'];
		$value_1 = $m['value_1'];
		$value_2 = $m['value_2'];
		$name = $m['name'];
		$unit = $m['unit'];
		$type = $m['type'];

		$filter_tr	.= '<tr>';
		$filter_tr	.= '<td class="shop_section_filter_tab_td_1">'.$name.'</td>';

		if($type == 'number')
		{
			if(isset($filter_n1_arr[$char_id])) {$value_1 = $filter_n1_arr[$char_id];}
			if(isset($filter_n2_arr[$char_id])) {$value_2 = $filter_n2_arr[$char_id];}

			$filter_tr	.= '<td class="shop_section_filter_tab_td_2">от</td>';
			$filter_tr	.= '<td class="shop_section_filter_tab_td_3"><input type="text" name="char_n1['.$char_id.']" value="'.$value_1.'" class="shop_section_filter_input"> до <input type="text" name="char_n2['.$char_id.']" value="'.$value_2.'" class="shop_section_filter_input"> '.$unit.'</td>';
		}

		if($type == 'string')
		{		
			$filter_tr	.= '<td class="shop_section_filter_tab_td_2">&nbsp;</td>';

			$var_string_arr = explode(';', $value_1);
			$count = count($var_string_arr);


			$option = "<option value=\"\">Выбрать</option>\n";
			if ($count > 0)
			{
				for ($i = 0; $i < $count; $i++)
				{
					if(isset($filter_s_arr[$char_id]) && $filter_s_arr[$char_id] == $var_string_arr[$i])
					{
						$selected = 'selected';
					}
					else
					{
						$selected = '';
					}

					$option .= "<option $selected>".$var_string_arr[$i]."</option>\n";
				}
			}

			$filter_tr	.= '<td class="shop_section_filter_tab_td_3"><select class="shop_section_filter_select" name="char_s['.$char_id.']">'.$option.'</select></td>';
		}

		$filter_tr	.= '</tr>';
	}


// ======= Цены =========================================================================================

	$action_link = 'http://'.$domain.'/shop/section/'.$section_id;
	$filter_session_price_ot = '';
	$filter_session_price_do = '';
	$filter_arrow_up ='';
	$filter_arrow_down = '';

	// Если кнопка сброса не была нажата то принимаем из post данные
	if(!isset($_POST["shop_filter_reset"]))
	{
		// Добавляем полученные цены от и до из post и пишем в сессию
		if(isset($_REQUEST['filter_price_ot']) || isset($_REQUEST['filter_price_do']))
		{
			$filter_session_price_ot = intval(str_replace('-', '', $_REQUEST['filter_price_ot']));
			$filter_session_price_do = intval(str_replace('-', '', $_REQUEST['filter_price_do']));

			$_SESSION['shop_filter'][$section_id]['ot']['price'] = $filter_session_price_ot;
			$_SESSION['shop_filter'][$section_id]['do']['price'] = $filter_session_price_do;
		}
		else
		{
			if(isset($_SESSION['shop_filter'][$section_id]['ot']['price'])) {$filter_session_price_ot = $_SESSION['shop_filter'][$section_id]['ot']['price'];} else {$filter_session_price_ot = '';}
			if(isset($_SESSION['shop_filter'][$section_id]['do']['price'])) {$filter_session_price_do = $_SESSION['shop_filter'][$section_id]['do']['price'];} else {$filter_session_price_do = '';}
		}

		// Обрабатываем кнопки сортировки по цене
		if(isset($filter_post) && $filter_post != '')
		{
			$filter_price_sorting = $filter_post;
			$_SESSION['shop_filter'][$section_id]['filter_price_sorting_value'] = $filter_price_sorting;
		}
		else
		{
			if(isset($_SESSION['shop_filter'][$section_id]['filter_price_sorting_value'])){$filter_price_sorting = $_SESSION['shop_filter'][$section_id]['filter_price_sorting_value'];} else {$filter_price_sorting = '';}
		}

		if($filter_price_sorting === '▲')
		{

			$filter_arrow_up = ' shop_section_filter_container_sorting_arrow_active';
			$sorting_sql = 'price ASC';
		}
		elseif($filter_price_sorting === '▼')
		{
			$filter_arrow_down = ' shop_section_filter_container_sorting_arrow_active';
			$sorting_sql = 'price DESC';
		}

		// Если пусты то 0
		if(!isset($filter_session_price_ot)){$filter_session_price_ot = 0;}
		if(!isset($filter_session_price_do)){$filter_session_price_do = 0;}

		// Проверяем что бы "от" не было больше чем "до"
		if($filter_session_price_ot >= $filter_session_price_do){$filter_session_price_do = 0;}

		// Добавляем запрос в бд
		if($filter_session_price_ot > 0)
		{
			$filter_sql .= ' AND `price` >=  "'.$filter_session_price_ot.'"';
		}
		if($filter_session_price_do > 0)
		{
			$filter_sql .= ' AND `price` <=  "'.$filter_session_price_do.'"';
		}
		else
		{
			$filter_session_price_do = NULL;
		}

		// если есть в массиве ЧПУ - заменяем
		if(isset($url_arr['shop/section/'.$section_id]) && $url_arr['shop/section/'.$section_id] != '')
		{
			$action_link = 'http://'.$domain.'/'.$url_arr['shop/section/'.$section_id];
		}
	}


	$filter_out = '
	<form id="filter_form_main" method="post" action="'.$action_link.'">
	<div class="shop_section_filter_container">
		<table class="shop_section_filter_tab">
		'.$filter_tr.'
		<tr>
			<td class="shop_section_filter_tab_td_1">Цена</td>
			<td class="shop_section_filter_tab_td_2">от</td>
			<td class="shop_section_filter_tab_td_3"><input type="text" name="filter_price_ot" value="'.$filter_session_price_ot.'" size="5" class="shop_section_filter_input"> до <input type="text" name="filter_price_do" value="'.$filter_session_price_do.'" size="5" class="shop_section_filter_input"> руб.</td></tr>
		<tr>
			<td class="shop_section_filter_tab_td_1" colspan="2"></td>
			<td class="shop_section_filter_tab_td_2"><input type="submit" value="Искать" id="shop_filter_set" name="shop_filter_set"><input type="submit" value="Сбросить фильтр" id="shop_filter_reset" name="shop_filter_reset"></td>
		</tr>
		</table>
		<div class="shop_section_filter_container_sorting">
			Сортировать по цене: <input type="submit" value="&#9650;" name="shop_filter_set" class="shop_section_filter_container_sorting_arrow'.$filter_arrow_up.'" title="по возрастанию"> <input type="submit" value="&#9660;" name="shop_filter_set" class="shop_section_filter_container_sorting_arrow'.$filter_arrow_down.'" title="по убыванию">
		</div>
	</div>
	</form>';
} // если есть фильтры
else
{
	$filter_out = '';
}


// ####### Функция вывода ##########################################################
function component()
{
	global $root, $site, $domain, $db, $url_arr, $menu, $section_id, $section_pub, $section_parent, $section_ordering, $section_title, $section_description, $filter_out, $page_nav, $quantity, $section_sub_sql, $sorting_sql, $section_description_out, $filter_s_arr,  $filter_n1_arr, $filter_n2_arr, $filter_sql, $shopSettings, $frontend_edit;

	$menu_id = $menu->getActiveId();
	

	// Запускаем рекурсию по разделам
	echo'<div class="shop_section_column">';

	section_tree($menu_id);

	echo'</div>';
	echo'<div>&nbsp;</div>';

	// Выводим фильтры
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_shop_section_filter" data-id="'.$section_id.'">'.$filter_out.'</div>';}
	else {echo $filter_out;}
	// --- / Находим `menu_id` для нашего `id_com` ---


	$pq = ($page_nav-1)*$shopSettings->getValue('quantity');
	if ($pq < 0){$pq = 0;}

	// если выводить подразделы
	if ($shopSettings->getValue('output_un_section') == 1)
	{
		section_items($menu_id, 0);
	}
	else
	{
		$section_sub_sql = "";
	}



	// === Вывод товаров =======================================================
	// Фильтр по характеристикм типа string
	if(isset($filter_s_arr))
	{
		foreach ($filter_s_arr as $char_id => $value_s)
		{
			$value_s = trim(htmlspecialchars(strip_tags($value_s)));
			if ($value_s != '')
			{
				 $filter_sql .= " 
				 AND id IN (
					SELECT item_id
					FROM com_shop_char
					WHERE name_id = '".intval($char_id)."' 
					AND value = '".$value_s."'
				)
				 ";
			}
		}
	}

	// Фильтр по характеристикм типа number
	if(isset($filter_n1_arr) || isset($filter_n2_arr))
	{
		foreach ($filter_n1_arr as $char_id => $value_n1)
		{
			$value_n1 = (float)$value_n1;
			$value_n2 = (float)$filter_n2_arr[$char_id];

			if ($value_n2 > 0){$sql_n2 = " AND value <= '".$value_n2."'";} else {$sql_n2 = '';}

			// ищем только в том случае, если хотя бы одно поле заполнено
			if ($value_n1 != 0 || $value_n2 != 0)
			{
				 $filter_sql .= " 
				 AND id IN (
					SELECT item_id
					FROM com_shop_char
					WHERE name_id = '".intval($char_id)."' 
					AND value >= '".$value_n1."' ".$sql_n2."
				 )
				 ";
			}
		}
	}

	if($shopSettings->getValue('grouping') == 1){$grouping_sql = ' GROUP BY group_identifier ';}else{$grouping_sql = '';}
	
	$stmt_item = $db->prepare("
	SELECT id, title, intro_text, price, price_old, currency, quantity, photo, photo_big, new, discount,
	CASE currency
	WHEN '0' THEN price
	WHEN '1' THEN price * ".CCurrency::getUSD()."
	WHEN '2' THEN price * ".CCurrency::getEUR()."
	END as price,
	CASE currency
	WHEN '0' THEN price_old
	WHEN '1' THEN price_old * ".CCurrency::getUSD()."
	WHEN '2' THEN price_old * ".CCurrency::getEUR()."
	END as price_old
	FROM com_shop_item
	WHERE (section = :section_id ".$section_sub_sql.") AND pub = '1' ".$filter_sql."
	".$grouping_sql."	
	ORDER BY ".$sorting_sql."
	LIMIT ".$pq.", ".$shopSettings->getValue('quantity')." 	
	");
	
	$stmt_item->execute(array('section_id' => $section_id));

	if($stmt_item->rowCount() > 0)
	{
		$items_out = '';
		while($m = $stmt_item->fetch())
		{
			$item_id = $m['id'];
			$item_title = $m['title'];
			$item_introtext = $m['intro_text'];
			$item_price = $m['price'];
			$item_price_old = $m['price_old'];
			$item_currency = $m['currency'];
			$item_quantity = $m['quantity'];
			$item_photo_small = $m['photo'];
			$item_photo_big = $m['photo_big'];
			$item_new = $m['new'];
			$item_discount = $m['discount'];


			$item_title = preg_replace("/\\\/", "<br>", $item_title);

			/* - Перенесли в SQL
			switch($item_currency)
			{
				case CURRENCY_USD:
				{
					//$item_price = CCurrency::usdToRub($item_price);
					$item_price_old = CCurrency::usdToRub($item_price_old);
				} break;

				case CURRENCY_EUR:
				{
					//$item_price = CCurrency::eurToRub($item_price);
					$item_price_old = CCurrency::eurToRub($item_price_old);
				} break;
			}
			*/
			
			$item_price = number_format($item_price, 0, '', ' ');
			$item_price_old = number_format($item_price_old, 0, '', ' ');

			if(!is_file($root."/components/shop/photo/".$item_photo_small))
			{
				$item_photo_small_out = '<div class="no-photo" style="width:'.$shopSettings->getValue('x_small').'px;height:'.$shopSettings->getValue('y_small').'px;"></div>';
			}
			else
			{
				$item_photo_small_out =
				'
				<img border="0" alt="'.$item_title.'" src="http://'.$domain.'/components/shop/photo/'.$item_photo_small.'" id="shop_item_img_'.$item_id.'" />
				';
			}

			if($shopSettings->getValue('mapping') == 6)
			{
				// Выводим характеристики для плоского дизайна
				$stmt_char = $db->prepare(
				"SELECT c.id, c.item_id, c.name_id, c.value, n.unit, n.name, n.type
				FROM com_shop_char c
				JOIN com_shop_char_name n ON n.id = c.name_id
				WHERE c.item_id = :item_id ORDER BY c.ordering"
				);
				
				$stmt_char->execute(array('item_id' => $item_id));

				$item_arr = array();

				while($m = $stmt_char->fetch())
				{
					$id = $m['id'];
					$item_id = $m['item_id'];
					$name_id = $m['name_id'];
					$name = $m['name'];
					$unit = $m['unit'];
					$type = $m['type'];
					$value = $m['value'];

					// Заносим всё в массив
					$item_arr[$name]['item_id'] = $item_id;
					$item_arr[$name]['unit'] = $unit;
					$item_arr[$name]['type'] = $type;
					$item_arr[$name]['value'][] = $value;
				}

				$item_char = '';
				$item_char_out = '';
				$c = 1; // считает характеристики
				// перебираем массив по названиям характеристик
				foreach ($item_arr as $name => $char)
				{
					if(count($char['value']) > 1)
					{
						$value_out = '';
						$i = 1;
						// перебираем характеристику
						foreach ($char['value'] as $c_value)
						{
							// ставим запятую между значениями характеристик и пропускаем в конце
							if(count($char['value']) > $i)
							{
								if($c_value != ''){$value_out .= $c_value.", ";}
							}
							else
							{
								$value_out .= $c_value;
							}

							$i++;
						}
					}
					else
					{
						$value_out = '';
						if($char['value'][0] != ''){$value_out .= $char['value'][0];}
					}

					if ($c < 4) // ограничение числа выводимых характеристик
					{
						if($char['unit'] == ''){$unit_out = '';} else {$unit_out = ' '.$char['unit'];}
						if($value_out != '')
						{
							$item_char .= '<span style="margin-right:20px;">'.$value_out.$unit_out.'</span>';
							$c++;
						}
					}
					else {break;}
				}


				$item_char_out .= '<div class="section_item_cell_char" style="width:'.$shopSettings->getValue('x_small').'px">';
				$item_char_out .= $item_char;
				$item_char_out .= '</div>';

			}
			else
			{
			}


			switch($shopSettings->getValue('mapping'))
			{
				case 1: // вывод блоками
					include($root."/components/shop/frontend/tmp/section_item_block_tmp.php");
					break;

				case 2: // вывод ячейками
					include($root."/components/shop/frontend/tmp/section_item_cell_tmp.php");
					break;

				case 3: // вывод карточками
					include($root."/components/shop/frontend/tmp/section_item_card_tmp.php");
					break;

				case 4: // вывод ячейками старый стиль
					include($root."/components/shop/frontend/tmp/section_item_cell_old_tmp.php");
					break;

				case 5: // вывод ячейками с всплывающими фотографиями show2
					include($root."/components/shop/frontend/tmp/section_item_cell_show2_tmp.php");
					break;

				case 6: // вывод плоскими ячейками
					include($root."/components/shop/frontend/tmp/section_item_cell_flat_tmp.php");
					break;
					
				case 7: // вывод товаров
					include($root."/components/shop/frontend/tmp/section_7_tmp.php");
					break;					
				
				case 999: // вывод плоскими ячейками
					include($root."/components/shop/frontend/tmp/section_999_tmp.php");
					break;					
			}

		}


		// выравниваем по центру
		$items_out = '
		<div style="text-align:center;">
			<table border="0" cellpadding="0" style="border-collapse:collaps;width:100%;">
				<tr>
					<td>'.$items_out.'</td>
				</tr>
			</table>
		</div>
		';
	} // $resulttov > 0
	
	
		// ----- НАВИГАЦИЯ -----
	// определяем общее количество товаров
	$stmt_pn = $db->prepare("SELECT id FROM com_shop_item WHERE (section = :section_id ".$section_sub_sql.") AND pub = '1' ".$filter_sql." ");
	$stmt_pn->execute(array('section_id' => $section_id));

	$kol_page_nav = ceil($stmt_pn->rowCount()/$shopSettings->getValue('quantity')); // количество страниц навигации = количество товаров / товаров на страницу - округляем в большую сторону

	$nav_out = '';
	
	if ($kol_page_nav > 1) // если количество страниц > 1 - выводим навигацию
	{
		$nav_out = '<br/>
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
				$nav_out .= '<div class="navpage-active">'.$i.'</div>';
			}
			else
			{
				// если есть в массиве ЧПУ - заменяем
				if(isset($url_arr['shop/section/'.$section_id]) && $url_arr['shop/section/'.$section_id] != '')
				{
					if($i > 1){$pn = '/&page='.$i;} else{$pn = '';}

					$shop_section_url = $url_arr['shop/section/'.$section_id].$pn;
				}
				else
				{
					// для первой страницы убираем параметр навигации = 0
					if ($i == 1 ){$nav_link = "";} else{$nav_link = '/'.$i;}

					$shop_section_url = 'shop/section/'.$section_id.$nav_link;
				}

				$nav_out .= '<div class="navpage"><a href="http://'.$domain.'/'.$shop_section_url.'">'.$i.'</a></div>';
			}
		}

			$nav_out .= '</div>
				  </td>
			</tr>
		</table>
		</div>';
	}
	// ----- / навигация -----

	// Подключаем шаблон раздела
	include($root."/components/shop/frontend/tmp/section_tmp.php");


} // конец функции component



// ###################################################################################
// Функция рекурсии подразделов. Считаем количество товаров.

$section_level = 0;

function section_tree($parent_id)
{
	global $db, $section_name_arr, $section_num_arr, $domain, $section_level, $number_shop_sum, $url_arr;

	// инкремент уровня
	$section_level++;

	// Получаем все подразделы текущего раздела
	$stmt_menu = $db->prepare("
	SELECT m.id, m.name, m.id_com 
	FROM menu m
	JOIN com_shop_item i
	ON i.section = m.id_com
	WHERE m.component = 'shop' 
	AND m.p1 = 'section' 
	AND m.parent = :parent 
	AND m.pub = '1' 
	ORDER BY m.ordering
	");

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
			$shop_ss_sql = "SELECT * FROM `com_shop_item` WHERE `section` = '$menu_id_com' AND `pub` = 1";
			$shop_ss_query = mysql_query($shop_ss_sql) or die ("Невозможно сделать выборку из таблицы - s_tree 2");
			$number_shop_ss = mysql_num_rows($shop_ss_query);

			// Уровень = 1
			if($section_level == 1)
			{
				$menu_name = preg_replace('/ /', '&nbsp', $menu_name);

				// если есть в массиве ЧПУ - заменяем
				if(isset($url_arr['shop/section/'.$menu_id_com]) && $url_arr['shop/section/'.$menu_id_com] != '')
				{
					$shop_section_url = $url_arr['shop/section/'.$menu_id_com];
				}
				else
				{
					$shop_section_url = 'shop/section/'.$menu_id_com;
				}

				// выводим название раздела до входа в рекурсию для 1 уровня
				echo '<div><b><a href="http://'.$domain.'/'.$shop_section_url.'">'.$menu_name.'</a></b>';
			}

			// увеличиваем сумму
			$number_shop_sum = $number_shop_sum + $number_shop_ss;

			section_tree($menu_id);

			// декремент уровня
			$section_level--;

			if($section_level == 1)
			{
				// Выводим сумму после выхода из рекурсии на 1 уровень.
				echo '<span class="shop_sections_number">('.$number_shop_sum.')</span> </div>';
				$number_shop_sum = 0;
			}

		}
	}
}



// ###################################################################################
// Функция рекурсии вывода товаров из подразделов. Выводим товары с учётом фильтров.

// Ищем подразделы если надо
function section_items($menu_id, $lvl)
{
	global $section_sub_sql, $section_id, $filter_sql;

	if ($lvl == 0)
	{
		$section_sub_sql = "";
	}

	// Запрашиваем подразделы =========================================
	$pod_section_query = mysql_query("SELECT * FROM `menu` WHERE `parent` = '$menu_id' AND `p1` = 'section' AND `pub` = '1'") or die ("Невозможно сделать выборку из таблицы - 6");

	// Смотрим существует ли подраздел
	$pod_section = mysql_num_rows($pod_section_query);

	if ($pod_section > 0)
	{
		// Получаем id_com для вывода товаров и id для следующих разделов
		while($s = mysql_fetch_array($pod_section_query))
		{
			$sec_id = $s['id'];
			$sec_id_com = $s['id_com'];

			$section_sub_sql .= " OR `section` = '$sec_id_com'";

			$lvl++;
			section_items($sec_id, $lvl);
		}
	}
}

?>
