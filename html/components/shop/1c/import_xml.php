<?php
// Обработка файла import.xml
// $steps == '0' - группы НЕ проходили; '1' - группы прошли, он НЕ прошли свойства - характеристики; '2' - прошли и группы и свойства - характеристики
defined('AUTH') or die('Restricted access');
include($root.'/lib/image_processor.php');
include('import_section.php');
include('properties.php');
include('chars.php');
include('import_item.php');

$reader_xml = new XMLReader();  // cоздаем объект класса XMLReader
if(!$reader_xml->open($root.$dir.$filename)){die("Не удалось открыть файл $filename");} // открываем XML-файл import.xml

$stmt_process = $db->query("SELECT import_number, import_sum, steps FROM com_shop_1c_processing WHERE id = '1' ");
$progress = $stmt_process->fetch();
$import_number = $progress['import_number'];
$import_sum = $progress['import_sum'];
$steps = $progress['steps'];

// При первом проходе считаем сумму товарных позиций
if($steps == '0' && $import_number == '0' && $import_sum == '0')
{
	// Считаем $import_sum
	$import_sum = 0;
	
	while ($reader_xml->read())
	{	
		if (($reader_xml->nodeType == XMLReader::ELEMENT) && ($reader_xml->name == 'Каталог'))
		{
			$import_1c_changes = $reader_xml->getAttribute('СодержитТолькоИзменения');
			
			// ------- ЕСЛИ ЗАГРУЗКА ПОЛНАЯ - СТИРАЕМ БД --------------------------------------
			if ($import_1c_changes == 'false' && $settings->c1_db_reset == 1) // загрузка полная
			{
				$stmt_items = $db->query('SELECT photo, photo_big, photo_more FROM com_shop_item');
				
				while($row = $stmt_items->fetch())
				{
					$photo_old_name = $row['photo'];
					$photobig_old_name = $row['photo_big'];
					$photo_more_arr = explode(';', $row['photo_more']);
					
					// удаляем старые фотографии
					$photo_dir = '/components/shop/photo/';
					$photo_old = $root.$photo_dir.$photo_old_name;
					$photobig_old = $root.$photo_dir.$photobig_old_name;

					// если есть файл изображения и его имя не пустое - удяляем файлы изображения
					if (isset($photobig_old_name) && $photobig_old_name != "")
					{
						if(file_exists($photo_old)){unlink($photo_old);}
						if(file_exists($photobig_old)){unlink($photobig_old);}
					}

					for ($i = 0; $i < count($photo_more_arr); $i++)
					{
						if(file_exists($photo_dir.$photo_more_arr[$i])){unlink($photo_dir.$photo_more_arr[$i]);}
						if(file_exists($photo_dir.$photo_more_arr[$i])){unlink(str_replace('.jpg', '_.jpg', $photo_dir.$photo_more_arr[$i]));}
					}
				}

				// Удаляем товары
				$stmt_delete = $db->exec('DELETE FROM com_shop_item');
				$stmt_ai = $db->exec('ALTER TABLE  com_shop_item AUTO_INCREMENT = 1');
			
				// Удаляем разделы
				$stmt_delete = $db->exec('DELETE FROM com_shop_section');
				$stmt_ai = $db->exec('ALTER TABLE  com_shop_section AUTO_INCREMENT = 1');

				// удаляем пункты меню
				$stmt_delete = $db->exec('DELETE FROM menu WHERE id_com > 0 AND component = \'shop\' AND main <> 1');
				
				// удаляем характеристики
				$stmt_delete = $db->exec('DELETE FROM com_shop_char');
				$stmt_ai = $db->exec('ALTER TABLE  com_shop_char AUTO_INCREMENT = 1');

				// удаляем свойства 1С
				$stmt_delete = $db->exec('DELETE FROM com_shop_1с_properties');
				$stmt_ai = $db->exec('ALTER TABLE com_shop_1с_properties AUTO_INCREMENT = 1');
			}
		}


		if (($reader_xml->nodeType == XMLReader::ELEMENT) && ($reader_xml->name == 'Товар'))
		{
			$import_sum++; // количество товаров
		}	
	}
	

	if($import_sum == 0){exit('No items!');}
	
	// записываем значение $import_sum
	$stmt_items_sum_update = $db->prepare("UPDATE com_shop_1c_processing SET import_sum = :import_sum WHERE id = '1' ");
	$stmt_items_sum_update->execute(array('import_sum' => $import_sum));

	exit('progress');	
}





$current_number = 1; // текущий номер перебираемого товара

while($reader_xml->read())
{
	if($steps == 0 && $import_number == 0 ) // ни группы ни товары ещё не проходили
	{
		// Раздел
		if ($steps == 0 && $reader_xml->nodeType == XMLReader::ELEMENT && $reader_xml->name == 'Группы' ) // группы ещё не пройдены, $steps = 0
		{
			$dom_section = new DOMDocument('1.0', 'UTF-8');
			$section_xml = simplexml_import_dom($dom_section->importNode($reader_xml->expand(),true));
	
			section_tree($section_xml,0); // Разборка группы
			
			$stmt_steps = $db->query('UPDATE com_shop_1c_processing SET steps = \'1\' WHERE id = \'1\' ');
			
			exit('progress');			
		}
	}


	// Свойства >>> Характеристики
	if($steps == 1 && $import_number == 0 ) // группы прошли, товары ещё не проходили
	{
		// Свойства
		if ($steps == 1 && $reader_xml->nodeType == XMLReader::ELEMENT && $reader_xml->name == 'Свойства' )
		{
			$dom_properties = new DOMDocument('1.0', 'UTF-8');
			$properties_xml = simplexml_import_dom($dom_properties->importNode($reader_xml->expand(),true));
	
			properties($properties_xml); // Обработка свойств
			
			$stmt_steps = $db->query('UPDATE com_shop_1c_processing SET steps = \'2\' WHERE id = \'1\' ');
			
			exit('progress');			
		}
	}


	// Товар
	if (($reader_xml->nodeType == XMLReader::ELEMENT) && ($reader_xml->name == 'Товар'))
	{
		if($current_number >= $import_number)
		{
			import_item($reader_xml);	
		}	

		progress ($time_start); // проверка времени работы > $time_limit секунд

		 // Проверка на последний товар
		if($current_number >= $import_sum)
		{
			$stmt_process_update = $db->prepare("UPDATE com_shop_1c_processing SET import_number = :import_number WHERE id = '1' ");
			$stmt_process_update->execute(array('import_number' => $current_number));			
			exit('success');
		}
		
		$current_number++; // следующий товар	
	}
}


// ############################################################################
function progress ($time_start)
{
	global $db, $time_limit, $current_number;

	// если прошло свыше $time_limit секунд после выполнения скрипта - записываем текущий номер и выдаём exit('progress');
	if(time() - $time_start > $time_limit)
	{
		$stmt_process_update = $db->prepare("UPDATE com_shop_1c_processing SET import_number = :import_number WHERE id = '1' ");
		$stmt_process_update->execute(array('import_number' => $current_number));
		exit('progress');
	}	
}

?>