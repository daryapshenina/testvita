<?php
// Поднимаем позицию товара с пересчётом ordering
defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d5); // преобразуем в число

$stmt_item = $db->prepare('SELECT section, ordering FROM com_shop_item WHERE id = :id LIMIT 1');
$stmt_item->execute(array('id' => $item_id));

$m = $stmt_item->fetch();

$item_ordering = $m['ordering'];
$item_section = $m['section'];	

$stmt_items = $db->prepare('SELECT id, ordering FROM com_shop_item WHERE section = :section ORDER BY ordering ASC');
$stmt_items->execute(array('section' => $item_section));

$i = 1; // начальный индекс массива
while($n = $stmt_items->fetch())
{
	$item_id_ord = $n['id'];
	$item_ordering_ord = $n['ordering'];
	
	//	echo "$item_id *** $item_id_ord * $item_ordering_ord <br />"; 		
	
	// если это не наш товар и если порядок этого товара совпадает с будущим порядком нашего товара - увеличиваем его на 1
	if ($item_id_ord != $item_id && $item_ordering_ord == $item_ordering + 1 ) {$item_ordering_ord--;}
	
	// если это наш товар - то увеличиваем его порядок на 1
	if ($item_id_ord == $item_id) {$item_ordering_ord++;}
	
	//	echo "$item_id /// $item_id_ord / $item_ordering_ord <br />"; 		
	
	// === Блок переформатирования (на случай наличия одинаковых порядков === 
	// Создаём массивы
	$A[$i] = array("id"=>"$i", "itemid"=>"$item_id_ord", "ordering"=>"$item_ordering_ord");
	$ORD[$i] = $item_ordering_ord; // массив "Порядок" по нему будем сортировать основной массив $A
	$SECTIONID[$i] = $item_id_ord; // Массив пунктов меню (при одинаковом порядке, первым будет тот, у кого section_id меньше)
	$i++;
}

array_multisort($ORD, SORT_ASC, $SECTIONID, SORT_ASC, $A);

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
	
	$stmt_update = $db->prepare('UPDATE com_shop_item SET ordering = :ordering WHERE id = :id ');
	$stmt_update->execute(array('id' => $row['itemid'], 'ordering' => $row['ordering']));
} 	

Header ("Location: /admin/com/shop/section/".$item_section."/"); exit;		
?>