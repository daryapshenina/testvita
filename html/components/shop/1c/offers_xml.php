<?php
// Как это работает.
// У нас в БД у товара есть два идентификатора identifier и group_identifier.
// identifier - это полный идентификатор = идентификатору в файле offers, с решёткой в середине
// group_identifier - идентификатор = идентификатору в файле import (короткий)
// Одинаковые товары имеют одинаковый идентификатор виртуальной группы group_identifier.
// Торговые предложения в 1с - это отдельные товары в нашей базе с разным identifier, но одинаковым group_identifier, по которому объединяются при выводе раздела с группировкой
// Считывая файл import - мы создаём только один товар, а потом в файле offers - смотрим, если есть характеристики у данного товара
// Если характеристики есть
defined('AUTH') or die('Restricted access');
include('properties.php');
include('chars.php');

$reader_xml = new XMLReader();  // cоздаем объект класса XMLReader
if(!$reader_xml->open($root.$dir.$filename)){die("Не удалось открыть файл $filename");} // открываем XML-файл offers.xml

$stmt_process = $db->query('SELECT offers_number, offers_sum, chars FROM com_shop_1c_processing WHERE id = \'1\' ');
$progress = $stmt_process->fetch();
$offers_number = $progress['offers_number'];
$offers_sum = $progress['offers_sum'];

$predlozhenie_flag = 0; // флаг наличия предложения в offers.xml 0 - нет 1- есть

// Считаем $offers_sum
if($progress['chars'] == '0' && $offers_sum == '0')
{
	// Считаем $offers_sum
	$offers_sum = 0;

	while ($reader_xml->read())
	{
		if (($reader_xml->nodeType == XMLReader::ELEMENT) && ($reader_xml->name == 'Предложение'))
		{
			$offers_sum++;  // количество предложений
		}				
	}
	
	// записываем значение $offers_sum
	$stmt_items_sum_update = $db->prepare("UPDATE com_shop_1c_processing SET offers_sum = :offers_sum WHERE id = '1' ");
	$stmt_items_sum_update->execute(array('offers_sum' => $offers_sum));

	exit('progress');
}




$current_number = 1; // текущий номер перебираемого товара

while($reader_xml->read())
{
	// Вносим характеристики / свойства
	if($progress['chars'] == 0 && $reader_xml->nodeType == XMLReader::ELEMENT && $reader_xml->name == 'Свойства')
	{		
		$dom_properties = new DOMDocument('1.0', 'UTF-8');
		$properties_xml = simplexml_import_dom($dom_properties->importNode($reader_xml->expand(),true));

		properties($properties_xml); // Обработка свойств
		
		$stmt_chars = $db->query('UPDATE com_shop_1c_processing SET chars = \'1\' WHERE id = \'1\' ');
		
		progress ($time_start); // Проверка времени работы > $time_limit секунд		
	}



	// Предложения
	if (($reader_xml->nodeType == XMLReader::ELEMENT) && ($reader_xml->name == 'Предложение'))
	{
		if($current_number >= $offers_number) // если мы обрабатываем по шагам
		{
			// Устанавливаем флаг = 1, что означает, что предложения есть в файле offers
			$predlozhenie_flag = 1;

			$dom_offers = new DOMDocument('1.0', 'UTF-8');
			$xml_offers = simplexml_import_dom($dom_offers->importNode($reader_xml->expand(),true));
			
			$identifier_offers = $xml_offers->Ид; // 1С Идентификатор

			$group_identifier = substr($identifier_offers, 0, 36);
			
			$quantity = $xml_offers->Количество; // количество	
			$quantity = str_replace(',', '.', $quantity);
			$quantity = floatval($quantity);
			
			// Проверяем и устанавливаем цену
			if(empty($xml_offers->Цены->Цена->ЦенаЗаЕдиницу)){$price = 0;}
			else{$price = $xml_offers->Цены->Цена->ЦенаЗаЕдиницу;}

			$pub = 1;

			// if($settings->item_quantity == 1 && $quantity == 0){$pub = 0;} else{$pub = 1;}  // если в настройках - учитывать количество и количество нулевое, то снимаем товар с публикации
	
// ******* ПРОВЕРКА ********
// $str = $identifier_offers."\n";
// $file = $root.$dir.'0.txt';
// $f = fopen($file,"a+");
// fwrite($f,$str);
// fclose($f);
// ******** / проверка ********			
	
			// Находим товар
			$stmt_item = $db->prepare('SELECT id FROM com_shop_item WHERE identifier = :identifier LIMIT 1');
			$stmt_item->execute(array('identifier' => $identifier_offers));
			$item_id = $stmt_item->fetchColumn();


			// Если нет характеристик -> group_identifier - уникальный, обновляем данные по group_identifier, identifier может быть пустым
			if (!isset($xml_offers->ХарактеристикиТовара->ХарактеристикаТовара))
			{
				// В настройках стоит - удалять товар с 0 количеством и у нас товар с 0 количеством
				if($settings->item_quantity == 2 && $quantity == 0) // Удалить товар
				{			
					mark_delete($item_id); // помечаем на удаление товар и характеристики	
				}
				else // Обновить товар
				{
					$stmt_offers_update = $db->prepare('UPDATE com_shop_item SET identifier = :identifier, pub = :pub, price = :price, quantity = :quantity, cdate = :cdate WHERE group_identifier = :group_identifier LIMIT 1');
					$stmt_offers_update->execute(array('identifier' => $identifier_offers, 'pub' => $pub, 'price' => $price, 'quantity' => $quantity, 'cdate' => date("Y-m-d H:i:s"), 'group_identifier' => $group_identifier));
				}
			}
			else // Если характеристики есть
			{
				// В настройках стоит - удалять товар с 0 количеством и у нас товар с 0 количеством
				if($settings->item_quantity == 2 && $quantity == 0) // Удалить товар
				{				
					if ($stmt_item->rowCount() > 0)
					{
						mark_delete($item_id); // помечаем на удаление товар и характеристики					
					}
					else // тот случай, когда сначала добавили товар, а потом внесли характеристики в 1C и удалили товар
					{
						// Ищем товар с group_identifier и пустым identifier (первый товар из группы товарных предложений, который ещё без характеристик)
						$stmt_item_0 = $db->prepare('SELECT id FROM com_shop_item WHERE identifier = \'\' AND group_identifier = :group_identifier LIMIT 1');
						$stmt_item_0->execute(array('group_identifier' => $group_identifier));
						$item_id = $stmt_item_0->fetchColumn();

						mark_delete($item_id); // помечаем на удаление товар и характеристики
					}						
				}
				else // Обновить товар
				{

					// Ищем товар с group_identifier и пустым identifier (первый товар из группы товарных предложений, который ещё без характеристик)
					$stmt_item_0 = $db->prepare('SELECT id FROM com_shop_item WHERE identifier = \'\' AND group_identifier = :group_identifier LIMIT 1');
					$stmt_item_0->execute(array('group_identifier' => $group_identifier));
		
					// Если такой товар найден, то первую характеристику из офферс записываем в него и меняем identifier
					if($stmt_item_0->rowCount() > 0)
					{	

						$item_id = $stmt_item_0->fetchColumn();		

						$stmt_offers_update = $db->prepare('UPDATE com_shop_item SET identifier = :identifier, pub = :pub, price = :price, quantity = :quantity, cdate = :cdate WHERE id = :id LIMIT 1');
						$stmt_offers_update->execute(array('identifier' => $identifier_offers, 'pub' => $pub, 'price' => $price, 'quantity' => $quantity, 'cdate' => date("Y-m-d H:i:s"), 'id' => $item_id));

						// пробежка по характеристикам
						foreach ($xml_offers->ХарактеристикиТовара->ХарактеристикаТовара as $ch)
						{

							$char_name = $ch->Наименование; // наименование характеристики
							$char_value = $ch->Значение; // значение характеристики	
						

							char_insert($item_id, $char_name, $char_value); // вставляем значение характеристики						
						}
					}
					else // Нет товара с таким с group_identifier и пустым identifier -> нет нового торгового предложения, обновляем существующие
					{

						item_offers($xml_offers, $identifier_offers, $group_identifier, $pub, $price, $quantity); // добавляем или обновляем характеристики  товара, при необходимости копируем товар	и добавляем характеристики							
					}					
				}
			}


			// Свойства
			if (isset($xml_offers->ЗначенияСвойств->ЗначенияСвойства))
			{
				item_properties($item_id, $xml_offers->ЗначенияСвойств);
			}
		}

		$current_number++; // текущий номер перебираемого товара++;			
	}


	progress ($time_start); // Проверка времени работы > $time_limit секунд
	

	 // Проверка на последний товар
	if($current_number > $offers_sum)
	{
		items_delete(); // Удаляем товары, помечанные на удаление
		// записываем значение $import_number
		$stmt_offers_number_update = $db->prepare("UPDATE com_shop_1c_processing SET offers_number = :offers_number WHERE id = '1' ");
		$stmt_offers_number_update->execute(array('offers_number' => $current_number));		
		exit('success');
	}		
}

// прошли весь файл и если не нашли "Предложение" (иначе повиснем в ожидании ответа) - выходим exit('success');	
if($predlozhenie_flag == 0)
{
	// записываем значение $import_number
	$stmt_offers_number_update = $db->prepare("UPDATE com_shop_1c_processing SET offers_number = :offers_number WHERE id = '1' ");
	$stmt_offers_number_update->execute(array('offers_number' => $current_number));	
	exit('success');
}



// ######################################################################################################
function progress ($time_start)
{
	global $db, $time_limit, $current_number;
	
	// если прошло свыше $time_limit секунд после выполнения скрипта - записываем текущий номер и выдаём exit('progress');
	if(time() - $time_start > $time_limit)
	{
		// записываем значение $import_number
		$stmt_offers_number_update = $db->prepare("UPDATE com_shop_1c_processing SET offers_number = :offers_number WHERE id = '1' ");
		$stmt_offers_number_update->execute(array('offers_number' => $current_number));
		
		exit('progress');
	}	
}



// Копируем товар с одинаковой товарной группой
function item_copy($identifier_offers, $group_identifier, $pub, $price, $quantity)
{
	global $db;
	
	// Ищем товар с group_identifier
	$stmt_item = $db->prepare('SELECT * FROM com_shop_item WHERE group_identifier = :group_identifier LIMIT 1');
	$stmt_item->execute(array('group_identifier' => $group_identifier));
	
	if($stmt_item->rowCount() > 0)
	{
		$item = $stmt_item->fetch();
		
		// Вставляем товар
		$stmt_item_insert = $db->prepare("
		INSERT INTO com_shop_item SET 
		identifier = :identifier,
		group_identifier = :group_identifier,	
		section = :section,
		pub = :pub,
		parent = '0',
		ordering = :ordering,
		title = :title, 
		intro_text = :intro_text,
		full_text = :full_text,
		etext_enabled = :etext_enabled, 
		etext = :etext, 
		price = :price, 
		price_old = :price_old, 
		currency = :currency,  
		quantity = :quantity,
		photo = :photo, 
		photo_big = :photo_big, 
		photo_more = :photo_more,
		new = :new,
		hit = :hit,
		rating = '0',
		discount = :discount, 
		cdate = :cdate, 
		tag_title = :tag_title, 
		tag_description = :tag_description	
		");
		
		$stmt_item_insert->execute(array(
		'section' => $item['section'],
		'identifier' => $identifier_offers,
		'group_identifier' => $item['group_identifier'],	
		'pub' => $pub, 
		'ordering' => $item['ordering'],  
		'title' => $item['title'],
		'intro_text' => $item['intro_text'],	
		'full_text' => $item['full_text'],
		'etext_enabled' => $item['etext_enabled'], 
		'etext' => $item['etext'], 
		'price' => $price, 
		'price_old' => $item['price_old'], 
		'currency' => $item['currency'], 
		'quantity' => $quantity,
		'photo' => $item['photo'], 
		'photo_big' => $item['photo_big'],
		'photo_more' => $item['photo_more'],
		'new' => $item['new'],
		'hit' => $item['hit'], 
		'discount' => $item['discount'],
		'cdate' => date("Y-m-d H:i:s"), 
		'tag_title' => $item['tag_title'], 
		'tag_description' => $item['tag_description']
		));	
		
		return $db->lastInsertId();
	}
}


// Добавляем или обновляем характеристики  товара, при необходимости копируем товар и добавляем характеристики	
function item_offers($xml_offers, $identifier_offers, $group_identifier, $pub, $price, $quantity)
{
	global $db, $root, $dir;

	// Ищем товар с этими характеристиками в БД
	$stmt_item = $db->prepare('SELECT id FROM com_shop_item WHERE identifier = :identifier LIMIT 1');
	$stmt_item->execute(array('identifier' => $identifier_offers));
	
	$item_id = $stmt_item->fetchColumn();
	
	if($stmt_item->rowCount() > 0) // Обновляем характеристики
	{				
		// пробежка по характеристикам
		foreach ($xml_offers->ХарактеристикиТовара->ХарактеристикаТовара as $ch)
		{
			$char_name = $ch->Наименование; // наименование характеристики
			$char_value = $ch->Значение; // значение характеристики	
		
			char_update($item_id, $char_name, $char_value);						
		}

		$stmt_update = $db->prepare('UPDATE com_shop_item SET pub = :pub, price = :price, quantity = :quantity, cdate = :cdate WHERE id = :id LIMIT 1');
		$stmt_update->execute(array('pub' => $pub, 'price' => $price, 'quantity' => $quantity, 'cdate' => date("Y-m-d H:i:s"), 'id' => $item_id));
	}
	else // такого товара нет - ищем товар с аналогичной группой и копируем его добавляем характеристики
	{
		$copy_id = item_copy($identifier_offers, $group_identifier, $pub, $price, $quantity); // копируем товар с одинаковой товарной группой
		
		// пробежка по характеристикам
		foreach ($xml_offers->ХарактеристикиТовара->ХарактеристикаТовара as $ch)
		{
			$char_name = $ch->Наименование; // наименование характеристики
			$char_value = $ch->Значение; // значение характеристики	
		
			char_insert($copy_id, $char_name, $char_value); // вставляем значение характеристики						
		}

		$stmt_update = $db->prepare('UPDATE com_shop_item SET pub = :pub, price = :price, quantity = :quantity, cdate = :cdate WHERE id = :id LIMIT 1');
		$stmt_update->execute(array('pub' => $pub, 'price' => $price, 'quantity' => $quantity, 'cdate' => date("Y-m-d H:i:s"), 'id' => $copy_id));		
	}						
}


// Пометить на удаление. Сразу удалять нельзя - может быть 2 товарных предложения, причём у первого - количество стоит '0'
// В качестве признака маркировки стоит время 0000-00-00 00:00:00
function mark_delete($item_id)
{
	global $db;

	$stmt_mark = $db->prepare("UPDATE com_shop_item SET cdate = '0000-00-00 00:00:00' WHERE id = :id LIMIT 1");
	$stmt_mark->execute(array('id' => $item_id));	
}


// Удаляем товары, помечанные на удаление
function items_delete()
{
	global $db, $root;
	
	$stmt_select = $db->query("SELECT id, photo, photo_big, photo_more FROM com_shop_item WHERE cdate = '0000-00-00 00:00:00'");
	
	while ($n = $stmt_select->fetch())
	{
		$item_id = $n['id'];
		$photo_dir = $root.'/components/shop/photo/'; 

		// если есть файл изображения и его имя не пустое - удяляем файлы изображения
		if (isset($n['photo']) && $n['photo'] != "")
		{		
			if(is_file($photo_dir.$n['photo']))
			{
				unlink($photo_dir.$n['photo']);				
			}

			if(is_file($photo_dir.$n['photo_big']))
			{
				unlink($photo_dir.$n['photo_big']);		
			}			
		}

		$photo_more_arr = explode(';', $n['photo_more']);

		for ($i = 0; $i < count($photo_more_arr); $i++)
		{
			if(is_file($photo_dir.$photo_more_arr[$i]))
			{
				unlink($photo_dir.$photo_more_arr[$i]);
				unlink(str_replace('.jpg', '_.jpg', $photo_dir.$photo_more_arr[$i]));		
			}
		}

		$stmt_delete = $db->prepare('DELETE FROM com_shop_item WHERE id = :id');
		$stmt_delete->execute(array('id' => $item_id));	

		// Удаляем характеристики
		$stmt_item_delete = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
		$stmt_item_delete->execute(array('item_id' => $item_id));

		// Удаляем сопутствующие товары
		$stmt_related_delete = $db->prepare('DELETE FROM com_shop_related_item WHERE item_id = :item_id OR related_id = :item_id');
		$stmt_related_delete->execute(array('item_id' => $item_id));	
	}
}

?>