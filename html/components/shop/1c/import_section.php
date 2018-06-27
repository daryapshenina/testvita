<?php
// Рекурсия по группам
defined('AUTH') or die('Restricted access');

// Находим, в каком меню находится главный пункт ИМ
$stmt_shop_menu = $db->query('SELECT menu_type FROM menu WHERE component = \'shop\' AND p1 = \'all\' LIMIT 1');
$row_shop_menu = $stmt_shop_menu->fetch();
$shop_menu = $row_shop_menu['menu_type'];	

function section_tree($section_xml, $section_parent_id)
{
	global $db, $root, $dir, $shop_menu;

	// количество групп
    $count_g = count($section_xml->Группа);

	// Перебираем группы
	for ($gr = 0; $gr < $count_g; $gr++)
	{
		$section_arr = $section_xml->Группа[$gr];

		$section_id = $section_arr->Ид;
		$section_name = $section_arr->Наименование;
		$section_group = $section_arr->Группы;
		
		
		// Находим - есть ли такой раздел  уже на сайте
		$stmt_section = $db->prepare('SELECT id FROM com_shop_section WHERE identifier = :identifier LIMIT 1');
		$stmt_section->execute(array('identifier' => $section_id));

		$now_section_id = $stmt_section->fetchColumn();
		

		// Если раздела нет - создаём его
		if ($stmt_section->rowCount() == 0)
		{
			// Раздел ИМ
			$stmt_section_insert = $db->prepare('INSERT INTO com_shop_section SET identifier = :identifier, pub = 1, parent = 0, ordering = :ordering, title = :title, description = \'\', tag_title = \'\',  tag_description =\'\', date =:date ');
			$stmt_section_insert->execute(array('identifier' => $section_id, 'ordering' => $gr, 'title' => $section_name, 'date' => date("Y-m-d H:i:s")));
			
			$id_com = $db->lastInsertId();

			// Меню
			$stmt_menu_insert = $db->prepare('INSERT INTO menu SET menu_type = :menu_type, name = :name, description = \'Раздел интернет-магазина\', pub = 1, parent = :parent, ordering = :ordering, component = \'shop\',  main =\'0\', p1 =\'section\', p2 =\'\', p3 =\'\', id_com = :id_com, prefix_css = \'\' ');
			$stmt_menu_insert->execute(array('menu_type' => $shop_menu, 'name' => $section_name, 'parent' => $section_parent_id, 'ordering' => $gr, 'id_com' => $id_com));

			$menu_id = $db->lastInsertId();
		}
		else // Обновляем данные
		{
			// Разделы ИМ
			$stmt_section_update = $db->prepare('UPDATE com_shop_section SET title = :title WHERE id = :id LIMIT 1');
			$stmt_section_update->execute(array('title' => $section_name, 'id' => $now_section_id));
			
			// Меню
			$stmt_section_update = $db->prepare('UPDATE menu SET name = :name WHERE component = \'shop\' AND p1 = \'section\' AND id_com = :id_com  LIMIT 1');
			$stmt_section_update->execute(array('name' => $section_name, 'id_com' => $now_section_id));

			$stmt_section_parent = $db->prepare('SELECT id FROM menu WHERE component = \'shop\' AND p1 = \'section\' AND id_com = :id_com  LIMIT 1');
			$stmt_section_parent->execute(array('id_com' => $now_section_id));
			$menu_id = $stmt_section_parent->fetchColumn();
		}		
		

		// Если есть вложенные группы - вызываем рекурсию
		if(isset($section_group) && $section_group != ''){section_tree($section_group, $menu_id);}
	}
}
?>