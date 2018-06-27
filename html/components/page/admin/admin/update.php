<?php
// DAN обновлено январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

// === Магические кавычки - если магические кавычки не включены - добавляем кавычки через функцию
if (!get_magic_quotes_gpc()) {
  $_GET = addslashes_array($_GET);
  $_POST = addslashes_array($_POST);
  $_COOKIE = addslashes_array($_COOKIE);
}

$page_id = intval($_POST["page_id"]);
$title = trim(htmlspecialchars($_POST["title"]));
$name = trim(htmlspecialchars($_POST["menu"]));
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
$sef = checkingeditor($_POST["sef"]);
if(isset($_POST["access"])){$access = intval($_POST["access"]);} else {$access = 0;}
$psw = $_POST["password"];
$text = $_POST["editor1"];
if(isset($_POST["parent"])){$parent = intval($_POST["parent"]);} else {$parent = 0;}
$ordering = intval($_POST["ordering"]);

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin"); exit;}

if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";} // Условие публикации
if (!isset($parent) || $parent == ""){$parent = "0";} // условие публикации

$err_flag = 0;

// пароль - если не пустой - проверяем на символы
if ($psw != '' && (!preg_match("/^[a-z0-9]{4,20}$/is",$psw)))
{
	$err_flag = 1;
	$err_psw =  '<div>&nbsp;</div><div class="padding-horizontal-20">Поле "Пароль" не заплонено не правильно! Только английские буквы и цифры - от 4 до 20 символов</div>';
}

// заполненность пункта меню
if ($name == "" || $name == " ")
{
	$err_flag = 1;
	$err_name = '<div>&nbsp;</div><div class="padding-horizontal-20">Поле "Наименование пункта меню" не заплонено!</div>';
}

// Проверка на ошибки
if ($err_flag == 1)
{
	$err = '<div id="main-top">Ошибка!</div>'.$err_psw.$err_name;
}
else
{
	// находим "id_menu" и тип меню занесенный в базу
	$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `id_com` = '$page_id' AND `component` = 'page'") or die ("Невозможно сделать выборку из таблицы - 1");

	while($mq = mysql_fetch_array($id_menu_query )):
		$menu_id = $mq['id'];
		$menu_type_last = $mq['menu_type'];
	endwhile;

	// Обновляем данные в таблице "com_page"
	$query_update_page = "UPDATE `com_page` SET `title` = '$title', `text` = '$text', `tag_title` = '$tag_title', `tag_description` = '$tag_description', `access` = '$access', `psw` = '$psw' WHERE `id` = '$page_id'";
	$sql_page = mysql_query($query_update_page) or die ("Невозможно обновить данные 2");


	// Обновляем данные в таблице "menu"
	$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name` = '$name', `pub` = '$pub', `parent` = '$parent', `ordering` = '$ordering', `component` = 'page' WHERE `id_com` = '$page_id' AND `component` = 'page';";
	$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 3");


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
			if (!preg_match("/^[a-z0-9-_\/]{1,255}$/is",$sef))
			{
				$sef_err = 1;
			}
			else
			{
				$sef = strtolower($sef); // в нижний регистр

				// проверяем - есть ли уже запись для `sef`
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef' AND `url` <> 'page/$page_id'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef` (наш не в счёт)
				{
					// проверяем - есть ли уже запись для `url`
					$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'page/$page_id'") or die ("Ошибка - 1");
					$url_query_result = mysql_num_rows($url_query);

					if($url_query_result > 0) // запись есть
					{
						// Обновляем данные в таблице "url"
						$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'page/$page_id'";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
					}
					else // запись отсутствует
					{
						// Вставляем в таблицу
						$url_sql = "INSERT INTO `url` (id, url, sef) VALUES(NULL, 'page/$page_id', '$sef')";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 3") ;
					}
				}
			}
		}

		if($sef == '')
		{
			// Обновляем данные в таблице "url"
			$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'page/$page_id'";
			$url_query = mysql_query($url_sql) or die ("Ошибка - 4");
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/page/".$page_id); exit;}

} // конец условия заполненного пункта меню





########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $menu_id, $lvl) // $menu_type 1 - верхнее 2 - левое  $page_id = 0 начальный уровень меню, $lvl - уровень меню
{
	global $site;
	$lvl++;


	$numtree_sql = "SELECT * FROM menu WHERE `parent` = '$menu_id' ORDER BY `ordering` ASC";

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
