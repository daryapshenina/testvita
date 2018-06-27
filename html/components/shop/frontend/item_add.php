<?php
// DAN 2012
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');

// определяем id раздела
$section_get_id = intval($admin_d4);

// определяем тип меню
$menu_t = intval($admin_d5);

// вывод настроек, необходим, что бы узнать высоту картинки, по ней и выровнять высоту первого редактора
$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 2");
while($m = mysql_fetch_array($num)):
	$setting_id = $m['id'];
	$setting_name = $m['name'];
	$setting_parameter = $m['parametr'];
	
	// размер по "y" малого изображения 
	if ($setting_name == "y_small")
	{
		$h_red = $setting_parameter;
		// если высота меньше 50px, то высота = 50px, иначе = высоте картинки
		if($h_red < 50){$h_red = 50;}
	} 			
endwhile;

// Перед тем как добавить товар - проверяем - есть ли разделы 
$number_sections = mysql_query("SELECT * FROM com_shop_section") or die ("Невозможно сделать выборку из таблицы - 1");
$result_number_sections = mysql_num_rows($number_sections);
if ($result_number_sections < 1)
{
	function a_com()
	{
		echo 
		'
		<div id="main-top">Отсутствуют разделы</div>
		<div style="padding: 10px">Отсутствуют разделы. Необходимо завести хотя бы один раздел.</div>
		';		
	}
}
else 
{
	// Определяем откуда пришли с контекстного меню товара или раздела
	// если это раздел - то $section_id_parent = $section
	if ($section_id_item == "section"){$section_id_parent = $section;}
	else {
		$section_id_item = intval($section_id_item);
		// находим родительский раздел
		$itemsection = mysql_query("SELECT * FROM com_shop_item WHERE id = $section_id_item LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");
			
		while($n = mysql_fetch_array($itemsection)):
			$section_id_parent = $n['section']; 
			$section_id_ordering = $n['ordering']; 	
		endwhile; 
	}

	function a_com()
	{ 
		global $site, $menu_t, $section_id_parent, $section_id_ordering, $h_red; 	
		
		echo '
		<div id="main-top">ИНТЕРНЕТ - МАГАЗИН: Добавить товар</div>
	
		<form enctype="multipart/form-data" method="POST" action="http://'.$site.'/admin/com/shop/iteminsert">	
		
		<table class="main-tab vam">
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>	
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Название товара</td>
				<td><input class="validate[required]" type="text" name="title" size="50"/></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Цена</td>
				<td><input class="validate[required]" type="text" name="price" size="10"/> руб.</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Фотография</td>
				<td><input type="file" name="photo" size="50"/></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать товар</td>
				<td><input type="checkbox" name="pub" value="1" checked/></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Категория</td>
				<td>
				<select size="10" name="section">';
				tree(0,0);
				echo'
				</select>
				</td>
			</tr>				
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Порядок размещения товара</td>
				<td><input type="text" name="ordering" size="3" value="'.$section_id_ordering.'"/></td>
			</tr>
			<tr>
				<td>&nbsp;</td>			
				<td height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>			
		</table>
		';
		
		
		
		echo '
		<div id="leftaccordion" class="left_list seo_fon">	
			<div class="left_head section_head_seo">Мета-теги</div>  
			<div class="left_body">                 
				<table>
					<tr>
						<td width="20">&nbsp;</td>			
						<td width="170" height="25">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">&lt;title&gt; (заголовок)</td>
						<td>
							<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">&lt;description&gt; (описание)</td>
						<td>
							<textarea rows="5" name="tag_description" class="w400">'.$tag_description.'</textarea>
						</td>
					</tr>				
					<tr>
						<td width="20">&nbsp;</td>			
						<td width="170" height="25">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>				
				</table>
			</div>
		</div>		
		';
		
		
		
		echo'
		<table class="main-tab">
			<tr>
				<td>&nbsp;</td>			
				<td height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>					
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Описание товара, вводный текст:</td>
				<td>&nbsp;</td>
			</tr>		
		</table>
		<textarea name="editor1"></textarea>
		<input type="hidden" name="menu_t" value="'.$menu_t.'"/>	
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor1\',
				{	        
					height: \''.$h_red.'px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
				}); 	
		</script>
		<table>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Описание товара, полный текст:</td>
				<td>(выводится в детальном описании товара)</td>
			</tr>		
		</table>
		<textarea name="editor2"></textarea>
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor2\',
				{	        
					height: \'400px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
				}); 	
		</script>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"/>
		<br/>
		&nbsp;
		</form>	
		';
	} // конец функции
}

	
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======
function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{
global $site, $item_section_idm, $select, $section_get_id;
global $lvl; // уровень
$lvl++;

$numtree = mysql_query("SELECT * FROM `menu` WHERE `parent` = '$i' AND `component` = 'shop' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");

	//$otstup = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	if ($lvl == 4) { $otstup = " - - - "; }
	elseif ($lvl == 3) { $otstup = " - - "; }
	elseif ($lvl == 2) { $otstup = " - "; }
	else {$otstup = "";}
	
	
	$result = mysql_num_rows($numtree);

	if ($result > 0) {

	while($m = mysql_fetch_array($numtree)):
		$section_id = $m['id'];
		$section_pub = $m['pub'];
		$section_id_com = $m['id_com'];
		$section_parent = $m['parent'];
		$section_ordering = $m['ordering'];
		$section_title = $m['name'];
		$section_p1 = $m['p1'];
		
	if ($section_get_id == $section_id_com) {$selected = 'selected';}
	else {$selected = '';}

	// Если пункт не является конем то не выводим
	if ($section_p1 != 'all')
	{
		echo'<option value="'.$section_id_com.'" '.$selected.' >'.$otstup.$section_title.'</option>';
	}
	tree($section_id, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским

	$lvl--;

	endwhile;

	} // конец проверки $result > 0
} // конец функции tree

?>