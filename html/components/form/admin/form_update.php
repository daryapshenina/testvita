<?php
// DAN 2012
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$form_title = trim(htmlspecialchars($_POST["title"]));
$form_menu = trim(htmlspecialchars($_POST["menu"]));
$form_pub = intval($_POST["pub"]);
$form_parent = intval($_POST["parent"]);
$form_ordering = intval($_POST["ordering"]);
$form_description = $_POST["editor1"];

$form_theme_pub = intval($_POST["theme_pub"]);
$form_theme = trim($_POST["theme"]);
$form_message_pub = intval($_POST["message_pub"]);
$form_message = trim($_POST["message"]);
$form_fio_pub = intval($_POST["fio_pub"]);
$form_fio = trim($_POST["fio"]);
$form_contact_pub = intval($_POST["contact_pub"]);
$form_contact = trim($_POST["contact"]);
$form_email_pub = intval($_POST["email_pub"]);
$form_tel_pub = intval($_POST["tel_pub"]);
$form_file_pub = intval($_POST["file_pub"]);
$form_file_text = trim(htmlspecialchars($_POST["file_text"]));
$form_captcha_pub = intval($_POST["captcha_pub"]);
$form_tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$form_tag_description = trim(htmlspecialchars($_POST["tag_description"]));
$sef = checkingeditor($_POST["sef"]);

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

$bt_save = $_POST["bt_save"]; // кнопка 'Сохранить'
$bt_prim = $_POST["bt_prim"]; // кнопка 'Применить'
$bt_none = $_POST["bt_none"]; // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin"); exit;}

if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";} // Условие публикации
//if (!isset($parent) || $parent == ""){$parent = "0";} // условие публикации

if ($form_title == "" || $form_title == " ")
{
	$err = '<div id="main-top">Поле "Название раздела" не заплонено!</div>';
}
else {
	// находим "id_menu" и тип меню занесенный в базу
	$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `component` = 'form'") or die ("Невозможно сделать выборку из таблицы - 1");

	while($mq = mysql_fetch_array($id_menu_query )):
		$menu_id = $mq['id'];
		$menu_type_last = $mq['menu_type'];
	endwhile;

	// Обновляем данные в таблице "menu"
	$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name` = '$form_menu', `pub` = '$form_pub', `parent` = '$form_parent', `ordering` = '$form_ordering' WHERE `component` = 'form' LIMIT 1" ;

	$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 0");

	// Обновляем данные в таблице "com_form" для id = 1 (заголовок, описание)
	$query_update_form_id_1 = "UPDATE `com_form` SET  `name` = '$form_title', `content` = '$form_description', `tag_title` = '$form_tag_title', `tag_description` = '$form_tag_description' WHERE `id` = '1' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_1) or die ("Невозможно обновить данные 1");

	// Обновляем данные в таблице "com_form" для id = 2 (тема)
	$query_update_form_id_2 = "UPDATE `com_form` SET  `content` = '$form_theme', `pub` = '$form_theme_pub' WHERE `name` = 'theme' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_2) or die ("Невозможно обновить данные 2");

	// Обновляем данные в таблице "com_form" для id = 3 (сообщение)
	$query_update_form_id_3 = "UPDATE `com_form` SET  `content` = '$form_message', `pub` = '$form_message_pub' WHERE `name` = 'message' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_3) or die ("Невозможно обновить данные 3");

	// Обновляем данные в таблице "com_form" для id = 4 (ФИО)
	$query_update_form_id_4 = "UPDATE `com_form` SET  `content` = '$form_fio', `pub` = '$form_fio_pub' WHERE `name` = 'fio' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_4) or die ("Невозможно обновить данные 4");

	// Обновляем данные в таблице "com_form" для id = 5 (Контактные данные)
	$query_update_form_id_5 = "UPDATE `com_form` SET  `content` = '$form_contact', `pub` = '$form_contact_pub' WHERE `name` = 'contact' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_5) or die ("Невозможно обновить данные 5");

	// Обновляем данные в таблице "com_form" для id = 6 (Email)
	$query_update_form_id_6 = "UPDATE `com_form` SET `pub` = '$form_email_pub' WHERE `name` = 'email' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_6) or die ("Невозможно обновить данные 6");

	// Обновляем данные в таблице "com_form" для id = 7 (Телефон)
	$query_update_form_id_7 = "UPDATE `com_form` SET  `pub` = '$form_tel_pub' WHERE `name` = 'tel' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_7) or die ("Невозможно обновить данные 7");

	// Обновляем данные в таблице "com_form" для id = 9 (Email получателя)
	$query_update_form_id_9 = "UPDATE `com_form` SET  `pub` = '$form_file_pub', `content` = '$form_file_text' WHERE `name` = 'file' LIMIT 1" ;

	$sql_section = mysql_query($query_update_form_id_9) or die ("Невозможно обновить данные 9");


	// Обновляем данные в таблице "com_form" для id = 10 (Каптча)
	$query_update_form_id_10 = "UPDATE `com_form` SET  `pub` = '$form_captcha_pub' WHERE `name` = 'captcha' LIMIT 1" ;
	$sql_section = mysql_query($query_update_form_id_10) or die ("Невозможно обновить данные 7");


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
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef' AND `url` <> 'form/all/1'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef` (наш не в счёт)
				{
					// проверяем - есть ли уже запись
					$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'form/all/1'") or die ("Ошибка - 1");
					$url_query_result = mysql_num_rows($url_query);

					if($url_query_result > 0) // запись есть
					{
						// Обновляем данные в таблице "url"
						$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'form/all/1'";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
					}
					else // запись отсутствует
					{
						// Вставляем в таблицу
						$url_sql = "INSERT INTO `url` (id, url, sef) VALUES(NULL, 'form/all/1', '$sef')";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 3") ;
					}
				}
			}
		}

		if($sef == '')
		{
			// Обновляем данные в таблице "url"
			$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'form/all/1'";
			$url_query = mysql_query($url_sql) or die ("Ошибка - 4");
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/form/editform/1"); exit;}


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

} // конец функции

?>
