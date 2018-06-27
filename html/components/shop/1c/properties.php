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
		$property_t_true = (boolean)$property_xml->ДляТоваров;
		$property_p_true = (boolean)$property_xml->ДляПредложений;

		// Находим - есть ли такая характеристика уже на сайте
		$stmt_char = $db->prepare('SELECT id, name FROM com_shop_char_name WHERE identifier = :identifier LIMIT 1');
		$stmt_char->execute(array('identifier' => $property_id));

		$now_char_id = $stmt_char->fetch()['id'];
		$now_char_name = $stmt_char->fetch()['name'];

		// Если характеристики нет - создаём её
		if ($stmt_char->rowCount() == 0)
		{
			// Характеристики ИМ
			if($property_t_true || $property_p_true)
			{
				$stmt_char_name_ordering = $db->query('SELECT MAX(ordering) FROM com_shop_char_name');
				$char_ordering = $stmt_char_name_ordering->fetchColumn() + 1;

				$stmt_char_insert = $db->prepare("INSERT INTO com_shop_char_name SET name = :name, unit = '', type = 'string', ordering = :ordering, identifier = :identifier");
				$stmt_char_insert->execute(array('name' => $property_name, 'ordering' => $char_ordering, 'identifier' => $property_id));				
			}
		}
		else // Обновляем данные
		{
			if($now_char_name != $property_name)
			{
				$stmt_char_update = $db->prepare('UPDATE com_shop_char_name SET name = :name WHERE id = :id LIMIT 1');
				$stmt_char_update->execute(array('name' => $property_name, 'id' => $now_char_id));				
			}
		}


		if(!empty($property_xml->ВариантыЗначений))
		{
			foreach($property_xml->ВариантыЗначений->Справочник as $spr)
			{
				$s_id = $spr->ИдЗначения;
				$s_value = $spr->Значение;

				// Проверяем существования значения данного свойства
				$stmt_s = $db->prepare('SELECT id_1c, value FROM com_shop_1с_properties WHERE id_1c = :id_1c LIMIT 1');
				$stmt_s->execute(array('id_1c' => $s_id));

				// Если нет - вставляем
				if($stmt_s->rowCount() == 0)
				{
					$stmt_s_insert = $db->prepare('INSERT INTO com_shop_1с_properties SET id_1c = :id_1c, value = :value');
					$stmt_s_insert->execute(array('id_1c' => $s_id, 'value' => $s_value));
				}
				else
				{
					if($stmt_s->fetch()['value'] != $s_value)
					{
						$stmt_s_update = $db->prepare('UPDATE com_shop_1с_properties SET value = :value WHERE id_1c = :id_1c  LIMIT 1');
						$stmt_s_update->execute(array('id_1c' => $s_id, 'value' => $s_value));
					}
				}									
			}
		}
	}
}




function item_properties($item_id, $prop)
{
	global $db, $root;

	// пробежка по свойствам
	if(!empty($prop->ЗначенияСвойства))
	{
		foreach ($prop->ЗначенияСвойства as $s)
		{
			$s_id = $s->Ид; // наименование характеристики
			$s_id_value = $s->Значение; // значение характеристики

			$stmt_c = $db->prepare("SELECT name FROM com_shop_char_name WHERE identifier = :identifier LIMIT 1");
			$stmt_c->execute(array('identifier' => $s_id));

			$char_name = $stmt_c->fetchColumn();

			$stmt_s = $db->prepare("SELECT value FROM com_shop_1с_properties WHERE id_1c = :id_1c LIMIT 1");
			$stmt_s->execute(array('id_1c' => $s_id_value));

			$char_value = $stmt_s->fetchColumn();

	// ******* ПРОВЕРКА ********
	// $str = $s_id." --- ".$s_id_value."; ".$item_id." => ".$char_name." : ".$char_value."\n";
	// $file = $root.'/components/shop/1c/import_1c/prop.txt';
	// $f = fopen($file,"a+");
	// fwrite($f,$str);
	// fclose($f);
	// ******** / проверка ********	

			char_insert($item_id, $char_name, $char_value); // вставляем значение характеристики						
		}
	}
}


?>