<?php
// DAN 2012
// Опускаем позицию пункта меню $admin_d2 = $d[2];

defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($admin_d4); 

// определяем тип меню
$menu_t = intval($admin_d5);

// оределяем - какое меню надо редактировать и какую таблицу подключать
if (!isset($menu_t) || $menu_t == "0" || $menu_t == "1"){$menu_type = "top";}
if ($menu_t == "2"){$menu_type = "left";}
	
// Определяем родительский пункт меню
$num = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'form' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
while($m = mysql_fetch_array($num)):
	$menu_id = $m['id'];
	$menu_parent = $m['parent'];
	$menu_ordering = $m['ordering'];
endwhile;	
	
// Выводим все поппункты родительского пукта и заносим иx в массив
$num_parent = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `parent` = '$menu_parent' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");
$i = 1; // начальный индекс массива
while($m_parent = mysql_fetch_array($num_parent)):
	$menu_id_ord = $m_parent['id'];
	$menu_ordering_ord = $m_parent['ordering'];
	// если это наш пункт меню - то уменьшаем  его порядок на 1	
	if ($menu_id_ord == $menu_id ) {$menu_ordering_ord++; $np = $menu_ordering_ord;}	
	// если это не наш пункт меню и если порядок этого пункта совпадает с нашим новым порядком нашего пункта - увеличиваем его на 1		
	if ($menu_id_ord != $menu_id && $menu_ordering_ord == $np ){$menu_ordering_ord--;}	
	// === Блок переформатирования (на случай наличия одинаковых порядков === 
	// Создаём массивы
	$A[$i] = array("id"=>"$i", "menuid"=>"$menu_id_ord", "ordering"=>"$menu_ordering_ord");
	$ORD[$i] = $menu_ordering_ord; // массив "Порядок" по нему будем сортировать основной массив $A
	$MENUID[$i] = $menu_id_ord; // Массив пунктов меню (при одинаковом порядке, первым будет тот, у кого id_menu меньше)
	$i++;
	
endwhile;	
/*	
  	foreach($A as $row) 
  	{ 	 
		echo "--- $row[id] - $row[menuid] - $row[ordering] -- $menu_parent <br />";	
  	} 
*/
array_multisort($ORD, SORT_ASC, $MENUID, SORT_ASC, $A);

// переписываем порядок
$j = 1;
foreach($A as $row) 
{ 	
	$B[$j] = array("id"=>"$row[id]", "menuid"=>"$row[menuid]", "ordering"=>"$j");
	$j++;
} 
// === Заносим новый порядок в базу данных ===
	
foreach($B as $row) 
{ 	
	// echo "--- $row[id] - $row[menuid] - $row[ordering] <br />";
	// Обновляем данные в таблице "menu"
	$query_update_menu = "UPDATE `menu` SET `ordering` = '$row[ordering]' WHERE `id` = '$row[menuid]';";
	// echo "=== $query_update_menu <br/>";
	$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 2");
} 	
	
Header ("Location: /admin/site/"); exit;		

?>