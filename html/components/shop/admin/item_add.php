<?php
// DAN обновление - январь 2014
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');

// определяем id раздела
$section_get_id = intval($admin_d4);

$component_head .= '<script type="text/javascript" src="http://'.$site.'/js/dan.framework.js"></script>';
$component_head .= '<script type="text/javascript" src="http://'.$site.'/components/shop/admin/item_add.js"></script>';

// Скрипт для получения списка доступных характеристик выбранного раздела
$component_head .=  '
	<script type="text/javascript">

		var req = getXmlHttp();

		function get_char_list(section_id)
		{
			req.open("POST", "/components/shop/admin/shop_section_char_get.php", true);
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send("section_select="+section_id);

			req.onreadystatechange = function() 
			{
				if (req.readyState == 4) 
				{
					if (req.status == 200) 
					{
						// Вставляем список характеристик
						document.getElementById("char_list").innerHTML = req.responseText;
					}
				}
			}
		}

		
		function url_ajax()
		{
			sef = document.getElementById("sef").value;
			
			var req = getXmlHttp()  
			req.onreadystatechange = function() 
			{
				if (req.readyState == 4) 
				{
					if(req.status == 200) 
					{
						document.getElementById("url_status").innerHTML = req.responseText;
					}
				}
			
			}
			req.open(\'GET\', \'http://'.$site.'/administrator/url/ajax.php?sef=\' + sef, true);
			req.send(null);
			document.getElementById("url_status").innerHTML = "<div align=\"left\"><img src=\"http://'.$site.'/administrator/tmp/images/loading.gif\" /></div>";
		}
	
	</script>
';		
	
	

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

	// учитывать количество товаров
	if($setting_name == "item_quantity")
	{
		if($setting_parameter == 0)
		{
			$item_quantity = '';
		}		
		else
		{
			$item_quantity = '
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Количество <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Количество товаров</em>Товары с нолевым количеством обозначаются статусом <br><b>&quot;Под заказ&quot;</b>.</span></div></td>
				<td><input type="text" name="quantity" value="1" size="10"></td>
			</tr>';
		}
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
		global $site, $url_arr, $item_quantity, $menu_t, $section_id_parent, $section_id_ordering, $h_red, $array_specs, $section_get_id;

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
				<td width="200" height="25">Название товара <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название товара</em>Выводится над основным содержимым в теле страницы.<br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
				<td><input type="text" name="title" size="50" required></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Цена</td>
				<td><input type="text" name="price" size="10" required> руб.</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>
				<td width="200" height="25">Скидка <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Товар со скидкой</em>Товар маркируется статусом "Скидка".<br>Появляется дополнительное поле "Старая цена", которая выводится на сайте в перечёркнутом виде, рядом с обычной ценой.</span></td>
				<td><input id="discount" type="checkbox" name="discount" value="1" '.$pub_discount.'> <span id="price_old_display">Старая цена: <input type="text" name="price_old" size="10" value="'.$item_price_old.'"> руб.</span></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>
				<td width="200" height="25">Новинка <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Новинка</em>Товар маркируется статусом "Новинка".</span></td>
				<td><input type="checkbox" name="new" value="1" '.$pub_new.'></td>
			</tr>			
			'.$item_quantity.'
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Фотография</td>
				<td><input type="file" name="photo" size="50"/></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать товар <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать товар</em>Галочка стоит - товар отображается. Нет галочки - товар не отображается с внешней стороны сайта.</span></td>
				<td><input type="checkbox" name="pub" value="1" checked></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Категория</td>
				<td>
				<select size="10" name="section" id="shop_select_tree_cat">';
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
			<div class="left_head section_head_seo">SEO</div>  
			<div class="left_body">                 
				<table>
					<tr>
						<td width="20">&nbsp;</td>			
						<td width="170" height="25">&nbsp;</td>
						<td width="420">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название товара – название сайта</b>, например: <b>Платье Kitti - интернет-магазин детской одежды</b></span></div></td>
						<td>
							<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание товара</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br>При выводе товара, если этот тег не заполнен - система управления достаточно грамотно генерирует этот тег в таком формате: <b>Название товара. Цена. Краткое описание.</b> пример: <b>Платье Kitti. Цена: 350 руб. рост 90 и 120. Производство Китай. Состав: хлопок 100%</b>.</span></div></td>
						<td>
							<textarea rows="5" name="tag_description" class="w400">'.$tag_description.'</textarea>
						</td>
					</tr>				
					<tr>
						<td>&nbsp;</td>			
						<td height="25">ЧПУ URL</td>
						<td>Формируется только автоматически</td>
						<td style="vertical-align: middle;"><div id="url_status"></div></td>
					</tr>					
					<tr>
						<td>&nbsp;</td>			
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>			
				</table>
			</div>
		</div>		
		<div id="leftaccordion" class="left_list seo_fon">	
			<div class="left_head section_head_seo">Характеристики товара</div>
			<div class="left_body" id="char_list"></div>
		</div>
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
		<div>&nbsp;</div>
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor2\',
				{	        
					height: \'400px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
				});
				
			get_char_list('.$section_get_id.');

			document.getElementById("shop_select_tree_cat").onchange = function()
			{
				get_char_list(document.getElementById("shop_select_tree_cat").options[document.getElementById("shop_select_tree_cat").selectedIndex].value);
			};				
		</script>		

		
		<div><input class="etext_enabled" type="checkbox" name="etext_enabled" onclick="etext_hide()" value="1"> Электронный товар
			<div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Электронный товар</em>Это товар с возможностью получения доступа или ссылок для скачивания, (например электронный курс) сразу после оплаты электронными деньгами. <br><br>После оплаты покупатель попадает на страницу получения товара. Страница получения товара станет доступной в административной части, если Вы поставите галочку <b>Электронный товар</b>. Это поле поле выделяется зелёной рамкой.</span></div>		
		</div>
		<div>&nbsp;</div>
		<div id="etext"><textarea name="editor3"></textarea></div>
		<div>&nbsp;</div>
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor3\',
				{	        
					height: \'400px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
				}); 	
		</script>	
		<script type="text/javascript">
			function etext_hide() 
			{	
				var etext_enabled = document.getElementsByName("etext_enabled");
				
				if(etext_enabled[0].checked)
				{
					document.getElementById("etext").style.display = "block";
				}
				else
				{
					document.getElementById("etext").style.display = "none";
				}
			}			
			etext_hide();
		</script>		
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
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

	$numtree = mysql_query("SELECT * FROM `menu` WHERE `parent` = '$i' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");

	//$otstup = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	if ($lvl == 4) { $otstup = " - - - "; }
	elseif ($lvl == 3) { $otstup = " - - "; }
	elseif ($lvl == 2) { $otstup = " - "; }
	else {$otstup = "";}
	
	
	$result = mysql_num_rows($numtree);

	if ($result > 0) 
	{
		while($m = mysql_fetch_array($numtree)):
			$section_id = $m['id'];
			$section_pub = $m['pub'];
			$section_id_com = $m['id_com'];
			$section_parent = $m['parent'];
			$section_component = $m['component'];
			$section_ordering = $m['ordering'];
			$section_title = $m['name'];
			$section_p1 = $m['p1'];
			
		if ($section_get_id == $section_id_com AND $section_component == 'shop') {$selected = 'selected';}
		else {$selected = '';}

		// Если пункт не является корнем и не является магазином то не выводим
		if ($section_p1 != 'all' AND $section_component == 'shop')
		{
			echo'<option value="'.$section_id_com.'" '.$selected.' >'.$otstup.$section_title.'</option>';
		}
		tree($section_id, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским

		$lvl--;

		endwhile;
	} // конец проверки $result > 0
} // конец функции tree

?>
