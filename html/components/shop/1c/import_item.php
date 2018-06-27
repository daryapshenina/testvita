<?php
// Товар - обрабатываем
defined('AUTH') or die('Restricted access');

function import_item($reader_xml)
{
	global $root, $dir, $db, $settings, $current_number, $import_sum;

	$dom_item = new DOMDocument('1.0', 'UTF-8');

	// import - первоначально - сбрасываем значение
	$images = '';
	$recvisits_name = '';
	$recvisits_value = '';

	// заносим в simple XML
	$tovar = simplexml_import_dom($dom_item->importNode($reader_xml->expand(),true));

	$id_c = $tovar->Ид; // 1С Идентификатор
	$article = $tovar->Артикул; // 1С Артикул
	$name = $tovar->Наименование; // 1C Наименование
	$section_id_c = $tovar->Группы->Ид; // 1C Группы товара
	$prop = $tovar->ЗначенияСвойств; // Массив свойств
	$description = $tovar->Описание; // Описание
	$images = $tovar->Картинка; // 1C Картинка //
	$recvisits_name = $tovar->ЗначенияРеквизитов->ЗначениеРеквизита->Наименование; // Реквизиты - наименование
	$recvisits_value = $tovar->ЗначенияРеквизитов->ЗначениеРеквизита->Значение; // Реквизиты - значение
	$images_more = ''; // дополнительные фотографии
	
	$article_txt = '<div class="item_article">Артикул: '.$article.'</div>';
	
	$name = @replace_quotes($name);
	$name = htmlspecialchars($name);
 
	$description =  nl2br($description);
	//$description = htmlspecialchars($description);
	$images_big = str_replace ('.jpg', '_.jpg', $images);


	// --- ИЩЕМ ЭТОТ ТОВАР В БАЗЕ ---
	$stmt_item = $db->prepare('SELECT id FROM com_shop_item WHERE group_identifier = :group_identifier LIMIT 1'); // по идентификатору группы в базе, т.к. полный идентификатор = идентификатору в файле offers - c #
	$stmt_item->execute(array('group_identifier' => $id_c));
	$item_id = $stmt_item->fetchColumn();
	
	// Удаляем товар со статусом "Удалён"
	if ($stmt_item->rowCount() > 0 && $item_id != 0 && $tovar['Статус'] == 'Удален')
	{
		$stmt_delete = $db->prepare('DELETE FROM com_shop_item WHERE id = :id');
		$stmt_delete->execute(array('id' => $item_id));

		// Удаляем характеристики
		$stmt_item_delete = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
		$stmt_item_delete->execute(array('item_id' => $item_id));

		// Удаляем сопутствующие товары
		$stmt_related_delete = $db->prepare('DELETE FROM com_shop_related_item WHERE item_id = :item_id OR related_id = :item_id');
		$stmt_related_delete->execute(array('item_id' => $item_id));			
	}	


	$images = (array)$images;
	if(!isset($images[0])){$images[0] = '';}

	$images_count = count($images);


	// --- ИЗОБРАЖЕНИЯ И ПАПКИ ---
	for($k = 0; $k < $images_count; $k++)
	{
		$file_images = $root.$dir.$images[$k]; // путь до файла

		// Если есть файл изображений - вызываем процессор изображений
		if(file_exists($file_images) && $images[$k] != '')
		{
			// --- СОЗДАЁМ ДИРЕКТОРИЮ ---
			// находим директорию
			$lastchr = strrpos($images[$k], '/'); // находит последнюю позицию символа '/' в строке
			$f_dir = substr($images[$k], 0, $lastchr);  // директория (символы от "0"до последнего вхождения "/")
			$img_name = substr($images[$k], $lastchr+1);

			$img_name_arr = explode(".", $img_name);
			$img_small_name[$k] = $img_name_arr[0].'.jpg';
			$img_big_name[$k] = $img_name_arr[0].'_.jpg';

			$folders = explode("/", $f_dir);
			$path = "";

			foreach($folders as $folder)
			{
				$path = $path.'/'.$folder;

				$folder_dir = $root."/components/shop/photo".$path;

				if(!file_exists($folder_dir)) // директория не существует
				{
					mkdir($folder_dir);
				}
			}


			// ------- Процессор изображений -------
			$tmp_path = $root.$dir.$images[$k];
			$img_small_path[$k] = $f_dir.'/'.$img_small_name[$k];

			switch($settings->small_resize_method)
			{
				case "1": // Умный ресайз
				{
					include_once($root."/classes/ImageResize/ImageResizeSmart.php");
					$img_small = new ImageResizeSmart($tmp_path, $root.'/components/shop/photo/'.$img_small_path[$k], $settings->x_small, $settings->y_small);
				} break;

				case "2": // Подрезка
				{
					include_once($root."/classes/ImageResize/ImageResizeCutting.php");
					$img_small = new ImageResizeCutting($tmp_path, $root.'/components/shop/photo/'.$img_small_path[$k], $settings->x_small, $settings->y_small);
				} break;

				case "3": // Скукожить
				{
					include_once($root."/classes/ImageResize/ImageResizeCompression.php");
					$img_small = new ImageResizeCompression($tmp_path, $root.'/components/shop/photo/'.$img_small_path[$k], $settings->x_small, $settings->y_small);
				} break;

				default: // Указанный размер
				{
					include_once($root."/classes/ImageResize/ImageResize.php");
					$img_small = new ImageResize($tmp_path, $root.'/components/shop/photo/'.$img_small_path[$k], $settings->x_small, $settings->y_small);
				}
			}

			$img_small->run();

            $size = getimagesize($file_images); 
            $src_width = $size[0];
            $src_height = $size[1];
            
            if($src_width > $settings->x_big){$src_width = $settings->x_big;}
            if($src_height > $settings->y_big){$src_height = $settings->y_big;}

			$img_big_path[$k] = $f_dir.'/'.$img_big_name[$k];

			include_once($root."/classes/ImageResize/ImageResize.php");
			$img_big = new ImageResize($tmp_path, $root.'/components/shop/photo/'.$img_big_path[$k], $src_width, $src_height);

			$img_big->run();


// ******* ПРОВЕРКА ********
//$str = $k." ---------- ".$src_width." ---------- ".$settings->x_big."\n";
//$str .= $src_height." ---------- ".$settings->y_big."\n";
//$file = $root.$dir.'0.txt';
//$f = fopen($file,"a+");
//fwrite($f,$str);
//fclose($f);
// ******** / проверка ********


			// img_load($settings->small_resize_method, $img_name, $img_dir, $tmp_file, $settings->x_small, $settings->y_small, $settings->x_big, $settings->y_big);

			if($k > 0){$images_more .= $img_small_path[$k].';';} // начинаем вносить изображения с индекса 1 (2 и далее по счёту)
		}
	}


	if(isset($img_small_path[0])){$image_small = $img_small_path[0];} else{$image_small = '';}
	if(isset($img_big_path[0])){$image_big = $img_big_path[0];} else {$image_big = '';}

	if ($stmt_item->rowCount() > 0 && $item_id != 0) // если товар есть, обновляем его
	{
		$stmt_update = $db->prepare('UPDATE com_shop_item SET title = :title, intro_text = :intro_text, full_text = :full_text, photo = :photo, photo_big = :photo_big, photo_more = :photo_more, cdate = :cdate WHERE id = :id LIMIT 1');
		$stmt_update->execute(array('title' => $name, 'intro_text' => $article_txt, 'full_text' => $description, 'photo' => $image_small, 'photo_big' => $image_big, 'photo_more' => $images_more, 'cdate' => date("Y-m-d H:i:s"), 'id' => $item_id));
	}
	else // товара нет, добавляем его
	{
		if($tovar['Статус'] != 'Удален')
		{
			// Находим раздел для товара
			$stmt_section = $db->prepare('SELECT id FROM com_shop_section WHERE identifier = :identifier LIMIT 1');
			$stmt_section->execute(array('identifier' => $section_id_c));

			$row_section = $stmt_section->fetch();
			$section_id = $row_section['id'];
			$item_ordering = $current_number;

			if(!empty($section_id))
			{
				$stmt_insert = $db->prepare('INSERT INTO com_shop_item SET identifier = \'\', group_identifier = :group_identifier, section = :section, pub = 1, parent = 0, ordering = :ordering, title = :title, `intro_text` = :intro_text, `full_text` = :full_text, etext_enabled = 0, etext = \'\', price = 0, price_old = 0, currency = 0,  quantity = 1, photo = :photo, photo_big = :photo_big, photo_more = :photo_more, new = 0, discount = 0, hit = 0, rating = 0, cdate = :cdate, tag_title =\'\', tag_description = \'\' ');
				$stmt_insert->execute(array('group_identifier' => $id_c, 'section' => $section_id, 'ordering' => $item_ordering, 'title' => $name, 'intro_text' => $article_txt, 'full_text' => $description, 'photo' => $image_small, 'photo_big' => $image_big, 'photo_more' => $images_more, 'cdate' => date("Y-m-d H:i:s") ));

				$item_id = $db->lastInsertId();
			}		
		}
	}


	// Свойства
	if (isset($prop))
	{
		item_properties($item_id, $prop);
	}


	// Свойства - характеристики
	foreach($prop as $xml_char)
	{	
		$char_identifier = $xml_char->ЗначенияСвойства->Ид;
		$char_value = $xml_char->ЗначенияСвойства->Значение;

		// Проверяем существование данного свойства
		$stmt_char = $db->prepare('SELECT id FROM com_shop_char_name WHERE identifier = :identifier');
		$stmt_char->execute(array('identifier' => $char_identifier));

	
		// Свойство не существует и новое название не пустое - добавляем его
		if($stmt_char->rowCount() > 0) 
		{
			$name_id = $stmt_char->fetchColumn();

			// Ищем - есть ли значение у данного свойства.
			$stmt_c = $db->prepare("SELECT id, value FROM com_shop_char WHERE item_id = :item_id AND name_id = :name_id LIMIT 1");
			$stmt_c->execute(array('item_id' => $item_id, 'name_id' => $name_id));
			$now_char_id = $stmt_c->fetch()['id'];
			$now_char_value = $stmt_c->fetch()['value'];

			if($stmt_c->rowCount() == 0 && $char_value != '') // Наименование значения есть и новое значение не пустое
			{
				// Находим ordering
				$stmt_char_ordering = $db->prepare('SELECT MAX(ordering) FROM com_shop_char WHERE item_id =:item_id');
				$stmt_char_ordering->execute(array('item_id' => $item_id));
				$char_ordering = $stmt_char_ordering->fetchColumn() + 1;

				$stmt_char_value_insert = $db->prepare('INSERT INTO com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering');
				$stmt_char_value_insert->execute(array('item_id' => $item_id, 'name_id' => $name_id, 'value' => $char_value, 'ordering' => $char_ordering));
			}
			else
			{
				if($char_value == '') // Если новое значение пустое - удаляем старое значение
				{
					$stmt_delete = $db->prepare('DELETE FROM com_shop_char WHERE id = :id');
					$stmt_delete->execute(array('id' => $now_char_id));						
				}
				else if($char_value != $now_char_value)// Иначе - обновляем
				{
					$stmt_char_update = $db->prepare('UPDATE com_shop_char SET value = :value WHERE id = :item_id');
					$stmt_char_update->execute(array('value' => $char_value, 'item_id' => $now_char_id));
				}
			}
		}
	}
}



?>