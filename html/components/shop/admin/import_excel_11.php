<?php
// DAN 2012
// Импорт данных из 1С

defined('AUTH') or die('Restricted access');

set_time_limit(90); // время работы скрипта 90 сек.

$old_delete = intval($_POST["old_delete"]);  

function a_com()
{
	global $site, $root, $old_delete, $file_name_arr;
	
	// ======= УДАЛЯЕМ СТАРЫЕ ДАННЫЕ =================================================================
	if ($old_delete == 1)
	{
		// ------- УДАЛЯЕМ СТАРЫЕ ФАЙЛЫ ИЗОБРАЖЕНИЙ -------------------------------------------------		
		$tovar_sql = "SELECT * FROM `com_shop_item`";	
		
		$tovar_query = mysql_query($tovar_sql) or die ("Невозможно сделать выборку из таблицы - 1");
		
		$resulttov = mysql_num_rows($tovar_query); // количество товаров
		
		$ph = 0;
		if ($resulttov > 0) 
		{	
			while($m = mysql_fetch_array($tovar_query)):
				$item_id = $m['id'];	
				$item_pub = $m['pub'];	
				$item_parent = $m['parent'];
				$item_ordering = $m['ordering'];
				$item_title = $m['title'];
				$item_price = $m['price'];	
				$item_ph = $m['photo'];	
				$item_phbig = $m['photobig'];
				
				if($item_ph != "")
				{ 
					// удаляем малое изображения
					if (file_exists($root.'/components/shop/photo/'.$item_ph))
					{
						unlink($root.'/components/shop/photo/'.$item_ph);
					}
				}
				
				if($item_phbig != "")
				{
					// удаляем большое изображения
					if (file_exists($root.'/components/shop/photo/'.$item_phbig))
					{
						unlink($root.'/components/shop/photo/'.$item_phbig); 
						$ph++;
					}
				}
			endwhile;	
		} // $resulttov > 0			
		// ------- / удаляем старые файлы изображений -------------------------------------------------------
		
		// --- УДАЛЯЕМ СТАРЫЕ ТОВАРЫ ---
		$shop_item_delete_sql = "DELETE FROM `com_shop_item` WHERE `id` > '0';";
		$shop_item_delete_query = mysql_query($shop_item_delete_sql) or die ("Невозможно удалить данные 2");			
		
		$shop_item_ai_sql = "ALTER TABLE `com_shop_item` AUTO_INCREMENT = 1";
		$shop_item_ai_query = mysql_query($shop_item_ai_sql) or die ("Невозможно установить AUTO_INCREMENT =1 3");		
		// --- / удаляем старые товары ---
		
		// --- УДАЛЯЕМ СТАРЫЕ РАЗДЕЛЫ ---
		$com_shop_section_delete_sql = "DELETE FROM `com_shop_section` WHERE `id` > '0';";
		$com_shop_section_delete_query = mysql_query($com_shop_section_delete_sql) or die ("Невозможно удалить данные 4");			
		
		$com_shop_section_ai_sql = "ALTER TABLE `com_shop_section` AUTO_INCREMENT = 1";
		$com_shop_section_ai_query = mysql_query($com_shop_section_ai_sql) or die ("Невозможно установить AUTO_INCREMENT =1 5");		
		// --- / удаляем старые разделы ---		
		
		// --- УДАЛЯЕМ СТАРЫЕ ПУНКТЫ МЕНЮ ---
		$menu_delete_sql = "DELETE FROM `menu` WHERE `component` = 'shop' AND `main` <> '1';";
		$menu_delete_query = mysql_query($menu_delete_sql) or die ("Невозможно удалить данные 6");				
		// --- / удаляем старые пункты меню ---

		// --- УДАЛЯЕМ ЧПУ УРЛ-ы связанные с магазином ---
		$url_delete_sql = "DELETE FROM `url` WHERE `url` LIKE '%shop/section/%';";
		$url_delete_query = mysql_query($url_delete_sql) or die ("Невозможно удалить данные 7");				
		// --- / удаляем ЧПУ УРЛ ---		
	}
	
	if ($ph > 0)
	{
		$usz = '
		<div>Старые разделы и товары удалены</div>
		<div>Удалено старых изображений: '.$ph.'</div>
		<div>&nbsp;</div>
		';
	}
	else {$usz = '';}
	
	// ======= / удаляем старые данные ========================================================================
	
	echo 
	'
	<table id="main-top-tab">
		<tr>
			<td class="imshop">Импорт данных из Excel - шаг 1 из 3</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">		
		<div>&nbsp;</div>		
		<div>&nbsp;</div>
		<div class="import_excel">Обработка старых данных - <font color="#009933">шаг 1 из 3</font></div>
		<div>&nbsp;</div>		
		'.$usz.'		
		<form method="post" action="http://'.$site.'/admin/com/shop/import_and_export/import_excel_12" enctype="multipart/form-data">
			<input class="import_dalee" type="submit" value="Далее" name="bt">
			<input  type="hidden" value="'.$file_name_arr[1].'" name="ext">
		</form>		
	</div>	
	';	
}

?>
