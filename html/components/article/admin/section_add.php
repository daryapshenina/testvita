<?php
// Добавляем новый раздел
defined('AUTH') or die('Restricted access');

// определяем тип меню
$section_id = intval($admin_d4);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------



function a_com()
{ 
	global $site, $section_id; 
	
	// сбрасываем значения
	$menu_top_selected = "";
	$menu_left_selected = "";
	
	// определяем содержимое меню	
	$num_sql = "SELECT * FROM `menu` WHERE `id_com` = '$section_id' AND `component` = 'article' AND `p1` = 'section' LIMIT 1";
	
	$num = mysql_query($num_sql) or die ("Невозможно сделать выборку из таблицы - 3");	
	
	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];	
		$menu_name = $m['name'];
		$menu_type = $m['menu_type'];		
		$menu_parent = $m['parent'];			
		$menu_ordering = $m['ordering'];
	endwhile;
	
	if ($menu_type == "top")
	{
		$menu_top_selected = "selected";	
	}
	if ($menu_type == "left")
	{
		$menu_left_selected = "selected";		
	}	
	
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
		req.open(\'GET\', \'/administrator/modules/menu_tree.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$menu_parent.', true);
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
	<h1>СТАТЬИ: Создать раздел</h1>
	<form method="POST" action="/admin/com/article/sectioninsert/">	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td>&nbsp;</td>		
			<td height="25">Название раздела <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название раздела</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать раздел <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - раздел отображается. Нет галочки - раздел не отображается, пункт меню тоже не отображается.</span></div></td>
			<td><input type="checkbox" name="sectionpub" value="1" checked /></td>
		</tr>		
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Наименование пункта меню</td>
			<td><input type="text" name="menuname" size="20" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Опубликовать пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать пункт меню</em> Пункт меню может быть опубликован только в том случае, если опубликован раздел. Однако раздел может быть опубликован вне зависимости от того - опубликован или нет пункт меню. Таким образом можно создавать разделы интернет - магазина, без публикации пунктов меню</span></div></td>
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
			<td width="200" height="25">Родительский пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
			<td><div id="menu_parent"></div></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
	</table>
	';
	
	
	
	echo '
	<div id="leftaccordion" class="left_list article_section_settings">
		<div class="left_head section_head_seo">SEO</div>  
		<div class="left_body left_list seo_fon-2">                 
			<table>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="170" height="25">&nbsp;</td>
					<td width="420">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название раздела – название сайта</b>, например: <b>Браслеты и кольца - интернет-магазин бижутерии</b></span></div></td>
					<td>
						<textarea rows="2" name="tag_title" class="w400"></textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
					<td>
						<textarea rows="5" name="tag_description" class="w400"></textarea>
					</td>
					<td>&nbsp;</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>			
					<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Например, для раздела интернет-магазина <b>Косметика</b> можно указать такой адрес: <b>http://site.ru/cosmetics</b>, для страницы контакты: <b>http://site.ru/contacts</b><br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/shop/section/777</b>, где 777 - номер раздела</span></div></td>
					<td>
						<textarea rows="1" name="sef" id="sef" class="w400" onkeyup="url_ajax()"></textarea>
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
		<div class="left_head section_head_settings">Настройки раздела</div>  
		<div class="left_body">                 
			<table>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">Показать подразделы</td>
					<td>
						<input type="checkbox" name="display_subsection" value="1" checked />
						<span class="gray-1"> - показать заголовки подразделов в содержимом раздела</span>
					</td>
				</tr>				
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">Показать статьи подразделов</td>
					<td>
						<input type="checkbox" name="display_sub_item" value="1" />
						<span class="gray-1"> - дополнительно выводятся статьи из подразделов в разделе<span>
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>				
				<tr>
					<td></td>
					<td height="25">Сортировать статьи по</td>
					<td>
						<select size="1" name="sorting">
							<option value="date">дате</option>
							<option value="rating">рейтингу</option>
							<option value="views">количеству просмотров</option>
							<option value="alphabet">алфавиту</option>							
							<option value="order">порядку</option>														
						</select>
						<span class="gray-1"> - сортировка по умолчанию </span>
					</td>
				</tr>				
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">Показать сортировку</td>
					<td>
						<input type="checkbox" name="display_sorting" value="1" checked />
						<span class="gray-1"> - позволяет пользователю выбрать вид отображения статей в разделе</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать дату</td>
					<td>
						<input type="checkbox" name="display_date" value="1" checked />
						<span class="gray-1"> - совместно с сортировкой по дате - отображается как архив новостей</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать количество просмотров</td>
					<td>
						<input type="checkbox" name="display_views" value="1" checked/>
					</td>
				</tr>	
				<tr>
					<td></td>
					<td height="25">Показать количество голосов</td>
					<td>
						<input type="checkbox" name="display_vote" value="1" checked />
						<span class="gray-1"> - одновременно отображает количество голосов и систему голосования</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать "подробнее"</td>
					<td>
						<input type="checkbox" name="show_details" value="1" />
						<span class="gray-1"> - не рекомендуется; хуже для продвижения</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Заголовок гиперссылкой</td>
					<td>
						<input type="checkbox" name="title_hyperlink" value="1" checked />
						<span class="gray-1"> - рекомендуется выводить заголовки статей в разделе гиперссылками - так лучше для продвижения сайта</span>
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>				
				<tr>
					<td></td>
					<td height="25">Вывод статьи</td>
					<td>
						<input type="radio" value="1" name="text_output" checked />
						вводный + основной текст<span class="gray-1"> - при открытии статьи выводится вводный текст, затем основной текст</span><br/>
						<input type="radio" value="2" name="text_output" />
						только основной текст<span class="gray-1"> - при открытии статьи выводится только основной текст</span> 					
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>	
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">Включить комментарии</td>
					<td>
						<input type="checkbox" name="comments" value="1" />
						<span class="gray-1"> - только при наличии расширения "Социальная сеть"</span>						
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>			
					<td width="200" height="25">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>				
			</table>
		</div>
	</div>		
	';
	
	
	
	echo'
	<table>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td>&nbsp;</td>			
			<td height="25">Описание раздела:</td>
			<td>&nbsp;</td>
		</tr>		
	</table>
	<input type="hidden" name="section_id" value="'.$section_id.'"/>	
	<textarea name="editor1"></textarea>
	
	<script type="text/javascript">	
		CKEDITOR.replace( \'editor1\',
			{	        
				height: \'200px\',
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
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