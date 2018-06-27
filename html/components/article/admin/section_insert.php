<?php
// DAN обновление - январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$section_title = htmlspecialchars($_POST["title"]);
$section_pub = intval($_POST["sectionpub"]);
$menu_name = htmlspecialchars($_POST["menuname"]);
$menu_pub = intval($_POST["menupub"]);
$menu_t = $_POST["menu_type"];
$menu_parent = intval($_POST["parent"]);

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

// № пункта преобразуем в число
$menu_id = intval($admin_d4);

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

if ($comments != 1){$comments = 0;}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com"); exit;}

if (!isset($section_pub) || $section_pub == ""){$s_pub = "0";} else{$s_pub = "1";} // Условие публикации раздела
if (!isset($menu_pub) || $menu_pub == "" || $section_pub == 0){$m_pub = "0";} else{$m_pub = "1";} // Условие публикации пункта меню
if (!isset($parent) || $parent == ""){$parent = "0";} // Родительская зависимость

if ($sorting == 'order'){$sort = 'order';}
elseif ($sorting == 'date'){$sort = 'date';}
elseif ($sorting == 'alphabet'){$sort = 'alphabet';}
elseif ($sorting == 'views'){$sort = 'views';}
elseif ($sorting == 'rating'){$sort = 'rating';}
else {$sort = 'order';}

// Родительская зависимость
if (!isset($menu_parent) || $menu_parent == ""){$menu_parent = "0";}

// условие заполненного пункта меню
if ($menu_name == "" || $menu_name == " ")
{
	$err = '<div id="main-top">Поле "Наименование пункта меню" не заплонено!</div>';
}
// условие заполненного заголовка
elseif ($section_title == "" || $section_title == " ")
{
	$err .= '<div id="main-top">Поле "Наименование раздела" не заплонено!</div>';
}

else
{
	// ======= Вставляем данные в таблицу меню =====================================================
	// Находим максимальное значение поля ordering для архива статей
	$mo = "SELECT max(ordering) `ordering` FROM `menu` WHERE `menu_type` = '$menu_type' AND `component`='article' AND `parent`='$menu_parent'";
	$mxo = mysql_query($mo)  or die ("Невозможно выбрать данные 1");
	while($maxord = mysql_fetch_array($mxo)):
		$maxordering = $maxord['ordering'];
	endwhile;

	// устанавливаем порядок для нового пункта на 1 больше последнего
	$ordering = $maxordering + 1;

	// Находим все пункты меню, следующие за этим
	$n_sql_query = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `ordering`>='$ordering'";
	$n_sql = mysql_query($n_sql_query) or die ("Невозможно выбрать данные 2");
	while($n = mysql_fetch_array($n_sql)):
		$menu_id = $n['id'];
		$menu_ordering = $n['ordering'];
		$menu_ordering = $menu_ordering + 1;

		// Обновляем данные в таблице "menu" для пунктов с порядком на единицу большим нашего
		$query_update_menu = "UPDATE `menu` SET `ordering` = '$menu_ordering' WHERE `menu_type` = '$menu_type' AND `id` = '$menu_id';";
		$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 3");
	endwhile;

	// ======= Вставляем данные в таблицу разделов архива статей ==============================
	// Находим максимальное значение поля ordering для этого родительского уровня меню
	$mo = "SELECT max(ordering) `ordering` FROM `com_article_section`";
	$mxo = mysql_query($mo) or die ("Невозможно обновить данные 4");
	while($maxord = mysql_fetch_array($mxo)):
		$maxordering = $maxord['ordering'];
	endwhile;

	$maxordering++;

	// Вставляем в таблицу "com_article_section"
	$query_insert_item = "INSERT INTO `com_article_section` (`id`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, `display_subsection`, `display_sub_item`, `sorting`, `display_sorting`, `display_date`, `display_views`, `display_vote`, `show_details`, `title_hyperlink`, `text_output`, `comments`) VALUES (NULL, '$s_pub', '0', '$maxordering', '$section_title', '$section_description', '$tag_title', '$tag_description', '$display_subsection', '$display_sub_item', '$sort', '$display_sorting', '$display_date', '$display_views', '$display_vote', '$show_details', '$title_hyperlink', '$text_output', '$comments')";

	// echo $query_insert_item;

	$sql_item = mysql_query($query_insert_item) or die ("Невозможно вставить данные 1");

	$id_com = mysql_insert_id();

	// ======= Вставляем данны в таблицу меню ===========================================================
	// Вставляем новый пункт в таблицу меню
	$query_insert_menu = "INSERT INTO `menu` (menu_type, name, description, pub, parent, ordering, component, main, p1, p2, p3, id_com, prefix_css) VALUES('$menu_type', '$menu_name', 'раздел архива статей', '$m_pub', '$menu_parent', '$ordering', 'article', '0', 'section', '', '', '$id_com', '')";

	$sql_menu = mysql_query($query_insert_menu) or die ("Невозможно вставить данные 2");



	// --- ЧПУ URL ----------------------------------------------------------------------------------------
	if(isset($sef) && $sef != '')
	{
		if(classvalidation::checkReservedWord($sef)) // зарезервированно
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
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef`
				{
					// Вставляем данные в таблице "url"
					$url_sql = "INSERT INTO `url` (url, sef) VALUES('article/section/$id_com', '$sef')";
					$url_query = mysql_query($url_sql) or die ("Невозможно вставить данные 4");
				}
			}
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/article/sectionedit/".$id_com); exit;}
}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
