<?php
// DAN обновлено - январь 2014
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');

// определяем id раздела
$section_id = intval($admin_d4);

// определяем тип меню
$menu_t = intval($admin_d5);	

// Перед тем как добавить товар - проверяем - есть ли разделы 
$number_sections = mysql_query("SELECT * FROM com_article_section") or die ("Невозможно сделать выборку из таблицы - 1");
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
	function a_com()
	{ 
		global $site, $section_id_parent, $section_id_ordering; 	
		
	
	echo '
	<script language="JavaScript"> 
	
	/* ------- AJAX - загрузка ------- */
	
	function getXmlHttp()
	{
		var xmlhttp;
		try 
		{
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch (e) 
		{
			try 
			{
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} 
			catch (E) 
			{
				xmlhttp = false;
			}
		}
		if (!xmlhttp && typeof XMLHttpRequest!="undefined") 
		{
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
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
	';		
		
		
		echo '
		<h1>СТАТЬИ: Добавить статью</h1>
	
		<form enctype="multipart/form-data" method="POST" action="/admin/com/article/iteminsert/">	
		
		<table class="main_tab">
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>	
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Название статьи <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название статьи</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
				<td><input type="text" name="title" size="50" required ></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать статью <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать статью</em>Галочка стоит - статья отображается. Нет галочки - статья не отображается.</span></div></td>
				<td><input type="checkbox" name="pub" value="1" checked></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Категория</td>
				<td>
				<select size="1" name="section">';
				tree(0,0);
				echo'
				</select>
				</td>
			</tr>				
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Порядок размещения статьи</td>
				<td><input type="number" min="0" max="1000000" name="ordering" size="3" value="'.$section_id_ordering.'"></td>
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
						<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок статьи</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название статьи – название сайта</b></span></div></td>
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
						<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой статьи можно прописать свой адрес вручную. <br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/article/item/777</b>, где 777 - номер статьи</span></div></td>
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
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Вводный текст:</td>
				<td>&nbsp;</td>
			</tr>		
		</table>
		<textarea name="editor1"></textarea>
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor1\',
				{	        
					height: \'150px\',
					filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
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
				<td width="200" height="25">Основной текст:</td>
				<td> </td>
			</tr>		
		</table>
		<textarea name="editor2"></textarea>
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor2\',
				{	        
					height: \'400px\',
					filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
				}); 	
		</script>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
		<br/>
		&nbsp;
		</form>	
		';
	
	} // конец функции

} // конец проверки существования разделов

// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======

function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
	global $site, $section_id;
	$lvl++;
	
	$numtree = mysql_query("SELECT * FROM `com_article_section` WHERE `parent` = '$i' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 3");
	
	$otstup = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",($lvl -1));  // отступ слева у пункта меню
		
	$result = mysql_num_rows($numtree);
		
	if ($result > 0) {
		
	while($m = mysql_fetch_array($numtree)):
		$section_id_tree = $m['id'];	
		$section_title_tree = $m['title'];

   // устанавливаем состояние выбрано для родительского раздела
	if ($section_id == $section_id_tree){$selected = "selected";} else {$selected = "";}
			
	echo'<option value="'.$section_id_tree.'" '.$selected.' >'.$section_title_tree.'</option>';		
			
//		tree($section_id, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским
//		$lvl--;
		
	endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree

?>