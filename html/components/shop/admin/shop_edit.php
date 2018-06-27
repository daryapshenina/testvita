<?php
defined('AUTH') or die('Restricted access');
include_once($root."/components/shop/classes/classFilter.php");

$head->addFile('/js/drag_drop/drag_drop.js');
$head->addFile('/components/shop/admin/tmp/shop_edit.js');
$head->addFile('/components/shop/admin/tmp/shop_edit.css');

// id_com
$id_com = intval($admin_d4);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------
// сбрасываем значения
$menu_top_selected = "";
$menu_left_selected = "";
 
function a_com()
{ 
	global $db, $shopSettings, $url_arr, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected; 

	// $prv = 0 нет такой страницы $prv = 1 страница есть
	// вывод содержимого меню
	$stmt_menu = $db->query("SELECT * FROM menu WHERE component = 'shop' AND main = '1' LIMIT 1");
	$shop = $stmt_menu->fetch();
	
	if ($shop['menu_type'] == "top"){$menu_top_selected = "selected";}
	if ($shop['menu_type'] == "left"){$menu_left_selected = "selected";}
	
	// выводим содержимое страницы	
	if ($shop['pub'] == 1){$pub = "checked";} else{$pub = "";} // устанавливаем признак публикации

	
	// если есть в массиве ЧПУ - заменяем
	$p_qs = 'shop/all/1';
	
	if(isset($url_arr[$p_qs]) && $url_arr[$p_qs] != '') $sef = $url_arr[$p_qs];
		else $sef = '';
	
	// Характеристики
	$filter = new classFilter;
	
	echo '
	<script language="JavaScript">
	
	select = \''.$filter->getSelect().'\';
	
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
		req.open(\'GET\', \'/administrator/modules/menu_parent.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$shop['parent'].', true);
		req.send(null);
		document.getElementById("menu_parent").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
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
		req.open(\'GET\', \'/administrator/url/ajax.php?sef=\' + sef, true);
		req.send(null);
		document.getElementById("url_status").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
	}		
	/* ------- / AJAX - загрузка ------- */	
	
	</script>
	
	<h1>Редактирование главной страницы интернет-магазина</h1>

	<form method="POST" action="/admin/com/shop/shopupdate/">
	
	<table class="main-tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td style="width:250px; height:25px;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td>&nbsp;</td>		
			<td height="25">Заголовок интернет-магазина <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок главной страницы интернет-магазина</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value = "'.$shopSettings->shop_title.'" required ></td>
		</tr>		
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Наименование пункта меню</td>
			<td><input type="text" name="menu" size="20" value="'.$shop['name'].'" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - страница отображается. Нет галочки - страница не отображается с внешней стороны сайта.</span></div></td>
			<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
		</tr>				
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Тип меню</td>
			<td>
				<select onChange="menu_type_select('.$shop['id'].')" name="menu_type" id="menu_type">
					<option '.$menu_top_selected.' id="menu_type_top" value="menu_top">Верхнее меню</option>				
					<option '.$menu_left_selected.' id="menu_type_left" value="menu_left">Левое меню</option>					
				</select>				
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Родительский пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
			<td><div id="menu_parent"></div></td>
		</tr>
		<tr>
			<td >&nbsp;</td>			
			<td height="25">Порядок расположения</td>
			<td><input type="number" min="0" max="1000" name="ordering" value="'.$shop['ordering'].'" size="5"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
	</table>
	';

	echo '
	<div class="seo_fon">
		<div class="accordion_head left_head">SEO</div>
		<div class="accordion_body a_1">
			<table>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="170" height="25">&nbsp;</td>
					<td width="420">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>заголовок содержимого страницы – название сайта</b>, например: <b>Строительство коттеджей и загородных домов - Строймонтаж</b></span></div></td>
					<td>
						<textarea rows="2" name="tag_title" class="w400">'.$shopSettings->tag_title.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
					<td>
						<textarea rows="5" name="tag_description" class="w400">'.$shopSettings->tag_description.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Автоматически сгенерированный url данной страницы: <b>/shop/all/1</b></div></td>
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
		<div class="accordion_head left_head">Фильтры поиска по характеристикам товаров</div>
		<div class="accordion_body a_2">
			<div style="margin:0px 20px">		
			<div id="char_add" class="button_green" style="margin-top:20px;">+ Добавить характеристику</div>
			<table class="main_tab filter_title_tab" style="border-spacing:0; width:100%;">
				<tr>
					<td class="filter_dnd">&nbsp;</td>
					<td class="filter_char">Фильтр по характеристике:
						<div class="help">			
							<span class="tooltip">
								<img src="/administrator/tmp/images/question-50.png" alt="Помощь" />
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
					<td class="filter_unit" title="Единица измерения">Ед. изм.</td>
					<td class="filter_type">Тип данных</td>						
					<td>Значения полей фильтра
						<div class="help">
							<span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" />
								<em>Значение полей фильтра</em>
								Для типа данных <i>строка</i> вводите значения фильтра  через точку с запятой, например: <b>белый;синий;красный</b>
								<br><br>
								для типа данных <i>число</i> введите значения фильтра  <b>от</b> <i>число</i> <b>до</b> <i>число</i>
							</span>
						</div>					
					</td>
				</tr>			
			</table>
			<div id="drag_trg">'.$filter->getFilter(0).'</div>
			</div>			
		</div>
	</div>	
	';
	
	
	
	echo'
	<table class="main-tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Описание интернет-магазина:</td>
			<td>&nbsp;</td>
		</tr>
	</table>

	<textarea name="editor1">'.$shopSettings->shop_text.'</textarea>
	
	<script type="text/javascript">	
		CKEDITOR.replace( \'editor1\',
			{
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
			}); 	
	</script>
	<br/>
	<input type="hidden" name="menu_id" value="2">
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
	<br/>
	&nbsp;
	</form>
	
	<script language="JavaScript">
		DAN.accordion("accordion_head", "accordion_body");
		menu_type_select('.$shop['id'].');
	</script>		
	';
} // конец функции



?>