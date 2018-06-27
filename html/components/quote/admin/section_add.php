<?php
// DAN 2012
// Добавляем новый раздел

defined('AUTH') or die('Restricted access');

// определяем тип меню
$section_id = intval($admin_d4);

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
	global $site, $section_id, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected; 
	
	// определяем содержимое меню	
	$num_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `id_com` = '$section_id' AND `component` = 'quote' AND `p1` = 'section' LIMIT 1";
	
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
	<div id="main-top">ЦИТАТЫ: Создать раздел</div>

	<form method="POST" action="http://'.$site.'/admin/com/quote/sectioninsert/">	
	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td>&nbsp;</td>		
			<td height="25">Название раздела</td>
			<td><input type="text" name="title" size="50" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать раздел</td>
			<td><input type="checkbox" name="sectionpub" value="1" checked /></td>
		</tr>		
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Наименование пункта меню</td>
			<td><input type="text" name="menuname" size="20" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать пункт меню</td>
			<td><input type="checkbox" name="menupub" value="1" checked /></td>
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
	<table class="main_tab">
		<tr>
			<td>&nbsp;</td>			
			<td height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>			
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Описание раздела:</td>
			<td>&nbsp;</td>
		</tr>		
	</table>
	<input type="hidden" name="section_id" value="'.$section_id.'"/>	
	<textarea name="editor1">'.$section_description.'</textarea>
	
	<script type="text/javascript">	
		CKEDITOR.replace( \'editor1\',
			{	        
				height: \'200px\',
				filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
			}); 	
	</script>
	<br/>
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