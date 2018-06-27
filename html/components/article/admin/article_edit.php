<?php
// DAN обновление - январь 2014
// редактируем страницу, определённую переменной $admin_d4 = $d[4];

defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($admin_d4); 

function a_com()
{ 
	global $site, $url_arr, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected; 

// $prv = 0 нет такой страницы $prv = 1 страница есть
// вывод содержимого меню
	$num = mysql_query("SELECT * FROM `menu` WHERE `component`='article' AND `main`='1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];
		$menu_name = $m['name'];
		$menu_type = $m['menu_type'];		
		$menu_pub = $m['pub'];	
		$menu_parent = $m['parent'];		
	endwhile;
	
// сбрасываем значения
$menu_top_selected = "";
$menu_left_selected = "";

if ($menu_type == "top")
{
	$menu_top_selected = "selected";	
}
if ($menu_type == "left")
{
	$menu_left_selected = "selected";		
}
	
	
// выводим содержимое страницы	
		$asql = mysql_query("SELECT * FROM `com_article_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

		while($m = mysql_fetch_array($asql)):
			$article_settings_id = $m['id'];
			$article_settings_name = $m['name'];	
			$article_settings_parametr = $m['parametr'];	
			
			if ($article_settings_id == 1){$article_title = $article_settings_name; $article_description = $article_settings_parametr;} // для главной страницы	
			
			if ($article_settings_name == 'tag_title'){$tag_title = $article_settings_parametr;}
			if ($article_settings_name == 'tag_description'){$tag_description = $article_settings_parametr;}	
	
			if ($menu_pub == 1){$pub = "checked";} else{$pub = "";} // устанавливаем признак публикации

		// Заголовок банка изображений 
			if ($article_settings_name == "quantity")
			{
				$quantity = '<input type="number"  min="1" max="1000" name="quantity" size="3" value="'.$article_settings_parametr.'" required >';
			} 			 						
		endwhile;		

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
		req.open(\'GET\', \'/administrator/modules/menu_parent.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$menu_parent.', true);
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
	
	
	<h1>Редактирование главной страницы архива статей</h1>

	<form method="POST" action="/admin/com/article/updatearticle/">	
	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Заголовок архива статей <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок архива статей</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value = "'.$article_title.'" required ></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Наименование пункта меню</td>
			<td><input type="text" name="menu" size="20" value="'.$menu_name.'" required ></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Опубликовать <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать</em>Галочка стоит - страница отображается. Нет галочки - страница не отображается с внешней стороны сайта.</span></div></td>
			<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
		</tr>				
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Количество статей на странице</td>
			<td>'.$quantity.'</td>
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
			<td width="200" height="25">Родительский пункт меню</td>
			<td><div id="menu_parent"></div></td>
		</tr>		
		<tr>
			<td>&nbsp;</td>			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	';
	

		
	// если есть в массиве ЧПУ - заменяем
	$p_qs = 'article/all/1';
	
	if(isset($url_arr[$p_qs]) && $url_arr[$p_qs] != '')
	{
		$sef = $url_arr[$p_qs];
	}
	else
	{
		$sef = '';
	}		
		
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
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br>Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>заголовок содержимого страницы – название сайта</b>, например: <b>Строительство коттеджей и загородных домов - Строймонтаж</b></span></div></td>
					<td>
						<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
					<td>
						<textarea rows="5" name="tag_description" class="w400">'.$tag_description.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>			
					<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. </span></div></td>
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
			
	
	
	echo'
	<br/>
	<textarea name="editor1">'.$article_description.'</textarea>
	
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
		menu_type_select('.$menu_id.');
	</script>		
	';
	
} // конец функции



?>