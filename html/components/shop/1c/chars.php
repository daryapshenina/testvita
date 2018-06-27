<?php
defined('AUTH') or die('Restricted access');

// Вставляем характеристики
function char_insert($item_id, $char_name, $char_value)
{
	global $db;

	if(empty($item_id) || empty($char_name) || empty($char_value)){return false;}
	
	if($item_id != NULL)
	{
		// находим name_id для характеристики
		$stmt_char_name = $db->prepare('SELECT id FROM com_shop_char_name WHERE name = :name LIMIT 1');
		$stmt_char_name->execute(array('name' => $char_name));
		$name_id = $stmt_char_name->fetchColumn();

		// Добавляем характеристику
		if($stmt_char_name->rowCount() == 0)
		{
			// находим ordering
			$stmt_char_name_ordering = $db->query('SELECT MAX(ordering) FROM com_shop_char_name');
			$char_name_ordering = $stmt_char_name_ordering->fetchColumn() + 1;

			$stmt_char_name_insert = $db->prepare("INSERT INTO com_shop_char_name SET name = :name, unit = '', type = 'string', ordering = :ordering");
			$stmt_char_name_insert->execute(array('name' => $char_name, 'ordering' => $char_name_ordering));

			$name_id = $db->lastInsertId();						
		}

		// находим ordering
		$stmt_char_ordering = $db->prepare('SELECT MAX(ordering) FROM com_shop_char WHERE item_id =:item_id');
		$stmt_char_ordering->execute(array('item_id' => $item_id));
		$char_ordering = $stmt_char_ordering->fetchColumn() + 1;						
		
		$stmt_char_value_insert = $db->prepare('INSERT INTO com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering');
		$stmt_char_value_insert->execute(array('item_id' => $item_id, 'name_id' => $name_id, 'value' => $char_value, 'ordering' => $char_ordering));		
	}
}


// Обновляем характеристики
function char_update($item_id, $char_name, $char_value)
{
	global $db;

	if(empty($item_id) || empty($char_name) || empty($char_value)){return false;}	
	
	// находим name_id для характеристики
	$stmt_char_name = $db->prepare('SELECT id FROM com_shop_char_name WHERE name = :name LIMIT 1');
	$stmt_char_name->execute(array('name' => $char_name));
	$name_id = $stmt_char_name->fetchColumn();	

	$stmt_char = $db->prepare('SELECT id FROM com_shop_char WHERE item_id = :item_id AND name_id = :name_id LIMIT 1');
	$stmt_char->execute(array('item_id' => $item_id, 'name_id' => $name_id));

	if($stmt_char->rowCount() > 0) // такое значение характеристики - есть - обновляем его
	{
		$stmt_char_update = $db->prepare('UPDATE com_shop_char SET value = :value WHERE item_id = :item_id AND name_id = :name_id LIMIT 1');
		$stmt_char_update->execute(array('item_id' => $item_id, 'name_id' => $name_id, 'value' => $char_value));		
	}
	else
	{
		// находим ordering
		$stmt_char_ordering = $db->prepare('SELECT MAX(ordering) FROM com_shop_char WHERE item_id =:item_id');
		$stmt_char_ordering->execute(array('item_id' => $item_id));
		$char_ordering = $stmt_char_ordering->fetchColumn() + 1;						
		
		$stmt_char_value_insert = $db->prepare('INSERT INTO com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering');
		$stmt_char_value_insert->execute(array('item_id' => $item_id, 'name_id' => $name_id, 'value' => $char_value, 'ordering' => $char_ordering));	
	}
	
}

?>