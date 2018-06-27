<?php
// DAN 2012
// редактируем страницу, определённую переменной $admin_d4 = $d[4];

defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($admin_d4); 

// определяем тип меню
$menu_t = intval($admin_d5);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------
// сбрасываем значения
$menu_top_selected = "";
$menu_left_selected = "";

if (!isset($menu_t) || $menu_t == "0" || $menu_t == "1")
{
	$menu_type = "top";
	$menu_top_selected = "selected";	
}
if ($menu_t == "2")
{
	$menu_type = "left";
	$menu_left_selected = "selected";		
}

 
function a_com()
{ 
	global $site, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected; 

// $prv = 0 нет такой страницы $prv = 1 страница есть
// вывод содержимого меню
	$num = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component`='quote' AND `main`='1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];
		$menu_name = $m['name'];	
		$menu_pub = $m['pub'];	
		$menu_parent = $m['parent'];		
	endwhile;
	
// выводим содержимое страницы	
		$asql = mysql_query("SELECT * FROM `com_quote_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

		while($m = mysql_fetch_array($asql)):
			$quote_settings_id = $m['id'];
			$quote_settings_name = $m['name'];	
			$quote_settings_parametr = $m['parametr'];	
			
			if ($quote_settings_id == 1){$quote_title = $quote_settings_name; $quote_description = $quote_settings_parametr;} // для главной страницы	
	
			if ($menu_pub == 1){$pub = "checked";} else{$pub = "";} // устанавливаем признак публикации

			// Количество цитат
			if ($quote_settings_name == "quantity")
			{
				$quantity = '<input type="number" min="0" max="1000000" name="quantity" size="3" value="'.$quote_settings_parametr.'" required >';
			}	

			// Тег title 
			if ($quote_settings_name == "tag_title"){$tag_title = $quote_settings_parametr;}
			
			// Тег description 
			if ($quote_settings_name == "tag_description"){$tag_description = $quote_settings_parametr;}
			
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
		req.open(\'GET\', \'http://'.$site.'/administrator/modules/menu_parent.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$menu_parent.', true);
		req.send(null);
		document.getElementById("menu_parent").innerHTML = "<div align=\"left\"><img src=\"http://'.$site.'/administrator/tmp/images/loading.gif\" /></div>";
		}
		
	/* ------- / AJAX - загрузка ------- */	
	
	</script>		
	
	
	<div id="main-top">Редактирование главной страницы архива цитат</div>

	<form method="POST" action="http://'.$site.'/admin/com/quote/quoteupdate/">	
	
	<table class="main-tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Заголовок сборника цитат</td>
			<td><input type="text" name="title" size="50" value = "'.$quote_title.'" required ></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Наименование пункта меню</td>
			<td><input type="text" name="menu" size="20" value="'.$menu_name.'" required ></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Опубликовать</td>
			<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
		</tr>				
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Количество цитат на странице</td>
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
			<td width="200" height="25">Описание сборника цитат:</td>
			<td>&nbsp;</td>
		</tr>
	</table>

	<textarea name="editor1">'.$quote_description.'</textarea>
	
	<script type="text/javascript">	
		CKEDITOR.replace( \'editor1\',
			{
				filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
			}); 	
	</script>
	<br/>
	<input type="hidden" name="menu_id" value="2">
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
	<br/>
	&nbsp;
	</form>	
	
	<script language="JavaScript">
		menu_type_select('.$menu_id.');
	</script>		
	';
	
} // конец функции



?>