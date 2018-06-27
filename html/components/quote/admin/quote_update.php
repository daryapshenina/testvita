<?php
// DAN 2012
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$title = htmlspecialchars($_POST["title"]);
$name = htmlspecialchars($_POST["menu"]);
$pub = intval($_POST["pub"]);
$parent = intval($_POST["parent"]);
$quantity = intval($_POST["quantity"]);
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$text = $_POST["editor1"];
$none = $_POST["none"]; // кнопка 'Отменить'

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com"); exit;} 

// Условие публикации
if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";} 
if (!isset($parent) || $parent == ""){$parent = "0";} 

// проверка заполнния пункта меню
if ($name == "" || $name == " ") 
{
	$err = '<div id="main-top">Поле "Наименование пункта меню" не заплонено!</div>';
}
else {	
	// находим "id_menu" и тип меню занесенный в базу
	$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `component` = 'quote' AND `p1` = 'all';") or die ("Невозможно сделать выборку из таблицы - 1");
	
	while($mq = mysql_fetch_array($id_menu_query )):
		$menu_id = $mq['id'];	
		$menu_type_last = $mq['menu_type'];	
	endwhile;
	
	// Обновляем данные в таблице "com_quote_settings"
	$query_update_quote = "UPDATE `com_quote_settings` SET `name`='$title', `parametr`='$text' WHERE `id`='1'";
	$sql_item = mysql_query($query_update_quote) or die ("Невозможно обновить данные 1");
	
	// Обновляем данные в таблице "com_quote_settings"
	$query_update_quote = "UPDATE `com_quote_settings` SET `parametr`='$quantity' WHERE `name`='quantity'";
	$sql_quote = mysql_query($query_update_quote) or die ("Невозможно обновить данные 2");		
	
	// Обновляем данные в таблице "tag_title"
	$query_update_quote = "UPDATE `com_quote_settings` SET `parametr`='$tag_title' WHERE `name`='tag_title'";
	$sql_quote = mysql_query($query_update_quote) or die ("Невозможно обновить данные 3");
	
	// Обновляем данные в таблице "tag_description"
	$query_update_quote = "UPDATE `com_quote_settings` SET `parametr`='$tag_description' WHERE `name`='tag_description'";
	$sql_quote = mysql_query($query_update_quote) or die ("Невозможно обновить данные 4");		

	// Обновляем данные в таблице "menu"
	$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name`='$name', `pub`='$pub', `parent` = '$parent', `component`='quote' WHERE `component`='quote' AND `main` = '1';";	
	$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 5");
		
	
	// --- Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---
	if ($menu_type != $menu_type_last)
	{
		// обновляем не только все пункты, но и подпункты данного меню
		tree($menu_type, $menu_id, 0);
	}
	// --- / Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---	
	
	
	Header ("Location: http://".$site."/admin/com"); exit;
		
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