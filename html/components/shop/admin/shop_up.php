<?php
defined('AUTH') or die('Restricted access');

$stmt_this = $db->query("SELECT id, parent FROM menu WHERE component = 'shop' AND main = '1' LIMIT 1");
$menu_this = $stmt_this->fetch();

$stmt_menu = $db->prepare("SELECT id, ordering FROM menu WHERE parent = :parent ORDER BY ordering ASC");
$stmt_menu->execute(array('parent' => $menu_this['parent']));

$ordering_arr = $stmt_menu->fetchAll();


// Считаем ogdering по порядку, т.к. могут быть разрывы 1,2,3, 8,9
$count = count($ordering_arr);

for ($i = 0; $i < $count; $i++)
{
	$ordering_arr[$i]['ordering'] = $i;
	if($ordering_arr[$i]['id'] == $menu_this['id']){$i_this = $i;}
}

// Если это не последний элемент - меняем местами значения ячеек
if($i_this < $count - 1)
{
	$ordering_arr[($i_this - 1)]['ordering'] = $i_this;  // следующая ячейка = текущему значению ячейки 'ordering' = $i_this
	$ordering_arr[($i_this)]['ordering'] = $i_this - 1;
}

// Записываем значения в базу данных
for ($i = 0; $i < $count; $i++)
{
	$id = $ordering_arr[$i]['id'];
	$ordering = $ordering_arr[$i]['ordering'];	

	$stmt_update = $db->prepare("UPDATE menu SET ordering = :ordering WHERE id = :id");
	$stmt_update->execute(array('ordering' => $ordering, 'id' => $id));
}

Header ("Location: /admin"); 
exit;			

?>