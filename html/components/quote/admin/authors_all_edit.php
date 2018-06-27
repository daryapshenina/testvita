<?php
// DAN 2012
// Редактируем "Все авторы"

defined('AUTH') or die('Restricted access');

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
	global $site, $d, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected, $menu_id, $menu_parent, $num, $prv; 
	

	// определяем содержимое меню	
	$num_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'quote' AND `p1` = 'authors' LIMIT 1";
	
	$num = mysql_query($num_sql) or die ("Невозможно сделать выборку из таблицы - 3");	
	
	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];	
		$menu_name = $m['name'];
		$menu_pub = $m['pub'];
		if ($menu_pub == "1"){$m_pub = "checked";} else{$m_pub = "";} // устанавливаем признак публикации
		$menu_parent = $m['parent'];			
		$menu_ordering = $m['ordering'];
	endwhile;	
	
	// выводим содержимое страницы	
	$asql = mysql_query("SELECT * FROM `com_quote_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
	while($m = mysql_fetch_array($asql)):
		$quote_settings_id = $m['id'];
		$quote_settings_name = $m['name'];	
		$quote_settings_parametr = $m['parametr'];	
		
		if ($quote_settings_id == 3){$authors_title = $quote_settings_name; $authors_description = $quote_settings_parametr;} // для главной страницы				 						
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
	
	
	<div id="main-top">ВСЕ АВТОРЫ: Редактировать</div>

	<form method="POST" action="http://'.$site.'/admin/com/quote/authorsupdate">	
	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td>&nbsp;</td>		
			<td height="25">Название</td>
			<td><input type="text" name="title" size="50" value="'.$authors_title.'" required ></td>
		</tr>	
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Наименование пункта меню</td>
			<td><input type="text" name="menuname" size="20" value="'.$menu_name.'" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать пункт меню</td>
			<td><input type="checkbox" name="menupub" value="1" '.$m_pub.'/></td>
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
			<td >&nbsp;</td>			
			<td height="25">Порядок расположения</td>
			<td><input type="number" min="0" max="1000" name="menuordering" value="'.$menu_ordering.'" size="5"></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
	</table>

	<textarea name="editor1">'.$authors_description.'</textarea>
	
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