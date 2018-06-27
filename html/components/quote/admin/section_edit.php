<?php
// DAN 2012
// Редактируем раздел

defined('AUTH') or die('Restricted access');

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


if ($section_id != ""){ // проверка - если id раздела значение не пустое
$num = mysql_query("SELECT * FROM `com_quote_section` WHERE `id` = '$section_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1"); // проверка существования записи
$prv = mysql_num_rows($num); 
}
else {	
	$prv = 0;
}

function a_com()
{ 
	global $site, $d, $section_id, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected, $menu_id, $menu_parent, $num, $prv; 
	
	// $prv = 0 нет такой страницы $prv = 1 страница есть
	if ($prv == 1)
	{	
	
		while($a = mysql_fetch_array($num)):				
			$section_id = $a['id'];
			$section_pub = $a['pub'];
			if ($section_pub == "1"){$s_pub = "checked";} else{$s_pub = "";} // устанавливаем признак публикации	
			
			$section_title = $a['title'];
			$section_description = $a['description'];			
			
			$sorting = $a['sorting'];
			if ($sorting == 'date'){$sortingdate = 'selected';} else {$sortingdate = '';} // сортировка - "по дате"	
			if ($sorting == 'rating'){$sortingvote = 'selected';} else {$sortingvote = '';} // "по рейтингу"			
			if ($sorting == 'alphabet'){$sortingalphabet = 'selected';} else {$sortingalphabet = '';} // "по алфавиту"
			if ($sorting == 'order'){$sortingorder = 'selected';} else {$sortingorder = '';} // сортировка - "по порядку"
				
			$display_sorting = $a['display_sorting'];
			if ($display_sorting == 1){$display_sorting_check = 'checked';} else {$display_sorting_check = '';}

			$display_date = $a['display_date'];
			if ($display_date == 1){$display_date_check = 'checked';} else {$display_date_check = '';} 	
			
			$display_vote = $a['display_vote'];
			if ($display_vote == 1){$display_vote_check = 'checked';} else {$display_vote_check = '';} 	

			$tag_title = $a['tag_title'];
			$tag_description = $a['tag_description'];
			
		endwhile;	
	
	// определяем содержимое меню	
	$num_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `id_com` = '$section_id' AND `component` = 'quote' AND `p1` = 'section' LIMIT 1";
	
	$num = mysql_query($num_sql) or die ("Невозможно сделать выборку из таблицы - 3");	
	
	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];	
		$menu_name = $m['name'];
		$menu_pub = $m['pub'];
			if ($menu_pub == "1"){$m_pub = "checked";} else{$m_pub = "";} // устанавливаем признак публикации
		$menu_parent = $m['parent'];			
		$menu_ordering = $m['ordering'];
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
	
	
	<div id="main-top">ЦИТАТЫ: Редактировать раздел</div>

	<form method="POST" action="http://'.$site.'/admin/com/quote/sectionupdate">	
	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td>&nbsp;</td>		
			<td height="25">Название раздела</td>
			<td><input type="text" name="title" size="50" value="'.$section_title.'" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать раздел</td>
			<td><input type="checkbox" name="sectionpub" value="1" '.$s_pub.'/></td>
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
			<td><input type="number" min="0" max="1000000" name="menuordering" value="'.$menu_ordering.'" size="5"></td>
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
	} // конец проверки $prv = 0 нет такой страницы $prv = 1 страница есть
	else {echo '<div id="main-top">Нет такой страницы!</div>';}
} // конец функции


?>