<?php
// Поднимаем позицию пункта меню $admin_d2 = $d[2];
defined('AUTH') or die('Restricted access');

$stmt_this = $db->prepare("SELECT id, parent FROM menu WHERE id_com = :id_com AND component = 'shop' AND p1 = 'section' LIMIT 1");
$stmt_this->execute(array('id_com' => $SITE->d[5]));
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

// Если это не первый элемент - меняем местами значения ячеек
if($i_this > 0)
{
	$ordering_arr[($i_this - 1)]['ordering'] = $i_this;  // предыдущая ячейка = текущему значению ячейки 'ordering' = $i_this
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