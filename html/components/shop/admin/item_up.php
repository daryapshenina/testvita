<?php
// DAN 2010
// Поднимаем позицию раздела $admin_d2 = $d[2];

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); // преобразуем в число
	
// Определяем раздел товара
$num = mysql_query("SELECT * FROM com_shop_item WHERE id = $item_id LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
while($m = mysql_fetch_array($num)):
	$item_id = $m['id'];
	$item_ordering = $m['ordering'];
	$item_section = $m['section'];		
endwhile;		

// Выводим все товары из данной категории в порядке следования и заносим иx в массив
$shop_sect = mysql_query("SELECT * FROM com_shop_item WHERE section = $item_section ORDER BY ordering ASC") or die ("Невозможно сделать выборку из таблицы - 2");
$i = 1; // начальный индекс массива
while($n = mysql_fetch_array($shop_sect)):
	$item_id_ord = $n['id'];
	$item_ordering_ord = $n['ordering'];

//	echo "$item_id *** $item_id_ord * $item_ordering_ord <br />"; 		
	
// если это не наш товар и если порядок этого товара совпадает с будущим порядком нашего товара - увеличиваем его на 1
	if ($item_id_ord != $item_id && $item_ordering_ord == $item_ordering-1) {$item_ordering_ord++;}	
	// если это наш товар - то уменьшаем  его порядок на 1
	if ($item_id_ord == $item_id ) {$item_ordering_ord--;}
	
//	echo "$item_id /// $item_id_ord / $item_ordering_ord <br />"; 		
	
	// === Блок переформатирования (на случай наличия одинаковых порядков === 
	// Создаём массивы
	$A[$i] = array("id"=>"$i", "itemid"=>"$item_id_ord", "ordering"=>"$item_ordering_ord");
	$ORD[$i] = $item_ordering_ord; // массив "Порядок" по нему будем сортировать основной массив $A
	$SECTIONID[$i] = $item_id_ord; // Массив пунктов меню (при одинаковом порядке, первым будет тот, у кого section_id меньше)
	$i++;
	
endwhile;	

array_multisort($ORD, SORT_ASC, $SECTIONID, SORT_ASC, $A);
/*
foreach($A as $row) 
{ 	 
	echo "=== $row[id] = $row[itemid] = $row[ordering] <br />";	
} 	
*/

// переписываем порядок
$j = 1;
foreach($A as $row) 
{ 	
	$B[$j] = array("id"=>"$row[id]", "itemid"=>"$row[itemid]", "ordering"=>"$j");
	$j++;
} 
// === Заносим новый порядок в базу данных ===
foreach($B as $row) 
{ 	
	//	echo "--- $row[id] - $row[itemid] - $row[ordering] <br />"; 
	// Обновляем данные в таблице "com_shop_item"
	$query_update_section = "UPDATE `com_shop_item` SET `ordering` = '$row[ordering]' WHERE `id` = '$row[itemid]';";
	$sql_section = mysql_query($query_update_section) or die ("Невозможно обновить данные");
} 	
	
	Header ("Location: http://".$site."/admin/com/shop/section/".$item_section."/"); exit;		

?>