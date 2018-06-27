<?php
// DAN обновлено - январь 2014
// Добавляем новый раздел

defined('AUTH') or die('Restricted access');

// определяем тип меню
$section_id = intval($admin_d4);

// определяем тип меню
$menu_t = intval($admin_d5);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------
$menu_type = "left";
$menu_left_selected = "selected";		



function a_com()
{ 
	global $site, $section_id, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected; 	

	
	// определяем содержимое меню	
	$num_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `id_com` = '$section_id' AND `component` = 'shop' AND `p1` = 'section' LIMIT 1";
	
	$num = mysql_query($num_sql) or die ("Невозможно сделать выборку из таблицы - 3");	
	
	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];	
		$menu_name = $m['name'];
		$menu_parent = $m['parent'];			
		$menu_ordering = $m['ordering'];
	endwhile;
	
	if(!isset($menu_parent)){$menu_parent = 0;}
	
	echo '
	<script language="JavaScript"> 
	
	/* ------- Действие при смене типа меню ------- */
	
	function menu_type_select(menu_id) 
	{
		if (document.getElementById("menu_type_left").selected == true)
		{
			menu_type_ajax("left",menu_id);
		}
		if (document.getElementById("menu_type_top").selected == true)
		{
			menu_type_ajax("top",menu_id);			
		}	 
	}
	
	/* ------- Действие при смене типа меню ------- */
	
	
	
	/* ------- AJAX - загрузка ------- */
	
	function getXmlHttp(){
	  var xmlhttp;
	  try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	  } catch (e) {
		try {
		  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
		  xmlhttp = false;
		}
	  }
	  if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
		xmlhttp = new XMLHttpRequest();
	  }
	  return xmlhttp;
	}
	
	function menu_type_ajax(type,m_id)
	{
		var req = getXmlHttp()  
		req.onreadystatechange = function() 
		{
			if (req.readyState == 4) 
			{
				if(req.status == 200) 
				{
					document.getElementById("menu_parent").innerHTML = req.responseText;
				}
			}
		
		}
		req.open(\'GET\', \'http://'.$site.'/administrator/modules/menu_tree.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$menu_parent.', true);
		req.send(null);
		document.getElementById("menu_parent").innerHTML = "<div align=\"left\"><img src=\"http://'.$site.'/administrator/tmp/images/loading.gif\" /></div>";
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
	
	/* ------- / AJAX - загрузка ------- */	
	
	</script>		
	

	
	
	<script language="JavaScript"> 
	 
	function frmpub() 
	{
		if (document.getElementById("sectionpub").checked == true)
		{	
		 	document.getElementById("menupub").innerHTML = \'<input type="checkbox" name="menupub" value="1" checked> показать пункт меню\';	 
		}
		else 
		{
		 	document.getElementById("menupub").innerHTML = \'<span class="unpub"><input class="form_unpub" type="checkbox" name="menupub" value="0" disabled > пункт меню скрыт и неактивен, для отображения - опубликуйте раздел</span>\';	 
		}	
	}
	</script>
	';

	echo '
		<div id="main-top">ИНТЕРНЕТ - МАГАЗИН: Создать раздел</div>
	
		<form method="POST" action="http://'.$site.'/admin/com/shop/sectioninsert">	
		
		<table class="main-tab">
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>	
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Название раздела <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название раздела</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
				<td><input type="text" name="title" size="50" required ></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать раздел <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - раздел отображается. Нет галочки - раздел не отображается, пункт меню тоже не отображается.</span></div></td>
				<td><input type="checkbox" name="sectionpub" value="1" checked></td>
			</tr>			
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Наименование пункта меню</td>
				<td><input type="text" name="menuname" size="20" required ></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать пункт меню <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать пункт меню</em> Пункт меню может быть опубликован только в том случае, если опубликован раздел. Однако раздел может быть опубликован вне зависимости от того - опубликован или нет пункт меню. Таким образом можно создавать разделы интернет - магазина, без публикации пунктов меню</span></div></td>
				<td><input type="checkbox" name="menupub" value="1" checked></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Тип меню</td>
				<td>
					<select onChange="menu_type_select('.$menu_id.')" name="menu_type" id="menu_type">
						<option '.$menu_top_selected.' id="menu_type_top" value="menu_top">Верхнее меню</option>				
						<option '.$menu_left_selected.' id="menu_type_left" value="menu_left">Левое меню</option>					
					</select>				
				</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Родительский пункт меню <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
				<td><div id="menu_parent"></div></td>
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
						<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название раздела – название сайта</b>, например: <b>Браслеты и кольца - интернет-магазин бижутерии</b></span></div></td>
						<td>
							<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
						<td>
							<textarea rows="5" name="tag_description" class="w400">'.$tag_description.'</textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Например, для раздела интернет-магазина <b>Косметика</b> можно указать такой адрес: <b>http://site.ru/cosmetics</b>, для страницы контакты: <b>http://site.ru/contacts</b><br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/shop/section/777</b>, где 777 - номер раздела</span></div></td>
						<td>
							<textarea rows="1" name="sef" id="sef" class="w400" onkeyup="url_ajax()">'.$sef.'</textarea>
						</td>
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
		';

		echo '
		<div id="leftaccordion" class="left_list seo_fon">	
			<div class="left_head section_head_seo">Характеристики товаров и фильтры по характеристикам</div>
			<div class="left_body">
			<div>&nbsp;</div>	
			<table class="main-tab">
				<tr>
					<td>&nbsp;</td>
					<td colspan="5" style="color: #999999; '.$bg_color.'">Характеристики</td>
					<td colspan="3" style="color: #999999; '.$bg_color.'">Фильтры
						<div class="help">			
							<span class="tooltip">
								<img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" />
								<em>Фильтры</em>	
								Фильтры выводятся в разделе, над выводом товаров, пример:
								<br><br>
								<b>Цвет:</b>
								<select size="1" name="D1">
									<option value="Характеристика 1">белый</option>
									<option value="Характеристика 2">синий</option>
									<option value="Характеристика 3">красный</option>
								</select>
								<br>
								<b>Ширина:</b>
								<select size="1" name="D1">
									<option value="Характеристика 1">100</option>
									<option value="Характеристика 2">120</option>
									<option value="Характеристика 3">140</option>
									<option value="Характеристика 4">160</option>
									<option value="Характеристика 5">180</option>
								</select>
								<br>
								<b>Длина:</b><input type="text" name="T1" size="5" value="80">см.&nbsp; до 
								<input type="text" name="T2" size="5" value="120">см.
								<br>
							</span>
						</div>					
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="5">&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				</tr>				
				<tr>
					<td height="25" width="20">&nbsp;</td>
					<td width="120"><b>Использовать</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Характеристики товаров</em>Поставьте галочку напротив тех характеристик, которые Вы собираетесь использовать в данном разделе.</span></div></td>
					<td width="300" height="25"><b>Наименование характеристик</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Наименование характеристик</em>Пример: <ul><li>ширина</li><li>длина</li><li>цвет</li></ul></span></div></td>
					<td width="100"><b>Ед. изм.</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Единица измерения</em>Пример: <ul><li>см.</li><li>кг.</li><li>Вт.</li></ul></span></div></td>
					<td width="100"><b>Тип данных</b>
						<div class="help">			
							<span class="tooltip">
							<img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" />
							<em>Тип данных - строка или число</em>	
							Для характеристик типа <b>строка</b> в качестве значений могут быть как текстовые так и числовые поля.
							Используется такой тип фильтров (примеры):
							<br><br>
							1. <b>Выпадающий список:</b><br>
							<select size="1" name="D1">
								<option value="Характеристика 1">Характеристика 1</option>
								<option value="Характеристика 2">Характеристика 2</option>
								<option value="Характеристика 3">Характеристика 3</option>
								<option value="Характеристика 4">Характеристика 4</option>
								<option value="Характеристика 5">Характеристика 5</option>
								<option value="Характеристика 6">Характеристика 6</option>
								<option value="Характеристика 7">Характеристика 7</option>
							</select>
							<br><br>
							2. <b>Переключатель:</b><br>
							<input type="radio" value="V1" checked name="R1">36<br>
							<input type="radio" value="V1" name="R1">38<br>
							<input type="radio" value="V1" name="R1">40<br>
							<br>
							3. <b>Флаг:</b><br>
							<input type="checkbox" name="C1" value="ON" checked>Дополнительные опции<br>
							<hr>
							Для характеристик типа <b>число</b> используются только числовые значения.	
							Используется такие типы фильтров (пример):
							<br><br>
							Ширина от <input type="text" name="T1" size="5" value="80">см.&nbsp; до 
							<input type="text" name="T2" size="5" value="120">см.<br>
							</span>
						</div>
					</td>
					<td width="100">&nbsp;</td>
					<td width="50"><b>Вкл.</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Включить фильтр</em>Поставьте галочку напротив тех фильтров, которые должны отобраться в данном разделе.</span></div></td>
					<td width="300" height="25"><b>Значение полей фильтра</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Значение полей фильтра</em>Для типа данных <i>строка/число</i> вводите значения фильтра  через точку с запятой, например: <b>белый;синий;красный</b><br><br>для типа данных <i>число</i> введите значения фильтра  <b>от</b> <i>число</i> <b>до</b> <i>число</i></span></div></td>
					<td>&nbsp;</td>					
				</tr>
		';
		
		$text_type = "cтрока / число";
		$bg_color = '';

		for($num = 1; $num <= 30; $num++)
		{
			if($num > 25){$text_type = 'число'; $bg_color = 'background: #c1efff;';}
			echo '
				<tr style="color: #999999; '.$bg_color.'">
					<td>&nbsp;</td>
					<td align="center"><input type="checkbox" name="char_enable_'.$num.'" value="1" ></td>			
					<td><input type="text" name="characteristic_'.$num.'" value="" size="50" ></td>
					<td><input type="text" name="char_unit_'.$num.'" value="" size="7" ></td>
					<td style="line-height:25px;">'.$text_type.'</td>
					<td>&nbsp;</td>
					<td align="center"><input type="checkbox" '.$filter_check[$num].' name="filter_enable_'.$num.'" value="1" ></td>
			';
			
			if($num < 26)
			{
				echo '<td style="line-height:25px;"><input type="text" name="filter_'.$num.'" value="" size="50" ></td>';
			}
			else
			{
				echo '<td style="line-height:25px;">от <input type="text" name="filter_ot_'.$num.'" value="" size="5" > до <input type="text" name="filter_do_'.$num.'" value="" size="5" > </td>';
			}
				
			echo '
					<td>&nbsp;</td>
				</tr>
			';			
		}
		echo '
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
				<td width="200" height="25">Описание раздела:</td>
				<td>&nbsp;</td>
			</tr>		
		</table>
		<input type="hidden" name="section_id" value="'.$section_id.'"/>	
		<textarea name="editor1"></textarea>
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor1\',
				{	        
					height: \'200px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
				}); 	
		</script>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
		<br/>
		&nbsp;
		</form>
		
		<script language="JavaScript">
			menu_type_select('.$menu_id.');
		</script>		
	';
} // конец функции


?>
