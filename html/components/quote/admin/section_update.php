<?php
// DAN 2012
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

$section_description = $_POST["editor1"];
$none = $_POST["none"]; // кнопка 'Отменить'

// определяем тип меню
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($none == "Отменить"){	Header ("Location: http://".$site."/admin/com"); exit;} 

if (!isset($section_pub) || $section_pub == ""){$s_pub = "0";} else{$s_pub = "1";} // Условие публикации раздела
if (!isset($menu_pub) || $menu_pub == "" || $section_pub == 0){$m_pub = "0";} else{$m_pub = "1";} // Условие публикации пункта меню

if ($section_title == "" || $section_title == " ") 
{ 
	$err = '<div id="main-top">Поле "Название раздела" не заплонено!</div>';
}
else {
		// находим "id_menu" и тип меню занесенный в базу
		$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `component` = 'quote' AND `p1` = 'section' AND `id_com` = '$section_id';") or die ("Невозможно сделать выборку из таблицы - 1");
		
		while($mq = mysql_fetch_array($id_menu_query )):
			$menu_id = $mq['id'];	
			$menu_type_last = $mq['menu_type'];	
		endwhile;

	// Обновляем данные в таблице "menu"	
		$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name` = '$menu_name', `pub` = '$m_pub', `parent` = '$menu_parent', `ordering` = '$menu_ordering' WHERE `id_com` = '$section_id' AND `component` = 'quote' AND `p1` = 'section' LIMIT 1" ;
		
		$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 2");	
	
	// Обновляем данные в таблице "com_quote_section"	
		$query_update_section = "UPDATE `com_quote_section` SET  `title` = '$section_title', `description` = '$section_description', `pub` = '$section_pub', `ordering` = '$menu_ordering', `tag_title` = '$tag_title', `tag_description` = '$tag_description' WHERE `id` = '$section_id' LIMIT 1" ;
		
		$sql_section = mysql_query($query_update_section) or die ("Невозможно обновить данные 3");
		
			
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
	
function a_com()
{ 
	global $err; 
	echo $err;
	
} // конец функции

?>