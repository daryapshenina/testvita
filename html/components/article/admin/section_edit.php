<?php
// DAN обновлено - январь 2014
// Редактируем раздел

defined('AUTH') or die('Restricted access');

$section_id = intval($admin_d4);


if ($section_id != ""){ // проверка - если id раздела значение не пустое
$num = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$section_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1"); // проверка существования записи
$prv = mysql_num_rows($num);
}
else {
	$prv = 0;
}

function a_com()
{
	global $site, $url_arr, $d, $section_id, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected, $menu_id, $menu_parent, $num, $prv;

	// $prv = 0 нет такой страницы $prv = 1 страница есть
	if ($prv == 1)
	{

		while($a = mysql_fetch_array($num)):
			$section_id = $a['id'];
			$section_pub = $a['pub'];
			if ($section_pub == "1"){$s_pub = "checked";} else{$s_pub = "";} // устанавливаем признак публикации

			$section_title = $a['title'];
			$section_description = $a['description'];

			$tag_title = $a['tag_title'];
			$tag_description = $a['tag_description'];

			$display_subsection = $a['display_subsection'];
			if ($display_subsection == 1){$display_subsection_check = 'checked';} else {$display_subsection_check = '';}

			$display_sub_item = $a['display_sub_item'];
			if ($display_sub_item == 1){$display_sub_item_check = 'checked';} else {$display_sub_item_check = '';}

			$sorting = $a['sorting'];
			if ($sorting == 'date'){$sortingdate = 'selected';} else {$sortingdate = '';} // сортировка - "по дате"
			if ($sorting == 'rating'){$sortingvote = 'selected';} else {$sortingvote = '';} // "по рейтингу"
			if ($sorting == 'views'){$sortingviews = 'selected';} else {$sortingviews = '';} // "по просмотрам"
			if ($sorting == 'alphabet'){$sortingalphabet = 'selected';} else {$sortingalphabet = '';} // "по алфавиту"
			if ($sorting == 'order'){$sortingorder = 'selected';} else {$sortingorder = '';} // сортировка - "по порядку"

			$display_sorting = $a['display_sorting'];
			if ($display_sorting == 1){$display_sorting_check = 'checked';} else {$display_sorting_check = '';}

			$display_date = $a['display_date'];
			if ($display_date == 1){$display_date_check = 'checked';} else {$display_date_check = '';}

			$display_views = $a['display_views'];
			if ($display_views == 1){$display_views_check = 'checked';} else {$display_views_check = '';}

			$display_vote = $a['display_vote'];
			if ($display_vote == 1){$display_vote_check = 'checked';} else {$display_vote_check = '';}

			$show_details = $a['show_details'];
			if ($show_details == 1){$show_details_check = 'checked';} else {$show_details_check = '';}

			$title_hyperlink = $a['title_hyperlink'];
			if ($title_hyperlink == 1){$title_hyperlink_check = 'checked';} else {$title_hyperlink_check = '';}

			$text_output = $a['text_output'];
			if ($text_output == 1){$text_output_check_1 = 'checked';} else {$text_output_check_1 = '';}
			if ($text_output == 2){$text_output_check_2 = 'checked';} else {$text_output_check_2 = '';}

			$comments = $a['comments'];
			if($comments == 1){$comments_output = 'checked';} else {$comments_output = '';}
		endwhile;

	// определяем содержимое меню
	$num_sql = "SELECT * FROM `menu` WHERE `id_com` = '$section_id' AND `component` = 'article' AND `p1` = 'section' LIMIT 1";

	$num = mysql_query($num_sql) or die ("Невозможно сделать выборку из таблицы - 3");

	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];
		$menu_type = $m['menu_type'];
		$menu_name = $m['name'];
		$menu_pub = $m['pub'];
		$menu_parent = $m['parent'];
		$menu_ordering = $m['ordering'];
	endwhile;

	if ($menu_pub == "1"){$m_pub = "checked";} else{$m_pub = "";} // устанавливаем признак публикации

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


	<h1>СТАТЬИ: Редактировать раздел</h1>

	<form method="POST" action="/admin/com/article/sectionupdate">

	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td height="25">Название раздела <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название раздела</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value="'.$section_title.'" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td height="25">Опубликовать раздел <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - раздел отображается. Нет галочки - раздел не отображается, пункт меню тоже не отображается.</span></div</td>
			<td><input type="checkbox" name="sectionpub" value="1" '.$s_pub.'/></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td height="25">Наименование пункта меню</td>
			<td><input type="text" name="menuname" size="20" value="'.$menu_name.'" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td height="25">Опубликовать пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать пункт меню</em> Пункт меню может быть опубликован только в том случае, если опубликован раздел. Однако раздел может быть опубликован вне зависимости от того - опубликован или нет пункт меню. Таким образом можно создавать разделы разделы архива статей, без публикации пунктов меню</span></div></td>
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
			<td width="200" height="25">Родительский пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
			<td><div id="menu_parent"></div></td>
		</tr>
		<tr>
			<td >&nbsp;</td>
			<td height="25">Порядок расположения</td>
			<td><input type="number" min="0" max="1000" name="menuordering" value="'.$menu_ordering.'" size="3"></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	';


	// если есть в массиве ЧПУ - заменяем
	$p_qs = 'article/section/'.$section_id;

	if(isset($url_arr[$p_qs]) && $url_arr[$p_qs] != '')
	{
		$sef = $url_arr[$p_qs];
	}
	else
	{
		$sef = '';
	}


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
						<input type="checkbox" name="display_subsection" value="1" '.$display_subsection_check.' />
						<span class="gray-1"> - показать заголовки подразделов в содержимом раздела</span>
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">Показать статьи подразделов</td>
					<td>
						<input type="checkbox" name="display_sub_item" value="1" '.$display_sub_item_check.'/>
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
							<option value="date" '.$sortingdate.'>дате</option>
							<option value="rating" '.$sortingvote.'>рейтингу</option>
							<option value="views" '.$sortingviews.'>количеству просмотров</option>
							<option value="alphabet" '.$sortingalphabet.'>алфавиту</option>
							<option value="order" '.$sortingorder.'>порядку</option>
						</select>
						<span class="gray-1"> - сортировка по умолчанию </span>
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">Показать сортировку</td>
					<td>
						<input type="checkbox" name="display_sorting" value="1" '.$display_sorting_check.' />
						<span class="gray-1"> - позволяет пользователю выбрать вид отображения статей в разделе</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать дату</td>
					<td>
						<input type="checkbox" name="display_date" value="1" '.$display_date_check.' />
						<span class="gray-1"> - совместно с сортировкой по дате - отображается как архив новостей</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать рейтинг <br/>(голоса за и против)</td>
					<td>
						<input type="checkbox" name="display_vote" value="1" '.$display_vote_check.' />
						<span class="gray-1"> - одновременно отображает количество голосов и систему голосования</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать количество просмотров</td>
					<td>
						<input type="checkbox" name="display_views" value="1" '.$display_views_check.'/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Показать "подробнее"</td>
					<td>
						<input type="checkbox" name="show_details" value="1" '.$show_details_check.'/>
						<span class="gray-1"> - не рекомендуется; хуже для продвижения</span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td height="25">Заголовок гиперссылкой</td>
					<td>
						<input type="checkbox" name="title_hyperlink" value="1" '.$title_hyperlink_check.' />
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
						<input type="radio" value="1" name="text_output" '.$text_output_check_1.' >
						вводный + основной текст<span class="gray-1"> - при открытии статьи выводится вводный текст, затем основной текст</span><br/>
						<input type="radio" value="2" name="text_output" '.$text_output_check_2.'>
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
						<input type="checkbox" name="comments" value="1" '.$comments_output.' />
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
	<textarea name="editor1">'.$section_description.'</textarea>

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
	} // конец проверки $prv = 0 нет такой страницы $prv = 1 страница есть
	else {echo '<div id="main-top">Нет такой страницы!</div>';}
} // конец функции


?>
