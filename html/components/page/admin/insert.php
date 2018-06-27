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

$title = trim(htmlspecialchars($_POST["title"]));
$name = trim(htmlspecialchars($_POST["menu"]));
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
$sef = checkingeditor($_POST["sef"]);
if(isset($_POST["access"])){$access = intval($_POST["access"]);} else {$access = 0;}
$psw = $_POST["password"];
$text = $_POST["editor1"];
$menu_t = $_POST["menu_type"];
if(isset($_POST["parent"])){$parent = intval($_POST["parent"]);} else {$parent = 0;}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else {$ordering = 0;}
if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else{$bt_save = '';}// кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else{$bt_prim = '';} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';}// кнопка 'Отменить'


// № пункта преобразуем в число
$menu_id = intval($admin_d3);

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}


// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/"); exit;}

// Условие публикации
if (!isset($pub) || $pub == ""){$pub = "0";}
if (!isset($parent) || $parent == ""){$parent = "0";} else{$parent = $parent;}

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
else {

	// Вставляем в таблицу "com_page"
	$query_insert_page = "INSERT INTO `com_page` (title, text, tag_title, tag_description, access, psw) VALUES( '$title', '$text', '$tag_title', '$tag_description', '$access', '$psw')";
	$sql_page = mysql_query($query_insert_page);

	// Вставляем пункт последним по-порядку

	if (!isset($ordering) || $ordering == ""){ // Если порядок не определён

	// Находим максимальное значение поля ordering для этого родительского уровня меню

	$mo = "SELECT max(`ordering`) `ordering` FROM `menu` WHERE `menu_type` = '$menu_type' AND `parent` = '$parent' ";
	$mxo = mysql_query($mo);
	while($maxord = mysql_fetch_array($mxo)):
		$maxordering = $maxord['ordering'];
	endwhile;

	$maxordering++;
	$ordering = $maxordering;
}

// Находим последнюю запись в таблице 'com_page', что бы вставить значение $id_com в таблицу меню

	$last = "SELECT * FROM `com_page` ORDER BY `id` DESC LIMIT 1";
	$lastid = mysql_query($last);
	while($lid = mysql_fetch_array($lastid)):
		$id_com = $lid['id'];
	endwhile;

// Вставляем в таблицу "menu"
	$query_insert_menu = "INSERT INTO `menu` (menu_type, name, description, pub, parent, ordering, component, main, p1, p2, p3, id_com, prefix_css) VALUES('$menu_type', '$name', 'страница', '$pub', '$parent', '$ordering', 'page', '0', '', '', '', '$id_com', '')";

	$sql_menu = mysql_query($query_insert_menu);

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
					$url_sql = "INSERT INTO `url` (url, sef) VALUES('page/$id_com', '$sef')";
					$url_query = mysql_query($url_sql) or die ("Невозможно вставить данные 4");
				}
			}
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/page/".$id_com); exit;}

} // конец условия заполненного пункта меню

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
