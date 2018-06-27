<?php
defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($d[4]); 

$stmt_menu = $db->prepare("SELECT * FROM menu WHERE id_com = :id_com AND component = 'article' AND main = 1 LIMIT 1");
$stmt_menu->execute(array('id_com' => $id_com));
$menu = $stmt_menu->fetch();

// Устанавливаем нулевой порядок меню
if ($menu['parent'] == ""){$menu['parent'] = 0;}
	
// Выводим все поппункты родительского пукта и заносим иx в массив
$stmt_parent = $db->prepare("SELECT * FROM menu WHERE parent = :parent ORDER BY ordering ASC");
$stmt_parent->execute(array('parent' => $menu['parent']));

$i = 1; // начальный индекс массива
while($m_parent = $stmt_parent->fetch()):
	$menu_id_ord = $m_parent['id'];
	$menu_ordering_ord = $m_parent['ordering'];
	// если это наш пункт меню - то уменьшаем  его порядок на 1	
	if ($menu_id_ord == $menu['id'] ) {$menu_ordering_ord++; $np = $menu_ordering_ord;}	
	// если это не наш пункт меню и если порядок этого пункта совпадает с нашим новым порядком нашего пункта - увеличиваем его на 1		
	if ($menu_id_ord != $menu['id'] && $menu_ordering_ord == $np ){$menu_ordering_ord--;}	
	// === Блок переформатирования (на случай наличия одинаковых порядков === 
	// Создаём массивы
	$A[$i] = array("id"=>"$i", "menuid"=>"$menu_id_ord", "ordering"=>"$menu_ordering_ord");
	$ORD[$i] = $menu_ordering_ord; // массив "Порядок" по нему будем сортировать основной массив $A
	$MENUID[$i] = $menu_id_ord; // Массив пунктов меню (при одинаковом порядке, первым будет тот, у кого id_menu меньше)
	$i++;
endwhile;	

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
	$stmt_update = $db->prepare("UPDATE menu SET ordering = :ordering WHERE id = :id");
	$stmt_update->execute(array('ordering' => $row['ordering'], 'id' => $row['menuid']));
} 	
	
Header ("Location: /admin/site/"); exit;			

?>