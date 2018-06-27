<?php
defined('AUTH') or die('Restricted access');
// $GET = квази $_GET

include_once __DIR__.'/lang/'.LANG.'.php';

// Авторизация пользователя
include_once($root."/classes/Auth.php");
$u = Auth::check();

if($frontend_edit == 1){$head->addFile('/components/shop/frontend/section/edit.js');}

include_once($root."/components/shop/classes/classShopItem.php");

if($shopSettings->mapping == 999)
{
	include_once($root."/tmp/shop/section/tmp.php");
	$head->addFile('/tmp/shop/section/style.css');
	if(file_exists($root.'/tmp/shop/section/tmp.js')){$head->addFile('/tmp/shop/section/tmp.js');}
}
else
{
	$head->addFile('/components/shop/frontend/section/tmp/'.$shopSettings->mapping.'/style.css');
	if(file_exists($root.'/components/shop/frontend/section/tmp/'.$shopSettings->mapping.'/tmp.js')){$head->addFile('/components/shop/frontend/section/tmp/'.$shopSettings->mapping.'/tmp.js');}
	include_once($root.'/components/shop/frontend/section/tmp/'.$shopSettings->mapping.'/tmp.php');
}

$section_id = $d[2];

$shopItemComponent = new classShopSectionItem($shopSettings);

// Цены пользователя
if(!empty($u))
{
	$stmt_pu = $db->prepare("SELECT u.price_type_id, t.name FROM com_shop_price_user u JOIN com_shop_price_type t ON t.id = u.price_type_id  WHERE user_id = :user_id LIMIT 1");
	$stmt_pu->execute(array('user_id' => $u));
	$p = $stmt_pu->fetch();
	$price_type = '<div class="price_type">Тип цены: '.$p['name'].'</div>';
	$price_type_id = $p['price_type_id'];
	$shopItemComponent->setPriceTypeId($price_type_id);
}
else
{
	$price_type = '';
}

if(isset($_POST['discount']) || isset($GET['discount'])){$filter_discount = 1;} else{$filter_discount = 0;}
if(isset($_POST['new']) || isset($GET['new'])){$filter_new = 1;} else{$filter_new = 0;}
if(isset($_POST['hit']) || isset($GET['hit'])){$filter_hit = 1;} else{$filter_hit = 0;}

// Ипользуем $GET = квази $_GET
if(isset($GET['page']) && $GET['page'] != ''){$page_nav = intval($GET['page']);} else{$page_nav = intval($d[3]);}

// ID активного меню
$active_menu = intval($section_id);

// --- Включённый фильтр, кнопка ---
if(isset($_POST["shop_filter_set"])){$filter_post = strip_tags($_POST["shop_filter_set"]);} else {$filter_post = '';}

// Разбиваем запрос на массив по признаку "="
// $qs_arr = preg_split('/[\=]/', $qs);

// получаем значение пременной; адрес страницы = он же индекс ассоциативного массива, смотри .htaccess
// if(isset($_GET[$qs_arr[0]])){$filter_get = $_GET[$qs_arr[0]];} else {$filter_get = '';}

// ------- Сброс фильтра -------
if($_SERVER['REQUEST_METHOD'] == 'POST') // Сбросить фильтр
{
	unset($_SESSION['shop_filter'][$section_id]);
	unset($filter_n1_arr);
	unset($filter_n2_arr);
	unset($filter_s_arr);
}


// ------- Получаем данные из $_POST или $_SESSION -------
if(!isset($_POST["shop_filter_reset"]))
{
	// Скидки
	if(isset($_POST['discount']) || isset($GET['discount']))
	{
		if(!isset($_SESSION)){session_start();}
		$_SESSION['shop_filter'][$section_id]['discount'] = 1;
	} 
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['discount'])){$filter_discount = $_SESSION['shop_filter'][$section_id]['discount'];}
		else{$filter_discount = 0;}
	}


	// Новинка
	if(isset($_POST['new']) || isset($GET['new']))
	{
		$filter_new = 1;
		if(!isset($_SESSION)){session_start();}
		$_SESSION['shop_filter'][$section_id]['new'] = 1;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['new'])){$filter_new = $_SESSION['shop_filter'][$section_id]['new'];}
		else{$filter_new = 0;}
	}


	// Hit
	if(isset($_POST['hit']) || isset($GET['hit']))
	{
		$filter_hit = 1;		
		if(!isset($_SESSION)){session_start();}
		$_SESSION['shop_filter'][$section_id]['hit'] = 1;
	} 
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['hit'])){$filter_hit = $_SESSION['shop_filter'][$section_id]['hit'];}		
		else{$filter_hit = 0;}
	}


	// String
	if(isset($_POST['char_s'])) // получен $_POST
	{
		$filter_s_arr = $_POST['char_s'];
		if(!isset($_SESSION)){session_start();}
		$_SESSION['shop_filter'][$section_id]['char_s'] = $filter_s_arr;
	}
	elseif(isset($GET['char_s'])) // получен $GET
	{
		$filter_s_arr = $GET['char_s'];
		if(!isset($_SESSION)){session_start();}
		$_SESSION['shop_filter'][$section_id]['char_s'] = $filter_s_arr;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['char_s'])){$filter_s_arr = $_SESSION['shop_filter'][$section_id]['char_s'];}
	}

	// number 1
	if(isset($_POST['char_n1'])) // получен $_POST
	{
		$filter_n1_arr = str_replace(',', '.', $_POST['char_n1']);

		if(!isset($_SESSION)){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_n1'] = $filter_n1_arr;
	}
	elseif(isset($GET['char_n1'])) // получен $GET
	{
		$filter_n1_arr = str_replace(',', '.', $GET['char_n1']);

		if(!isset($_SESSION)){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_n1'] = $filter_n1_arr;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['char_n1'])){$filter_n1_arr = $_SESSION['shop_filter'][$section_id]['char_n1'];}
	}

	// number 2
	if(isset($_POST['char_n2'])) // получен $_POST
	{
		$filter_n2_arr = str_replace(',', '.', $_POST['char_n2']);

		if(!isset($_SESSION)){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_n2'] = $filter_n2_arr;
	}
	elseif(isset($GET['char_n2'])) // получен $GET
	{
		$filter_n2_arr = str_replace(',', '.', $GET['char_n2']);

		if(!isset($_SESSION)){session_start();}

		$_SESSION['shop_filter'][$section_id]['char_n2'] = $filter_n2_arr;
	}
	else
	{
		if(isset($_SESSION['shop_filter'][$section_id]['char_n2'])){$filter_n2_arr = $_SESSION['shop_filter'][$section_id]['char_n2'];}
	}
}




if($d[1] == "all") // Для вывода всех разделов
{
	// если есть в массиве ЧПУ - заменяем
	$action_link = '/shop/all/1';

	if(!empty($url_arr['shop/all/1']))
	{
		$action_link = '/'.$url_arr['shop/all/1'];
	}
}
else // Для отдельных разделов
{
	// если есть в массиве ЧПУ - заменяем
	$action_link = '/shop/section/'.$section_id;

	if(!empty($url_arr['shop/section/'.$section_id]))
	{
		$action_link = '/'.$url_arr['shop/section/'.$section_id];
	}
}

// Если получен запрос POST - перезапрашиваем страницу методом GET, а значения фильтров у нас уже в сессии
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	Header ("Location: ".$action_link);
}


// --- Загрузка настроек сортировки товаров ---
// Смотрим как выводить

switch($shopSettings->sorting_items)
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
if($d[1] !== "all")
{
	$stmt_section = $db->prepare('SELECT * FROM com_shop_section WHERE id = :id AND pub = 1 ORDER BY ordering ASC LIMIT 1');
	$stmt_section->execute(array('id' => $section_id));

	// если раздела нет
	if ($stmt_section->rowCount() == "0")
	{
		// выдаём страницу ошибки 404.html
		header("HTTP/1.0 404 Not Found");
		include("404.php");
		exit;
	}

	$section = $stmt_section->fetch();
}
else // Вывести все разделы ИМ.
{
	$section['title'] = $shopSettings->shop_title;
	$section['tag_title'] = $shopSettings->tag_title;
	$section['tag_description'] = $shopSettings->tag_description;
	$section['description'] = '';
	$section_id = 0;
}


// SEO
if($section['tag_title'] == '')
{
	$tag_title = $section['title']." - ".Settings::instance()->getValue('Наименование сайта');
}
else
{
	$tag_title = $section['tag_title'];
}

if($section['tag_description'] == '')
{
	$tag_description = Settings::instance()->getValue('Описание сайта');
}
else
{
	$tag_description = $section['tag_description'];
}


if($page_nav > 1)
{
	if($section['tag_title'] == ''){$tag_title = $section['title']." - ".Settings::instance()->getValue('Наименование сайта').", страница ".$page_nav;}
	else{$tag_title .= ", страница ".$page_nav;}

	if($section['tag_description'] == ''){$tag_description = Settings::instance()->getValue('Описание сайта').", страница ".$page_nav;}
	else{$tag_description .= ", страница ".$page_nav;}
}



// ======= Фильтры ====================================================
$filter_out = '';

if($shopSettings->section_filters == 1) // Показать фильтры над разделом
{
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

				if($value_1 == 0){$value_1 = '';}
				if($value_2 == 0){$value_2 = '';}

				$filter_tr	.= '<td class="shop_section_filter_tab_td_2">'.LANG_SHOP_SECTION_FROM.'</td>';
				$filter_tr	.= '<td class="shop_section_filter_tab_td_3"><input type="text" name="char_n1['.$char_id.']" value="'.$value_1.'" class="shop_section_filter_input"> '.LANG_SHOP_SECTION_TO.' <input type="text" name="char_n2['.$char_id.']" value="'.$value_2.'" class="shop_section_filter_input"> '.$unit.'</td>';
			}

			if($type == 'string')
			{
				$filter_tr	.= '<td class="shop_section_filter_tab_td_2">&nbsp;</td>';

				$var_string_arr = explode(';', $value_1);
				$count = count($var_string_arr);


				$option = '<option value="">'.LANG_SHOP_SECTION_CHOOSE.'</option>\n';
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

		if($filter_discount == 1)
		{
			$filter_tr .= '<tr><td class="shop_section_filter_tab_td_2">'.LANG_SHOP_SECTION_DISCOUNT.'</td></tr>';
		}
		if($filter_new == 1)
		{
			$filter_tr .= '<tr><td class="shop_section_filter_tab_td_2">'.LANG_SHOP_SECTION_NEW.'</td></tr>';
		}
		if($filter_hit == 1)
		{
			$filter_tr .= '<tr><td class="shop_section_filter_tab_td_2">'.LANG_SHOP_SECTION_HIT.'</td></tr>';
		}

	// ======= Цены =========================================================================================

		$filter_session_price_ot = '';
		$filter_session_price_do = '';
		$filter_arrow_up ='';
		$filter_arrow_down = '';

		// Если кнопка сброса не была нажата то принимаем из post данные
		if(!isset($_POST["shop_filter_reset"]))
		{
			if(isset($_POST['filter_price_ot']) || isset($_POST['filter_price_do'])) // Добавляем полученные цены от и до из $_POST и пишем в сессию
			{
				$filter_session_price_ot = intval(str_replace('-', '', $_POST['filter_price_ot']));
				$filter_session_price_do = intval(str_replace('-', '', $_POST['filter_price_do']));

				$_SESSION['shop_filter'][$section_id]['ot']['price'] = $filter_session_price_ot;
				$_SESSION['shop_filter'][$section_id]['do']['price'] = $filter_session_price_do;
			}
			elseif(isset($GET['filter_price_ot']) || isset($GET['filter_price_do'])) // Добавляем полученные цены от и до из $GET и пишем в сессию
			{
				if(!isset($GET['filter_price_ot'])) $GET['filter_price_ot'] = '';
				if(!isset($GET['filter_price_do'])) $GET['filter_price_do'] = '';
				$filter_session_price_ot = intval(str_replace('-', '', $GET['filter_price_ot']));
				$filter_session_price_do = intval(str_replace('-', '', $GET['filter_price_do']));

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
				$set_sorting_price = 1;
			}
			elseif($filter_price_sorting === '▼')
			{
				$filter_arrow_down = ' shop_section_filter_container_sorting_arrow_active';
				$set_sorting_price = 2;
			}
		}

		// Если пусты то 0
		if(!isset($filter_session_price_ot)){$filter_session_price_ot = 0;}
		if(!isset($filter_session_price_do)){$filter_session_price_do = 0;}

		// Проверяем что бы "от" не было больше чем "до"
		if($filter_session_price_ot >= $filter_session_price_do && $filter_session_price_do != 0){$filter_session_price_do = 0;}

		if($filter_session_price_ot == 0){$filter_session_price_ot_f = '';}
		else{$filter_session_price_ot_f = $filter_session_price_ot;}

		if($filter_session_price_do == 0){$filter_session_price_do_f = '';}
		else{$filter_session_price_do_f = $filter_session_price_do;}

		$filter_out = '
		<form id="filter_form_main" method="post" action="'.$action_link.'">
		<div class="shop_section_filter_container">
			<table class="shop_section_filter_tab">
			'.$filter_tr.'
			<tr>
				<td class="shop_section_filter_tab_td_1">'.LANG_SHOP_SECTION_PRICE.'</td>
				<td class="shop_section_filter_tab_td_2">'.LANG_SHOP_SECTION_FROM.'</td>
				<td class="shop_section_filter_tab_td_3"><input type="text" name="filter_price_ot" value="'.$filter_session_price_ot_f.'" size="5" class="shop_section_filter_input"> '.LANG_SHOP_SECTION_TO.' <input type="text" name="filter_price_do" value="'.$filter_session_price_do_f.'" size="5" class="shop_section_filter_input"> '.$shopSettings->currency.'</td>
			</tr>
			'.

			/*
			'
			<tr>
				<td class="shop_section_filter_tab_td_1" colspan="2">Новинка</td>
				<td class="shop_section_filter_tab_td_2">
					<input name="filter_new" type="checkbox" />
				</td>
			</tr>
			<tr>
				<td class="shop_section_filter_tab_td_1" colspan="2">Скидка</td>
				<td class="shop_section_filter_tab_td_2">
					<input name="filter_new" type="checkbox" />
				</td>
			</tr>
			'
			*/

			'
			<tr>
				<td class="shop_section_filter_tab_td_1" colspan="2"></td>
				<td class="shop_section_filter_tab_td_2"><input type="submit" value="'.LANG_SHOP_SECTION_SEARCH.'" id="shop_filter_set" name="shop_filter_set"><input type="submit" value="'.LANG_SHOP_SECTION_RESET.'" id="shop_filter_reset" name="shop_filter_reset"></td>
			</tr>
			</table>
			<div class="shop_section_filter_container_sorting">
				'.LANG_SHOP_SECTION_SORT_IN_PRICE.': <input type="submit" value="&#9650;" name="shop_filter_set" class="shop_section_filter_container_sorting_arrow'.$filter_arrow_up.'" title="'.LANG_SHOP_SECTION_ASCENDING.'"> <input type="submit" value="&#9660;" name="shop_filter_set" class="shop_section_filter_container_sorting_arrow'.$filter_arrow_down.'" title="'.LANG_SHOP_SECTION_DESCENDING.'">
			</div>
		</div>
		</form>';
	} // если есть фильтры
}


// ####### Функция вывода ##########################################################
function component()
{
	global $root, $SITE, $d, $u, $price_type, $price_type_id, $domain, $db, $url_arr, $section_tree_arr, $menu, $section, $section_id, $shopItemComponent, $filter_discount, $filter_s_arr, $filter_n1_arr, $filter_n2_arr, $filter_session_price_ot, $filter_session_price_do, $set_sorting_price, $kol_page_nav, $filter_out, $filter_new, $page_nav, $shopSettings, $frontend_edit;

	$menu_id = $menu->getActiveId();

	if($shopSettings->output_un_section == 1 || $shopSettings->sub_sections == 1)
	{
		$section_tree_out = section_tree($menu_id);

		// Показать подразделы
		if($shopSettings->sub_sections == 1)
		{
			// Запускаем рекурсию по разделам
			echo'<div class="shop_section_column">'.$section_tree_out.'</div>';
			echo'<div>&nbsp;</div>';
		}

		if ($shopSettings->output_un_section == 1) // если выводить подразделы
		{
			if(isset($section_tree_arr))
			{
				foreach($section_tree_arr as $s_id)
				{
					$shopItemComponent->addSection($s_id);
				}
			}
		}			
	}



	// Выводим фильтры
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_shop_section_filter" data-id="'.$section_id.'">'.$filter_out.'</div>';}
	else {echo $filter_out;}
	// --- / Находим `menu_id` для нашего `id_com` ---

	$pq = ($page_nav-1) * $shopSettings->quantity;
	if ($pq < 0){$pq = 0;}

	if($d[1] != "all")
	{
		$shopItemComponent->addSection($section_id); // из какого раздела выводить
	}
	else
	{
		$shopItemComponent->setAllCategoryIfNotAdd(true);
	}

	// 0 - вывести все 1 - вывести только товары со скидкой 2 - вывести только новые товары 3 - выводить и новые товары и\или со скидкой
	if($filter_discount == '1' && $filter_new == '0')
	{
		$shopItemComponent->setMode(1);
	}
	else if($filter_discount == '0' && $filter_new == '1')
	{
		$shopItemComponent->setMode(2);
	}
	else if($filter_discount == '1' && $filter_new == '1')
	{
		$shopItemComponent->setMode(3);
	}

	$shopItemComponent->setStart($pq); // устанавливаем с какого товара выводить
	$shopItemComponent->setQuantity($shopSettings->quantity); // устанавливаем количество выводимых товаров
	$shopItemComponent->setViewItemWithoutImage(true); // устанавливаем, что выводить все товары, даже без изображений
	$shopItemComponent->setTypeOut(0); // вывести для компонента

	// Фильтр по характеристикм типа string
	if(isset($filter_s_arr))
	{
		$shopItemComponent->setFilterString($filter_s_arr);
	}

	// Фильтр по характеристикм типа number
	if(isset($filter_n1_arr) || isset($filter_n2_arr))
	{
		$shopItemComponent->setFilterNumber($filter_n1_arr, $filter_n2_arr);
	}

	// Фильтр по цене
	$shopItemComponent->setPrice($filter_session_price_ot, $filter_session_price_do);

	// Сортировка по цене
	if($set_sorting_price == 1 || $set_sorting_price == 2){$shopItemComponent->setSorting($set_sorting_price);}

	// flex
	$items_out = '<div class="flex_row items_main">'.$shopItemComponent->viewItems($price_type_id).'</div>';

	$kol_page_nav = ceil($shopItemComponent->getItemSum()/$shopSettings->quantity); // количество страниц навигации = количество товаров / товаров на страницу - округляем в большую сторону

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
			if($i > 1){$pn = '?page='.$i;} else{$pn = '';}

			if ($i == $page_nav)
			{
				$nav_out .= '<div class="navpage-active">'.$i.'</div>';
			}
			else
			{
				// если есть в массиве ЧПУ - заменяем
				if(isset($url_arr['shop/section/'.$section_id]) && $url_arr['shop/section/'.$section_id] != '')
				{
					if($d[1] == 'all'){$shop_section_url = $url_arr['shop/all/1'].$pn;}
					else {$shop_section_url = $url_arr['shop/section/'.$section_id].$pn;}
				}
				else
				{
					if($d[1] == 'all'){$shop_section_url = 'shop/all/1'.$pn;}
					else{$shop_section_url = 'shop/section/'.$section_id.$pn;}
				}

				$nav_out .= '<div class="navpage"><a href="/'.$shop_section_url.'">'.$i.'</a></div>';
			}
		}

			$nav_out .= '</div>
				  </td>
			</tr>
		</table>
		</div>';
	}
	else{$nav_out = '';}
	// ----- / навигация -----

	// Подключаем шаблон раздела
	include($root."/components/shop/frontend/section/tmp/tmp.php");


} // конец функции component



// ###################################################################################
// Функция рекурсии вывода товаров из подразделов. Выводим товары с учётом фильтров.

// Ищем подразделы если надо
function section_tree($menu_id, $lvl = 0)
{
	global $db, $domain, $url_arr, $section_tree_arr, $section_tree_items_sum;
	$lvl++;

	if($menu_id != '')
	{
		$stmt_menu = $db->prepare("SELECT id, id_com, name FROM menu WHERE parent = :menu_id AND p1 = 'section' AND pub = '1' ORDER BY ordering");
		$stmt_menu->execute(array('menu_id' => $menu_id));

		if($stmt_menu->rowCount() > 0)
		{
			// Получаем id_com для вывода товаров и id для следующих разделов
			while($m = $stmt_menu->fetch())
			{
				$section_tree_arr[] = $m['id_com'];

				// Получаем все товары текущего раздела
				$stmt_items = $db->prepare("SELECT id FROM com_shop_item WHERE section = :section_id AND pub ='1'");
				$stmt_items->execute(array('section_id' => $m['id_com']));

				$items_count = $stmt_items->rowCount();

				$section_tree_items_sum += $items_count;

				section_tree($m['id'], $lvl);

				if($lvl == 1)
				{
					// $menu_name = preg_replace('/ /', '&nbsp', $m['name']);

					// если есть в массиве ЧПУ - заменяем
					if(isset($url_arr['shop/section/'.$m['id_com']]) && $url_arr['shop/section/'.$m['id_com']] != '')
					{
						$shop_section_url = $url_arr['shop/section/'.$m['id_com']];
					}
					else
					{
						$shop_section_url = 'shop/section/'.$m['id_com'];
					}

					// выводим название раздела до входа в рекурсию для 1 уровня
					if(!isset($section_tree_out)){$section_tree_out = '';}
					$section_tree_out .= '<div><a href="/'.$shop_section_url.'">'.$m['name'].'</a>';
					if($section_tree_items_sum > 0){$section_tree_out .= '<span class="shop_sections_number">('.$section_tree_items_sum.')</span>';}
					$section_tree_out .= '</div>';
					$section_tree_items_sum = 0; // обнуляем счётчик для след. разделов
				}
			}
		}
	}

	$lvl--;
	if(isset($section_tree_arr) && isset($section_tree_out))
	{
		return $section_tree_out;
	}
}




function section_item_char($item_id)
{
	global $db, $shopSettings;

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


	$item_char_out .= '<div class="section_item_cell_char" style="width:'.$shopSettings->x_small.'px">';
	$item_char_out .= $item_char;
	$item_char_out .= '</div>';


	return $item_char_out;
}

?>
