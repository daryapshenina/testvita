<?php
// DAN обновлено - январь 2014
// Редактируем статью

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $url_arr, $item_id, $item_section_id; 
	
	// находим родительский раздел
	$itemsection = mysql_query("SELECT * FROM com_article_item WHERE id = $item_id LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
			
	while($n = mysql_fetch_array($itemsection)):
		$item_section_id = $n['section'];
		$item_pub = $n['pub'];
		$item_ordering = $n['ordering']; 	
		$item_title = $n['title']; 
		$item_introtext = $n['introtext'];	
		$item_fulltext = $n['fulltext'];
		$item_views = $n['views'];
		$item_rating = $n['rating'];		
		$item_vote_plus = $n['vote_plus'];
		$item_vote_minus = $n['vote_minus'];		
		$item_cdate = $n['cdate'];
		$item_lastip = $n['lastip'];
		$tag_title = $n['tag_title'];
		$tag_description = $n['tag_description'];			
	endwhile; 	
	
	// Условия
	if($item_pub === "1"){$checked = "checked";} else {$checked = "";}
	
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
	
	
	<script language="JavaScript">
		function rating() 
		{
			vote_plus = Number(document.getElementById("vote_plus").value);
			vote_minus = Number(document.getElementById("vote_minus").value);
			vote_sum = vote_plus + vote_minus;
			if (vote_plus > 0 || vote_sum > 0)
			{
				vote_percent_plus = 100*vote_plus/vote_sum;
				vote_percent_plus = Math.round(vote_percent_plus); 
				vote_percent_minus = 100 - vote_percent_plus; 
				vote_rating = vote_percent_plus + \'% за\';
			}
			else 
			{
				vote_percent_plus = 100*vote_plus/vote_sum;
				vote_percent_plus = Math.round(vote_percent_plus); 
				vote_percent_minus = 100 - vote_percent_plus; 
				vote_rating = \'Нет голосов\';
			}

			document.getElementById("rating").innerHTML = \'<div class="votingbar"><div class="vote_bar_plus" style="width: \' + vote_percent_plus +\'%"; ></div><div class="vote_bar_minus" style="width: \' + vote_percent_minus +\'%"; ></div></div>\' + vote_plus  +  \' за, \' + vote_minus + \' против (\' + vote_rating + \')\';
		}
	</script>
	
	<h1>АРХИВ СТАТЕЙ: Редактировать статью</h1>

	<form enctype="multipart/form-data" method="POST" action="/admin/com/article/itemupdate/'.$item_id.'/">	
	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Название статьи <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название статьи</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value="'.$item_title.'" required ></td>
		</tr>			
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Опубликовать статью <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать статью</em>Галочка стоит - статья отображается. Нет галочки - статья не отображается.</span></div></td>
			<td><input type="checkbox" name="pub" value="1" '.$checked.'></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Просмотров</td>
			<td><input type="number" min="0" max="1000000" name="views" size="10" value="'.$item_views.'"></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Рейтинг</td>
			<td><div id="rating"></div></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Голосов за</td>
			<td><input onClick="rating()" id="vote_plus" type="number" min="0" max="1000000" name="vote_plus" size="10" value="'.$item_vote_plus.'"></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Голосов против</td>
			<td><input onClick="rating()" id="vote_minus" type="number" min="0" max="1000000" name="vote_minus" size="10" value="'.$item_vote_minus.'"></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Дата</td>
			<td><input type="text" name="cdate" size="20" value="'.$item_cdate.'" required ></td>
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
			<td><input type="number" min="0" max="1000000" name="ordering" size="3" value="'.$item_ordering.'"></td>
		</tr>	
		<tr>
			<td>&nbsp;</td>			
			<td height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>			
	</table>
		';

	// если есть в массиве ЧПУ - заменяем
	$p_qs = 'article/item/'.$item_id;
	
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
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок статьи</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название статьи – название сайта</b></span></div></td>
					<td>
						<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание статьи</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание статьи для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
					<td>
						<textarea rows="5" name="tag_description" class="w400">'.$tag_description.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>			
					<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Например, для раздела интернет-магазина <b>Косметика</b> можно указать такой адрес: <b>http://site.ru/cosmetics</b>, для страницы контакты: <b>http://site.ru/contacts</b><br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/shop/section/777</b>, где 777 - номер раздела</span></div></td>
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
	<table class="main_tab">
		<tr>
			<td>&nbsp;</td>			
			<td height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Вводный текст</td>
			<td>&nbsp;</td>
		</tr>		
	</table>
		<textarea name="editor1">'.$item_introtext.'</textarea>
		
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor1\',
				{	        
					height: \'200px\',
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
		<textarea name="editor2">'.$item_fulltext.'</textarea>
		
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
	<script language="JavaScript">
		rating();
	</script>	
	';

} // конец функции

// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======

function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
global $site, $item_section_id;
global $lvl; // уровень
$lvl++;

$numtree = mysql_query("SELECT * FROM com_article_section WHERE parent = $i ORDER BY ordering ASC") or die ("Невозможно сделать выборку из таблицы - 2");

	$otstup = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) {
	
	while($m = mysql_fetch_array($numtree)):
		$section_id = $m['id'];	
		$section_pub = $m['pub'];	
		$section_parent = $m['parent'];
		$section_ordering = $m['ordering'];
		$section_title = $m['title'];
		$section_description = $m['description'];

   // устанавливаем состояние выбрано для родительского раздела
	if ($section_id == $item_section_id){$selected = "selected";} else {$selected = "";}
			
	echo'<option value="'.$section_id.'" '.$selected.' >'.$otstup.$section_title.'</option>';		
		
//		tree($section_id, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским
//		$lvl--;
		
	endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree

?>