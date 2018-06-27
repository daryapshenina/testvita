<?php
// Вставляем данные в базу данных
defined('AUTH') or die('Restricted access');

$title = htmlspecialchars($_POST["title"]);
$name = htmlspecialchars($_POST["menu"]);
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
$parent = intval($_POST["parent"]);
$quantity = intval($_POST["quantity"]);
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$sef = checkingeditor($_POST["sef"]);
$text = $_POST["editor1"];
if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else{$bt_save = '';} // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else{$bt_prim = '';} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';} // кнопка 'Отменить'

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){	Header ("Location: /admin"); exit;}

// Условие публикации
if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";}
if (!isset($parent) || $parent == ""){$parent = "0";}

// проверка заполнния пункта меню
if ($name == "" || $name == " ")
{
	$err = '<div id="main-top">Поле "Наименование пункта меню" не заплонено!</div>';
}
else
{
	// находим "id_menu" и тип меню занесенный в базу
	$stmt_menu = $db->query("SELECT * FROM menu WHERE component = 'article' AND `p1` = 'all' LIMIT 1");
	
	
	
	
	$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `component` = 'article' AND `p1` = 'all';") or die ("Невозможно сделать выборку из таблицы - 1");

	while($mq = mysql_fetch_array($id_menu_query )):
		$menu_id = $mq['id'];
		$menu_type_last = $mq['menu_type'];
	endwhile;

	// Обновляем данные в таблице "com_article_settings"
	$query_update_article = "UPDATE `com_article_settings` SET `name`='$title', `parametr`='$text' WHERE `id`='1'";
	$sql_item = mysql_query($query_update_article) or die ("Невозможно обновить данные 1");

	// Обновляем данные в таблице "com_article_settings"
	$query_update_article = "UPDATE `com_article_settings` SET `parametr`='$quantity' WHERE `name`='quantity'";
	$sql_article = mysql_query($query_update_article) or die ("Невозможно обновить данные 2");

	// Обновляем данные в таблице "tag_title"
	$query_update_article = "UPDATE `com_article_settings` SET `parametr`='$tag_title' WHERE `name`='tag_title'";
	$sql_article = mysql_query($query_update_article) or die ("Невозможно обновить данные 3");

	// Обновляем данные в таблице "tag_description"
	$query_update_article = "UPDATE `com_article_settings` SET `parametr`='$tag_description' WHERE `name`='tag_description'";
	$sql_article = mysql_query($query_update_article) or die ("Невозможно обновить данные 3");

	// Обновляем данные в таблице "menu"
	$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name`='$name', `pub`='$pub', `parent` = '$parent', `component`='article' WHERE `component`='article' AND `main` = '1';";
	$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 5");



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
		if(classvalidation::checkReservedWord($sef)) // зарезервированно
		{
			$sef_err = 1;
		}
		else
		{
			// проверяем на символы
			if (!preg_match("/^[a-z0-9-\/]{1,255}$/is",$sef))
			{
				$sef_err = 1;
			}
			else
			{
				$sef = strtolower($sef); // в нижний регистр

				// проверяем - есть ли уже запись для `sef`
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef' AND `url` <> 'article/all/1'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef` (наш не в счёт)
				{
					// проверяем - есть ли уже запись
					$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'article/all/1'") or die ("Ошибка - 1");
					$url_query_result = mysql_num_rows($url_query);

					if($url_query_result > 0) // запись есть
					{
						// Обновляем данные в таблице "url"
						$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'article/all/1'";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
					}
					else // запись отсутствует
					{
						// Вставляем в таблицу
						$url_sql = "INSERT INTO `url` (id, url, sef) VALUES(NULL, 'article/all/1', '$sef')";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 3") ;
					}
				}
			}
		}

		if($sef == '')
		{
			// Обновляем данные в таблице "url"
			$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'article/all/1'";
			$url_query = mysql_query($url_sql) or die ("Ошибка - 4");
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/article/all/1"); exit;}

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

// ==================================================================================

function a_com()
{
	global $err;

	echo $err;

} // конец функции

?>
