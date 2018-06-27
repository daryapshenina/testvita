<?php
// редактируем страницу, определённую переменной $admin_d4 = $d[4];
defined('AUTH') or die('Restricted access');

// № пункта преобразуем в число
$id_component = intval($admin_d3); 

function a_com()
{ 
	global $site, $url_arr, $d, $id_component, $menu_parent, $menu_type, $menu_top_selected, $menu_left_selected, $update, $menu_id, $prv; 

	// вывод содержимого меню	
	$num = mysql_query("SELECT * FROM `menu` WHERE `component` = 'page' AND `id_com` = '$id_component' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");
	$prv = mysql_num_rows($num); 
	
	// страница есть
	if ($prv == 1)
	{	
		while($m = mysql_fetch_array($num)):
			$menu_id = $m['id'];
			$menu_type = $m['menu_type'];
			$menu_name = $m['name'];
			$menu_description = $m['description'];	
			$menu_pub = $m['pub'];	
			$menu_parent = $m['parent'];
			$menu_ordering = $m['ordering'];
			$menu_component = $m['component'];
		endwhile;
		
		// выводим содержимое страницы	
		$pgsql = mysql_query("SELECT * FROM `com_page` WHERE `id` = '$id_component'") or die ("Невозможно сделать выборку из таблицы - 3");

			while($p = mysql_fetch_array($pgsql)):
				$page_id = $p['id'];
				$page_title = $p['title'];	
				$page_text = $p['text'];
				$tag_title = $p['tag_title'];
				$tag_description = $p['tag_description'];
				$page_access = $p['access'];
				$page_psw = $p['psw'];
			endwhile;
			
		// устанавливаем признак публикации
		if ($menu_pub == 1){$pub = "checked";} else{$pub = "";} 
		
		// устанавливаем признак доступа по паролю
		if ($page_access == 1){$access_checkbox = "checked";} else{$access_checkbox = "";} 	
		
		if ($menu_type == "top")
		{
			$menu_top_selected = "selected";
		}
		if ($menu_type == "left")
		{
			$menu_left_selected = "selected";	
		}	
		

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
		
		
		
		<h1>Редактирование страницы</h1>

		<form method="POST" action="/admin/com/page/update">	
		
		<table class="main_tab">
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>	
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Заголовок страницы <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок основного содержимого страницы</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
				<td><input type="text" name="title" size="50" value = "'.$page_title.'"></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Наименование пункта меню</td>
				<td><input type="text" name="menu" size="20" value="'.$menu_name.'" required ></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать страницу <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - страница отображается. Нет галочки - страница не отображается с внешней стороны сайта.</span></div></td>
				<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
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
			';

			// если выбрано главное меню - выводим "Родительский пункт"
			
			echo '
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Родительский пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
				<td>
				<div id="menu_parent">
				<select size="10" name="parent">
				<option value="0">Нет родительского пункта</option>
			';	

			tree($menu_type,0,0); // выводим меню и подменю		
					
			echo '		
				</select> 
				</div>
				</td>
			</tr>
			';

			
			echo '		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Порядок расположения</td>
				<td><input type="number" min="0" max="1000" name="ordering" value="'.$menu_ordering.'" size="3"></td>
			</tr>			
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		';
		
		// если есть в массиве ЧПУ - заменяем
		$p_qs = 'page/'.$page_id;
		
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
						<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>заголовок содержимого страницы – название сайта</b>, например: <b>Строительство коттеджей и загородных домов - Строймонтаж</b></span></div></td>
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
						<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Например, для фотогалереи можно указать такой адрес: <b>http://site.ru/foto</b>, для страницы контакты: <b>http://site.ru/contacts</b><br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/page/777</b>, где 777 - номер страницы</span></div></td>
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
			
			<div class="left_head section_head_lock">Доступ</div>  
			<div class="left_body">                 
				<table>
					<tr>
						<td width="20">&nbsp;</td>			
						<td width="170" height="25">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">Доступ по паролю <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>При включении этой функции доступ к странице возможен только по паролю</span></div></td>
						<td><input type="checkbox" name="access" '.$access_checkbox.' value="1"></td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">Пароль:</td>
						<td><input type="text" name="password" value="'.$page_psw.'" size="20"><span class="gray-1"> - только цифры и английские буквы от 4 до 20 символов</span></td>
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
		<br/>
		<textarea  id="editor1" name="editor1">'.$page_text.'</textarea>
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor1\',
				{	        
					height: \'400px\',
					filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\'			
				}); 	
		</script>
		<br/>
		<input type="hidden" name="page_id" value="'.$page_id.'">	
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
		<br/>
		&nbsp;
		</form>	
		';
	} // конец проверки $prv = 0 нет такой страницы;    $prv = 1 страница есть
	else {echo '<div id="main-top">Нет такой страницы!</div>';}
} // конец функции



// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ =======

function tree($menu_type, $i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
	global $site, $menu_parent, $menu_id, $lvl;  //global - уровень
	$lvl++;
	
	$numtree_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `parent` = '$i' AND `parent` <> '$menu_id' AND `id` <> '$menu_id' ORDER BY ordering ASC";
	
	$numtree = mysql_query($numtree_sql) or die ("Невозможно сделать выборку из таблицы - 4");

	$otstup = str_repeat("&nbsp;-&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) {
	
	while($m = mysql_fetch_array($numtree)):
		$menu_id_tree = $m['id'];
		$menu_name_tree = $m['name'];
		
		if ($menu_parent == $menu_id_tree){$selected = "selected";} else {$selected = "";}
		
		// --- условия публикации ---
		if ($menu_pub == "1") {
			$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10">';
			$classmenu = 'menu_pub';
			}
			else {
			$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
			$classmenu = 'menu_unpub';
			}
			
			echo '<option value="'.$menu_id_tree.'" '.$selected.' >'.$otstup.$menu_name_tree.'</option>';			
		
		tree($menu_type, $menu_id_tree, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским
		$lvl--;
		
	endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree


?>