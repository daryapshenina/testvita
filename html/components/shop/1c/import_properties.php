<?php
defined('AUTH') or die('Restricted access');
	
function properties($properties_xml)
{
	global $db, $root;

	// Перебираем свойства
	foreach($properties_xml as $property_xml)
	{

		$property_id = $property_xml->Ид;
		$property_name = $property_xml->Наименование;
		$property_true = (boolean)$property_xml->ДляТоваров;

		// Находим - есть ли такая характеристика уже на сайте
		$stmt_char = $db->prepare('SELECT id, name FROM com_shop_char_name WHERE identifier = :identifier LIMIT 1');
		$stmt_char->execute(array('identifier' => $property_id));

		$now_char_id = $stmt_char->fetch()['id'];
		$now_char_name = $stmt_char->fetch()['name'];

		// Если характеристики нет - создаём её
		if ($stmt_char->rowCount() == 0)
		{
			// Характеристики ИМ
			if($property_true)
			{
				$stmt_char_name_ordering = $db->query('SELECT MAX(ordering) FROM com_shop_char_name');
				$char_ordering = $stmt_char_name_ordering->fetchColumn() + 1;

				$stmt_char_insert = $db->prepare("INSERT INTO com_shop_char_name SET name = :name, unit = '', type = 'string', ordering = :ordering, identifier = :identifier");
				$stmt_char_insert->execute(array('name' => $property_name, 'ordering' => $char_ordering, 'identifier' => $property_id));				
			}
		}
		else // Обновляем данные
		{
			if($property_true && $now_char_name != $property_name)
			{
				$stmt_char_update = $db->prepare('UPDATE com_shop_char_name SET name = :name WHERE id = :id LIMIT 1');
				$stmt_char_update->execute(array('name' => $property_name, 'id' => $now_char_id));				
			}
		}
	}
}
?>