<?php
defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $root, $db; 
	

	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Импорт из 1С</div>
		<div>&nbsp;</div>
		<div style="margin:10px 10px 20px 10px">
	';	
	
	/* ======= CSV ============================================================ */
	if(file_exists($root."/components/shop/import_1c_7/price.csv"))
	{
		// нужно для корректной работы функции fgetcsv - она чувствительна к локали
		setlocale(LC_ALL, 'ru_RU.utf8');
		
		$handle = fopen($root."/components/shop/import_1c_7/price.csv", "r") or die("ошибка открытия файла <b>price.csv</b>!");
		
		while(($data = fgetcsv($handle, 1000, "#")) !== FALSE) 
		{
			// количество колонок
			$num = count($data);
			
			 //номер строки
			$row++;
			
			for ($i=0; $i < $num; $i++) 
			{
				//$data[$i] = iconv("Windows-1251", "UTF-8", $data[$i]);
				
				$import_sign = $data[0]; // признак раздел / товар
				$import_level = $data[1]; // уровень
				$import_identifier = $data[2]; // идентификатор
				$import_parent = intval($data[3]); // родительский раздел
				$import_title = $data[6]; // заголовок
				$import_price = $data[9]; // цена
				$import_photo = $data[14]; // изображение
				$import_photo_small = 'small/'.$import_photo; // изображение малое
			}
			
			//echo $row.' - '.$import_title.'<br>';
			

			
			// ------- Раздел ------------------------------------------------------------ 
			if($import_sign == 1)
			{
				// --- ищем этот раздел в БД ---
				$stmt_section = $db->prepare("SELECT id, parent, title FROM com_shop_section WHERE identifier = :identifier LIMIT 1");
				$stmt_section->execute(array('identifier' => $import_identifier));
				
				if($stmt_section->rowCount > 0) // раздел существует
				{
					while($m = $stmt_section->fetch())
					{
						$section_id = $m['id'];
						$section_parent = $m['parent'];
						$section_title = $m['title'];						
					}
					
					// если есть изменения - обновляем БД
					if($section_parent != $import_parent && $section_title != $import_title) 
					{
						$stmt_update = $db->prepare("UPDATE com_shop_section SET parent = :parent, title = :title WHERE identifier = :identifier LIMIT 1");
						$stmt_update->execute(array('parent' => $import_parent, 'title' => $import_title, 'identifier' => $import_identifier));
						
						$repeat = $import_level - 1;
						$out_section .= '<span syle="color:#008000">'.str_repeat("-", $repeat).' '.$import_title.'</span><br>';						
					}
				}
				else // если раздел не существует - вставляем данные в БД
				{
					// Вставляем данные в таблицу "com_shop_section"	
					$section_insert_sql = "INSERT INTO `com_shop_section` (`id`, `identifier`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, 
					`char_enable_1`, `char_enable_2`, `char_enable_3`, `char_enable_4`, `char_enable_5`, `char_enable_6`, `char_enable_7`, `char_enable_8`, `char_enable_9`, `char_enable_10`, 
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`, 
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					date
					) 
					VALUES (NULL, '$import_identifier', '1', '0', '$row', '$import_title', '', '', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '',
					NOW();
					)";	
					
				
					//echo '<br><br>
					//'.$section_insert_sql.'
					//<br><br>';
					
					$query_insert = $db->exec($section_insert_sql);
					$id_com = db->lastInsertId();						
					
					// Находим $section_id родителя для нашего $import_parent
					$stmt_select = $db->prepare("SELECT id FROM com_shop_section WHERE identifier = :parent LIMIT 1");
					$stmt_select->execute(array('parent' => $import_parent));

					$s = $stmt_select->fetch();
					$section_id = $s['id'];
					
					if($stmt_select->rowCount() > 0)
					{
						// Находим $menu_id для $section_id
						$stmt_menu = $db->prepare("SELECT id FROM menu WHERE component = 'shop' AND p1 = 'section' AND id_com = :section_id LIMIT 1");
						$stmt_menu->execute(array('section_id' => $section_id));						
					}
					
					if($stmt_menu->rowCount() > 0) // родительский раздел
					{
						$menu_parent = $stmt_menu->fetchColumn();
					}
					else // если нет родительского раздела, то `parent` = 0
					{
						$menu_parent = 0;
					}
					
					// Вставляем новый пункт в таблицу меню
					$menu_insert_sql = "INSERT INTO `menu` (`id`, `menu_type`, `name`, `description`, `pub`, `parent`, `ordering`, `component`, `main`, `p1`, `p2`, `p3`, `id_com`, `prefix_css`) VALUES(NULL, 'left', '$import_title', 'раздел интернет-магазина', '1', '$menu_parent', '$row', 'shop', '0', 'section', '', '', '$id_com', '')";
					$query_insert = $db->exec($menu_insert_sql);
					$menu_id = db->lastInsertId();
					
					$repeat = $import_level - 1;
					$out_section .= str_repeat("-", $repeat).' '.$import_title.'<br>';
				}
			}
			
			
			// ------- Товар ---------------------------------------------------------------
			if($data[0] == 0)
			{
				// --- ищем этот товар в БД ---
				$stmt_item = $db->prepare("SELECT * FROM com_shop_item WHERE identifier = :identifier LIMIT 1");
				$stmt_item->execute(array('identifier' => $import_identifier));				
				
				if($stmt_item->rowCount() > 0) // товар существует
				{
					while($m = $stmt_item->fetch())
					{
						$item_id = $m['id'];
						$item_section = $m['section'];
						$item_title = $m['title'];
						$item_price = $m['price'];
						$item_photo = $m['photo'];						
					}
					
					// если есть изменения - обновляем БД
					if($item_section != $import_parent && $item_title != $import_title && $import_price != $item_price && $import_photo != $import_photo) 
					{
						$item_update_sql = "UPDATE  `com_shop_item` SET  `section` =  '$import_parent', `title` =  '$import_title', `price` = '$import_price', `photo` = '$import_photo' WHERE  `identifier` = '$import_identifier' LIMIT 1 ;";
						$query = $db->exec($item_update_sql);
						
						$repeat = $import_level - 1;
						$out_item .= '<span syle="color:#008000">'.$import_title.'</span><br>';				
					}					
				}
				else // товар не существует - вставляем его в БД
				{
					
					// // Ищем раздел для товара. Находим $section_id родителя для нашего $import_parent
					$stmt_section = $db->prepare("SELECT id FROM com_shop_section WHERE identifier = :parent LIMIT 1");
					$stmt_section->execute(array('parent' => $import_parent));					

					$section_id = $stmt_section->fetchColumn();					
				
					// Вставляем в таблицу "com_shop_item"	
					$item_insert_sql = "INSERT INTO `com_shop_item` (`id`, `identifier`, `section`, `pub`, `parent`, `ordering`, `title`, `introtext`, `fulltext`, `etext_enabled`, `etext`, `price`, `quantity`, `photo`, `photobig`, `cdate`, `tag_title`, `tag_description`, 
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`
					) 
					VALUES (NULL, '$import_identifier', '$section_id', '1', '0', '$row', '$import_title', '', '', '0', '', '$import_price', '1', '$import_photo_small', '$import_photo', NOW(), '', '', 
					'', '', '', '', '', '', '', '0', '0', '0')";	

					$query_insert = $db->exec($item_insert_sql);			
					$out_item .= $import_title.'<br>';
				}
			}			
		}
		
		fclose($handle);

		echo '
			<div><b>Разделы:</b></div>
			<div>&nbsp;</div>
		';		
		echo $out_section;
		
		echo '
			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<div><b>Товары:</b></div>
			<div>&nbsp;</div>
		';		
		echo $out_item;
		
	}
	else
	{
		echo'<span style="color:#ff0000">Файл <b>price.csv</b> не найден!</span>';
	}
		
	echo'
		</div>
		<div>&nbsp;&nbsp;<a href="/admin/com/shop/all"><input class="greenbutton" type="button" value="Выход" name="bt"></a></div>
	';			
}

?>