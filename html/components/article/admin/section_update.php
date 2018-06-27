<?php
// DAN обновлено январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$section_id = intval($_POST["section_id"]);
$section_title = htmlspecialchars($_POST["title"]);
$section_pub = intval($_POST["sectionpub"]);
$menu_name = htmlspecialchars($_POST["menuname"]);
$menu_pub = intval($_POST["menupub"]);
$menu_parent = intval($_POST["parent"]);
$menu_ordering = intval($_POST["menuordering"]);

$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$sef = checkingeditor($_POST["sef"]);

$display_subsection = intval($_POST["display_subsection"]);
$display_sub_item = intval($_POST["display_sub_item"]);

$sorting = htmlspecialchars($_POST["sorting"]);
$display_sorting = intval($_POST["display_sorting"]);
$display_date = intval($_POST["display_date"]);
$display_views = intval($_POST["display_views"]);
$display_vote = intval($_POST["display_vote"]);
$show_details = intval($_POST["show_details"]);
$title_hyperlink = intval($_POST["title_hyperlink"]);
$text_output = intval($_POST["text_output"]);
$comments = intval($_POST["comments"]);

$section_description = $_POST["editor1"];
$bt_save = $_POST["bt_save"]; // кнопка 'Сохранить'
$bt_prim = $_POST["bt_prim"]; // кнопка 'Применить'
$bt_none = $_POST["bt_none"]; // кнопка 'Отменить'

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

if ($comments != 1){$comments = 0;}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){	Header ("Location: /admin/com"); exit;}

if (!isset($section_pub) || $section_pub == ""){$s_pub = "0";} else{$s_pub = "1";} // Условие публикации раздела
if (!isset($menu_pub) || $menu_pub == "" || $section_pub == 0){$m_pub = "0";} else{$m_pub = "1";} // Условие публикации пункта меню

if ($sorting == 'order'){$sort = 'order';}
elseif ($sorting == 'date'){$sort = 'date';}
elseif ($sorting == 'alphabet'){$sort = 'alphabet';}
elseif ($sorting == 'views'){$sort = 'views';}
elseif ($sorting == 'rating'){$sort = 'rating';}
else {$sort = 'order';}

if ($section_title == "" || $section_title == " ")
{
	$err = '<div id="main-top">Поле "Название раздела" не заплонено!</div>';
}
else {
		// находим "id_menu" и тип меню занесенный в базу
		$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `component` = 'article' AND `p1` = 'section' AND `id_com` = '$section_id';") or die ("Невозможно сделать выборку из таблицы - 1");

		while($mq = mysql_fetch_array($id_menu_query )):
			$menu_id = $mq['id'];
			$menu_type_last = $mq['menu_type'];
		endwhile;

	// Обновляем данные в таблице "menu"
		$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name` = '$menu_name', `pub` = '$m_pub', `parent` = '$menu_parent', `ordering` = '$menu_ordering' WHERE `id_com` = '$section_id' AND `component` = 'article' AND `p1` = 'section' LIMIT 1" ;

		$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 2");

	// Обновляем данные в таблице "com_article_section"
		$query_update_section = "UPDATE `com_article_section` SET  `pub` = '$section_pub', `ordering` = '$menu_ordering', `title` = '$section_title', `description` = '$section_description', `tag_title` = '$tag_title', `tag_description` = '$tag_description', `display_subsection` = '$display_subsection', `display_sub_item` = '$display_sub_item', `sorting` = '$sort', `display_sorting` = '$display_sorting',`display_date` = '$display_date', `display_views` = '$display_views', `display_vote` = '$display_vote', `show_details` = '$show_details', `title_hyperlink` = '$title_hyperlink', `text_output` = '$text_output', `comments` = '$comments' WHERE `id` = '$section_id' LIMIT 1" ;

		$sql_section = mysql_query($query_update_section) or die ("Невозможно обновить данные 3");


		// --- Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---
		if ($menu_type != $menu_type_last)
		{
			// обновляем не только все пункты, но и подпункты данного меню
			tree($menu_type, $menu_id, 0);
		}
		// --- / Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---



		// --- ЧПУ URL ----------------------------------------------------------------------------------------
		if(isset($sef))
		{
			if(classValidation::checkReservedWord($sef)) // зарезервированно
			{
				$sef_err = 1;
			}
			else
			{
				// проверяем на символы
				if (!preg_match("/^[a-z0-9-_\/]{1,255}$/is",$sef))
				{
					$sef_err = 1;
				}
				else
				{
					$sef = strtolower($sef); // в нижний регистр

					// проверяем - есть ли уже запись для `sef`
					$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef' AND `url` <> 'article/section/$section_id'") or die ("Ошибка - 1");
					$sef_query_result = mysql_num_rows($sef_query);

					if($sef_query_result == 0) // нет такого `sef` (наш не в счёт)
					{

						// проверяем - есть ли уже запись
						$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'article/section/$section_id'") or die ("Ошибка - 1");
						$url_query_result = mysql_num_rows($url_query);

						if($url_query_result > 0) // запись есть
						{
							// Обновляем данные в таблице "url"
							$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'article/section/$section_id'";
							$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
						}
						else // запись отсутствует
						{
							// Вставляем данные в таблице "url"
							$url_sql = "INSERT INTO `url` (url, sef) VALUES('article/section/$section_id', '$sef')";
							$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
						}
					}
				}
			}

			if($sef == '')
			{
				// Обновляем данные в таблице "url"
				$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'article/section/$section_id'";
				$url_query = mysql_query($url_sql) or die ("Ошибка - 4");
			}
		}
		// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/article/sectionedit/".$section_id); exit;}


} // конец условия заполненного пункта меню




########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $menu_id, $lvl) // $menu_type 1 - верхнее 2 - левое  $page_id = 0 начальный уровень меню, $lvl - уровень меню
{
	global $site;

	$numtree_sql = "SELECT * FROM `menu` WHERE `parent` = '$menu_id' ORDER BY `ordering` ASC";

	$numtree = mysql_query($numtree_sql) or die ("Невозможно сделать выборку из таблицы - 3");

	$result = mysql_num_rows($numtree);

	if ($result > 0)
	{
		while($m = mysql_fetch_array($numtree)):
			$menu_id = $m['id'];
			$menu_name = $m['name'];
			$menu_id_com = $m['id_com'];

			// Обновляем данные в таблице "menu"
			$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type' WHERE `id` = '$menu_id';";
			$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 4");

			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu_id, $lvl);

		endwhile;

	} // конец проверки $result > 0
} // конец функции tree

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
