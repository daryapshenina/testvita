<?php
defined('AUTH') or die('Restricted access');

$section_id = intval($d[5]);

$stmt_menu = $db->prepare("SELECT id, ordering, component, p1, id_com FROM menu WHERE parent IN (
		SELECT * FROM (
			SELECT parent FROM menu WHERE id_com = :id_com AND component = 'photo' AND p1 = 'section' LIMIT 1
		) x
	) ORDER BY ordering
");

$stmt_menu->execute(array('id_com' => $section_id));
$ordering_arr = $stmt_menu->fetchAll();

// Перестраиваем ogdering по порядку
$count = count($ordering_arr);

for ($i = 0; $i < $count; $i++)
{
	$ordering_arr[$i]['ordering'] = $i;
	if($ordering_arr[$i]['component'] == 'photo' && $ordering_arr[$i]['p1'] == 'section' && $ordering_arr[$i]['id_com'] == $section_id){$i_this = $i;}
}

// Если это не последний элемент - меняем местами значения ячеек
if($i_this < $count - 1)
{
	$ordering_arr[($i_this + 1)]['ordering'] = $i_this;  // следующая ячейка = текущему значению ячейки 'ordering' = $i_this
	$ordering_arr[($i_this)]['ordering'] = $i_this + 1;
}


// Записываем значения в базу данных
for ($i = 0; $i < $count; $i++)
{
	$id = $ordering_arr[$i]['id'];
	$ordering = $ordering_arr[$i]['ordering'];	
	
	$stmt_update = $db->prepare("UPDATE menu SET ordering = :ordering WHERE id = :id");
	$stmt_update->execute(array('ordering' => $ordering, 'id' => $id));
}

Header ("Location: http://".$domain."/admin"); 
exit;
?>