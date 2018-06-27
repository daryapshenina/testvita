<?php
// Выводит модули сайта в центре (компонентом)
defined('AUTH') or die('Restricted access');

$id = intval($d[3]);

$stmt_select = $db->prepare("
	SELECT id, ordering FROM modules WHERE block IN (
		SELECT * FROM (
			SELECT block FROM modules WHERE id = :id LIMIT 1
		) x
	) ORDER BY ordering
");

$stmt_select->execute(array('id' => $id));

$ordering_arr = $stmt_select->fetchAll();


// Перестраиваем ogdering по порядку
$count = count($ordering_arr);

for ($i = 0; $i < $count; $i++)
{
	$ordering_arr[$i]['ordering'] = $i;
	if($ordering_arr[$i]['id'] == $id){$i_this = $i;}
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
	
	$stmt_update = $db->prepare("UPDATE modules SET ordering = :ordering WHERE id = :id");
	$stmt_update->execute(array('ordering' => $ordering, 'id' => $id));
}

Header ("Location: /admin/modules"); 
exit;
?>