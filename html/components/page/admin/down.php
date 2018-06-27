<?php
defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($admin_d4);

// Определяем родительский пункт меню
$stmt = $db->prepare("SELECT * FROM `menu` WHERE `id_com` = :id_com AND `component` = 'page' LIMIT 1");
$stmt->execute(array(
	'id_com' => $id_com
));

$m = $stmt->fetch();
$menu_id = $m['id'];
$menu_type = $m['menu_type'];
$menu_parent = $m['parent'];
$menu_ordering = $m['ordering'];

// Выводим все поппункты родительского пукта и заносим иx в массив
$stmt = $db->prepare("SELECT * FROM `menu` WHERE `menu_type` = :menu_type AND `parent` = :parent ORDER BY `ordering` ASC");
$stmt->execute(array(
	'menu_type' => $menu_type,
	'parent' => $menu_parent,
));

$i = 1; // начальный индекс массива
while($m_parent = $stmt->fetch()):
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