<?php
// выводит раздел архива статей
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

if($frontend_edit == 1){$head->addFile('/components/article/frontend/section_edit.js');}

$section_id = intval($d[2]);
$sorting = $d[3];
$page_nav = intval($d[4]);

if (!isset($page_nav) || $page_nav == ""){$page_nav = 0;}

// ID активного меню
$active_menu = $section_id;

// ======= ПРОВЕРКА СУЩЕСТВОВАНИЯ РАЗДЕЛА ========================================
$section_sql = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$section_id' AND `pub` = '1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

$resultsecitem = mysql_num_rows($section_sql); // количество разделов

// если разделов нет
if ($resultsecitem == "0")
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}
// ======= / проверка существования раздела ========================================

while($m = mysql_fetch_array($section_sql)):
	$section_id = $m['id'];
	$section_pub = $m['pub'];
	$section_parent = $m['parent'];
	$section_ordering = $m['ordering'];
	$section_title = $m['title'];
	$section_description = $m['description'];
	$tag_title = $m['tag_title'];
	$tag_description = $m['tag_description'];
	$section_display_subsection = $m['display_subsection'];
	$section_display_sub_item = $m['display_sub_item'];
	$section_sorting = $m['sorting'];
	$section_display_sorting = $m['display_sorting'];
	$section_display_date = $m['display_date'];
	$section_display_vote = $m['display_vote'];
	$section_display_views = $m['display_views'];
	$section_show_details = $m['show_details'];
	$section_title_hyperlink = $m['title_hyperlink'];
	$section_text_output = $m['text_output'];
endwhile;

// Если тег тайтл не заполнен то $page_title + $site_title;
$page_title = $section_title;

// ####### Функция вывода ##########################################################
function component()
{
	global $root, $site, $url_arr, $sorting, $page_nav, $quantity, $section_sql, $section_id,
	$section_pub, $section_parent, $section_ordering, $section_title, $section_description,
	$section_display_subsection, $section_display_sub_item, $section_sorting, $section_display_sorting, $section_display_date,
	$section_display_vote, $section_display_views, $section_display_alphabet, $section_display_order,
	$section_show_details, $section_title_hyperlink, $section_text_output, $frontend_edit;

	// если существует сортировка в адресной строке берём её иначе - оставляем из БД
	if (($sorting == "date")||($sorting == "rating")||($sorting == "views")||($sorting == "alphabet")||($sorting == "order"))
	{
		$section_sorting = $sorting;
	}

	// активный пункт сортировки
	if ($section_sorting == "date"){$sort_active_date = "sort_button_active"; $ordering = "`cdate` DESC";} else {$sort_active_date = '';}
	if ($section_sorting == "rating"){$sort_active_rating = "sort_button_active"; $ordering = "`rating` DESC, `vote_plus` DESC";} else {$sort_active_rating = '';}
	if ($section_sorting == "views"){$sort_active_views = "sort_button_active"; $ordering = "views DESC";} else {$sort_active_views = '';}
	if ($section_sorting == "alphabet"){$sort_active_alphabet = "sort_button_active"; $ordering = "title ASC";} else {$sort_active_alphabet = '';}
	if ($section_sorting == "order"){$sort_active_order = "sort_button_active"; $ordering = "ordering ASC";} else {$sort_active_order = '';}

	// отображение сортировки по дате
	if ($section_display_date == 1)
	{
		$section_display_date_tmp = '<a href="/article/section/'.$section_id.'/date" rel="nofollow" class="sort_button '.$sort_active_date.'">'.LANG_ARTICLE_DATE.'</а>';
	}

	// отображение сортировки по рейтингу (по количеством голосов за и против)
	if ($section_display_vote == 1)
	{
		$section_display_rating_tmp = '<a href="/article/section/'.$section_id.'/rating" rel="nofollow" class="sort_button '.$sort_active_rating.'">'.LANG_ARTICLE_RATED.'</a>';
	}

	// отображение сортировки по количеству просмотров
	if ($section_display_views == 1)
	{
		$section_display_views_tmp = '<a href="/article/section/'.$section_id.'/views" rel="nofollow" class="sort_button '.$sort_active_views.'">'.LANG_ARTICLE_VIEWS.'</a>';
	}

	// отображение сортировки по алфавиту
	$section_display_alphabet_tmp = '<a href="/article/section/'.$section_id.'/alphabet" rel="nofollow" class="sort_button '.$sort_active_alphabet.'">'.LANG_ARTICLE_ALPHABET.'</a>';

	// отображение сортировки по порядку
	$section_display_order_tmp = '<a href="/article/section/'.$section_id.'/order" rel="nofollow" class="sort_button '.$sort_active_order.'">'.LANG_ARTICLE_ORDER.'</a>';

	// отображение элементов сортировки
	if ($section_display_sorting == 1)
	{
		$section_display_sorting_tmp = '<div class="article_sort_panel"><span class="sort_button">'.LANG_ARTICLE_SORT_BY.': </span>'.$section_display_date_tmp.$section_display_rating_tmp.$section_display_views_tmp.$section_display_alphabet_tmp.$section_display_order_tmp.'</div>';
	}
	else
	{
		$section_display_sorting_tmp = '';
	}



	// ======= Вывод подразделов ==================================================
	// если включено отображение подразделов
	if ($section_display_subsection == 1)
	{

		// --- Находим `menu_id` для нашего `$section_id` ---
		$section_menu_sql = "SELECT * FROM `menu` WHERE `component` = 'article' AND `p1` = 'section' AND `id_com` = '$section_id' LIMIT 1";

		$section_menu_query = mysql_query($section_menu_sql) or die ("Невозможно сделать выборку из таблицы - 2");

		while($s = mysql_fetch_array($section_menu_query)):
			$menu_id = $s['id'];
			$menu_type = $s['menu_type'];
			if ($menu_type == "top") {$menu_t = 1;}
			if ($menu_type == "left") {$menu_t = 2;}
		endwhile;
		// --- / Находим `menu_id` для нашего `id_com` ---



		// ----- Находим подразделы -----
		$section_parent_sql = "SELECT * FROM `menu` WHERE `parent` = '$menu_id' ORDER BY `ordering` ASC";

		$section_parent_query = mysql_query($section_parent_sql) or die ("Невозможно сделать выборку из таблицы - 3");

		// количество подразделов
		$result_sp = mysql_num_rows($section_parent_query);

		$display_subsection_sql = '';

		$article_subsection = '';

		// если подразделы существуют
		if ( $result_sp > 0)
		{
			while($sp = mysql_fetch_array($section_parent_query)):

				$section_parent_id = $sp['id_com'];

				// --- Выводим подразделы ---
				$section_pp_sql = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$section_parent_id' AND `pub` = '1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 4");


				// если стоит отображать содержимое подразделов
				if($section_display_sub_item == 1){$display_subsection_sql	.= " OR `section` = '$section_parent_id' ";}

				while($sp = mysql_fetch_array($section_pp_sql)):

					$subsection_id = $sp['id'];
					$subsection_t = $sp['title'];

					// -- находим количество статей в категории
					$article_ss_sql = "SELECT * FROM `com_article_item` WHERE `section` = '$subsection_id' AND `pub` = 1";

					$article_ss_query = mysql_query($article_ss_sql) or die ("Невозможно сделать выборку из таблицы - 2");

					// количество статей
					$number_articles_ss = mysql_num_rows($article_ss_query);
					// -- / находим количество статей в категории

					// Вывод подразделов
					$article_subsection .= '
						<div class="article_sections_list">
							<b><a href="/article/section/'.$subsection_id.'">'.$subsection_t.'</a></b><span class="article_sections_number">('.$number_articles_ss.')</span>
						</div>
					';

				endwhile;
				// --- / выводим подразделы / ---

			endwhile;
		} // / если подразделы существуют
	} // / если включено отображение подразделов
	// ----- / Находим подразделы -----
	// ======= / Вывод подразделов ================================================



	$pq = ($page_nav-1)*$quantity;
	if ($pq < 0){$pq = 0;}

	// ======= Вывод статей =======================================================
	$article_sql = "SELECT * FROM `com_article_item` WHERE `section` = '$section_id' ".$display_subsection_sql." AND `pub` = 1 ORDER BY $ordering LIMIT $pq,$quantity";

	// echo "<p>$article_sql</p>";

	$article_query = mysql_query($article_sql) or die ("Невозможно сделать выборку из таблицы - 5");

	$resulttov = mysql_num_rows($article_query); // количество статей

	$article_items = '';

	if ($resulttov > 0)
	{
		while($m = mysql_fetch_array($article_query)):
			$section_article_id = $m['id'];
			$section_article_pub = $m['pub'];
			$section_article_ordering = $m['ordering'];
			$section_article_title = $m['title'];
			$section_article_introtext = $m['introtext'];
			$section_article_fulltext = $m['fulltext'];
			$section_article_views = $m['views'];
			$section_article_rating = $m['rating'];
			$section_article_vote_plus = $m['vote_plus'];
			$section_article_vote_minus = $m['vote_minus'];
			$section_article_cdate = $m['cdate'];
			$section_article_lastip = $m['lastip'];

			$section_article_cdate_d = substr($section_article_cdate, 0, 10);

			$cdate = explode("-",$section_article_cdate_d);
			$cd['01'] = LANG_ARTICLE_JANUARY;
			$cd['02'] = LANG_ARTICLE_FEBRUARY;
			$cd['03'] = LANG_ARTICLE_MARCH;
			$cd['04'] = LANG_ARTICLE_APRIL;
			$cd['05'] = LANG_ARTICLE_MAY;
			$cd['06'] = LANG_ARTICLE_JUNE;
			$cd['07'] = LANG_ARTICLE_JULY;
			$cd['08'] = LANG_ARTICLE_AUGUST;
			$cd['09'] = LANG_ARTICLE_SEPTEMBER;
			$cd['10'] = LANG_ARTICLE_OCTOBER;
			$cd['11'] = LANG_ARTICLE_NOVEMBER;
			$cd['12'] = LANG_ARTICLE_DECEMBER;

			// если существуют голоса, только тогда назначаем рейтинг
			if ($section_article_vote_plus > 0 || $section_article_vote_minus > 0)
			{
				$section_vb_plus = $section_article_rating;
				$section_vb_minus = 100 - $section_article_rating;
			}
			else
			{
				$section_vb_plus = 0;
				$section_vb_minus = 0;
			}

			// if ($section_article_introtext != ""){$section_article_introtext = $section_article_introtext."<div>&nbsp;</div>";}

			// свойства - отображение сортировки по рейтингу (по количеством голосов за и против)
			if ($section_display_vote == 1)
			{
				$prop_rating = '<div class="article_prop" title="'.LANG_ARTICLE_RATING_DESCRIPTION.'" ><img border="0" src="/components/article/frontend/tmp/images/za.png" /></div><div class="article_prop article_prop_rating" title="'.LANG_ARTICLE_RATING_DESCRIPTION_2.'" >'.$section_article_rating.'%</div><div class="article_prop" title="'.LANG_ARTICLE_RATING_DESCRIPTION_3.'" >'.$section_article_vote_plus.'&nbsp;&nbsp;</div><div class="article_prop_votingbar article_vb" title="'.LANG_ARTICLE_RATING_DESCRIPTION_4.'" ><div class="article_vb_plus article_fl" style="width: '.$section_vb_plus.'%"></div><div class="article_vb_minus article_fr" style="width: '.$section_vb_minus.'%"></div></div><div class="article_prop" title="'.LANG_ARTICLE_RATING_DESCRIPTION_5.'" >&nbsp;&nbsp;'.$section_article_vote_minus.' </div>';
			}

			// свойства - отображение сортировки по количеству просмотров
			if ($section_display_views == 1)
			{
				$prop_views = '<div class="article_prop article_prop_views" title="Просмотров"><img border="0" src="/components/article/frontend/tmp/images/view_small.png" /></div><div class="article_prop" title="'.LANG_ARTICLE_VIEWS_2.'">'.$section_article_views.'</div>';
			}

			// свойства - отображение сортировки по дате
			if ($section_display_date == 1)
			{
				$prop_date = '<div class="article_prop article_prop_date" title="'.LANG_ARTICLE_DATE_OF_CREATION.'">'.intval($cdate[2]).' '.$cd[$cdate[1]].' '.$cdate[0].'</div>';
			}

			//------- отображение свойств -------
			$prop = '<div class="article_prop_panel">'.$prop_rating.$prop_views.$prop_date.'</div>';
			//------- / отображение свойств -------

			// если есть в массиве ЧПУ - заменяем
			if(isset($url_arr['article/item/'.$section_article_id]) && $url_arr['article/item/'.$section_article_id] != '')
			{
				$article_url = '/'.$url_arr['article/item/'.$section_article_id];
			}
			else
			{
				$article_url = '/article/item/'.$section_article_id;
			}

			// если подключена функция "подробнее"
			if ($section_show_details == 1)
			{
				$show_details = '<div style="margin-top:20px;"><a href="'.$article_url.'">'.LANG_ARTICLE_READ_MORE.'</a></div>';
			}
			else
			{
				$show_details = "";
			}

			// вывод заголовка (с гиперссылкой или без)

			if ($section_title_hyperlink == 1)
			{
				$article_title = '<a href="'.$article_url.'">'.$section_article_title.'</a>';
			}
			else
			{
				$article_title = $section_article_title;
			}


			if($frontend_edit == 1){$frontend_edit_out = 'class="edit_mode" data-type="com_article_item" data-id="'.$section_article_id.'"';} else {$frontend_edit_out = '';}

			$article_items .=
			'<div class="article_sat">'.$article_title.'</div>
			<div>'.$section_article_introtext.'</div>
			'.$show_details.' '.$prop.'
			';

		endwhile;
	} // $resulttov > 0

	// ----- НАВИГАЦИЯ -----
	// определяем общее количество статей
	$nav_num_sql = mysql_query("SELECT * FROM `com_article_item` WHERE `section` = '$section_id' ".$display_subsection_sql." AND `pub` = 1") or die ("Невозможно сделать выборку из таблицы - 6");
	$result_nav_num = mysql_num_rows($nav_num_sql);

	$kol_page_nav = ceil($result_nav_num/$quantity); // количество страниц навигации = количество статей / статей на страницу - округляем в большую сторону

	$article_nav = '';

	if ($kol_page_nav > 1) // если колитчество страниц > 1 - выводим навигацию
	{
		$article_nav = '<br/>
		<div align="center">
		<table border="0" cellpadding="0" style="border-collapse: collapse">
			<tr>
				<td>
				<div class="navbg"><div class="navpage-str">'.LANG_ARTICLE_PAGES.':</div>
		';

		if ($page_nav < 1){$page_nav = 1;}
		for ($i = 1; $i <= $kol_page_nav; $i++)
		{
			if ($i == $page_nav)
			{
				$article_nav .= '<div class="navpage-active">'.$i.'</div>';
			}
			else
			{
				// для первой страницы убираем параметр навигации = 0
				$nav_link = $i;

				if ($i == 1 ){$nav_link = "";} else{$nav_link = '/'.$nav_link;}
				$nav_link = '/'.$section_sorting.$nav_link;

				$article_nav .= '<div class="navpage"><a href="/article/section/'.$section_id.$nav_link.'">'.$i.'</a></div>';
			}

		}
			$article_nav .= '</div>
				  </td>
			</tr>
		</table>
		</div>
		<br/>'
		;

	}

	// ----- / навигация -----

	// Подключаем шаблон вывода заголовка раздела и фильтров
	include($root."/components/article/frontend/tmp/section_tmp.php");

} // конец функции component

?>