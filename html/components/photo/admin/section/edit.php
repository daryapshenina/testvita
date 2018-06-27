<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/photo/admin/section/tmp/edit.css');


if($d[4] == 'edit')
{
	$section_id = intval($d[5]);

	$stmt_select = $db->prepare("SELECT id, name, menu_type, pub, parent, ordering FROM menu WHERE id_com = :section_id AND component = 'photo' AND (p1 = 'section' OR p1 = 'all') ORDER BY id LIMIT 1");
	$stmt_select->execute(array('section_id' => $section_id));
	$m = $stmt_select->fetch();
}
else{$m['parent'] = 0;}


$head->addCode('
<script language="JavaScript">
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

		req.open(\'GET\', \'/administrator/modules/menu_tree.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$m['parent'].', true);
		req.send(null);
		document.getElementById("menu_parent").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
	}
</script>
');



function a_com()
{
	global $root, $db, $domain, $d, $m, $photo_settings, $section_id, $url_arr;

	$sef = '';
	$photo_type_arr = array_fill(1, 3, '');


	if($d[4] == 'edit')
	{
		$title = 'Редактировать раздел';
		// если есть в массиве ЧПУ - заменяем
		$p_qs = 'photo/section/'.$section_id;
		if(isset($url_arr[$p_qs]) && $url_arr[$p_qs] != ''){$sef = $url_arr[$p_qs];}
		$act = 'update';

		if(!isset($m['id'])){$m['id'] = 0;}
		if(!isset($m['parent'])){$m['parent'] = 0;}

		$menu_top_selected = $menu_left_selected = '';

		if($m['menu_type'] == 'top'){$menu_top_selected = 'selected';}
		if($m['menu_type'] == 'left'){$menu_left_selected = 'selected';}

		if($m['pub'] == 1){$section_pub_checked = 'checked';}else{$section_pub_checked = '';}

		$stmt_section = $db->prepare("SELECT * FROM com_photo_section WHERE id = :id LIMIT 1");
		$stmt_section ->execute(array('id' => $section_id));
		$section = $stmt_section ->fetch();


		if($section['pub'] == 1){$title_pub_checked = 'checked';}else{$title_pub_checked = '';}

		$photo_type_arr[$section['type']] = 'selected';
	}
	else
	{
		$title = 'Добавить раздел';
		$section_id = '';
		$act = 'insert';

		$section['title'] = '';
		$section['tag_title'] = '';
		$section['tag_description'] = '';
		$section['text_top'] = '';
		$section['text_bottom'] = '';

		$menu_top_selected = '';
		$menu_left_selected = 'selected';
		$title_pub_checked = 'checked';
		$section_pub_checked = 'checked';
		$photo_type_arr[1] = 'selected';

		$m['id'] = 0;
		$m['name'] = '';
		$m['parent'] = 0;
		$m['ordering'] = 1;
	}


	echo '<h1>'.$title.'</h1>
	<form method="POST" action="/admin/com/photo/section/'.$act.'">
	<table class="admin_table_2">
		<tr>
			<td style="width:150px;">Наименование раздела</td>
			<td><input class="input section_checkbox" id="title_pub" name="title_pub" type="checkbox" '.$title_pub_checked.' value="1"><label for="title_pub" class="section_label"></label><input type="text" class="input section_input" name="title" size="50" value="'.$section['title'].'" required></td>
		</tr>
		<tr>
			<td>Пункт меню</td>
			<td><input class="input section_checkbox" id="menu_pub" name="menu_pub" type="checkbox" '.$section_pub_checked.' value="1"><label for="menu_pub" class="section_label"></label><input type="text" class="input section_input" name="menu" size="20" value="'.$m['name'].'" required></td>
		</tr>
		<tr>
			<td>Тип фотогалереи</td>
			<td>
				<select class="input" name="photo_type">
					<option '.$photo_type_arr['1'].' value="1">Стандартная</option>
					<option '.$photo_type_arr['2'].' value="2">Изображение с всплывающим описанием в квадрате</option>
					<option '.$photo_type_arr['3'].' value="3">Изображение с затемнением снизу</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Тип меню</td>
			<td>
				<select onChange="menu_type_select('.$m['id'].')" class="input" name="menu_type" id="menu_type">
					<option '.$menu_top_selected.' id="menu_type_top" value="top">Верхнее меню</option>
					<option '.$menu_left_selected.' id="menu_type_left" value="left">Левое меню</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Родительский пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
			<td><div id="menu_parent"></div></td>
		</tr>
		<tr>
			<td>Порядок расположения</td>
			<td><input class="input" name="ordering" type="number" value="'.$m['ordering'].'" style="width: 80px;"></td>
		</tr>
	</table>
	<div>&nbsp;</div>
	<div id="leftaccordion" class="left_list seo_fon">
		<div class="left_head section_head_seo">SEO</div>
		<div class="left_body">
			<table class="main_tab">
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">&nbsp;</td>
					<td width="420">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название раздела – название сайта</b>, например: <b>Браслеты и кольца - интернет-магазин бижутерии</b></span></div></td>
					<td>
						<textarea rows="2" name="tag_title" class="w400">'.$section['tag_title'].'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
					<td>
						<textarea rows="5" name="tag_description" class="w400">'.$section['tag_description'].'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Например, для раздела интернет-магазина <b>Косметика</b> можно указать такой адрес: <b>http://site.ru/cosmetics</b>, для страницы контакты: <b>http://site.ru/contacts</b><br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/photo/section/777</b>, где 777 - номер раздела</span></div></td>
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
	<div class="section_description">Текст сверху раздела:</div>
	<input type="hidden" name="section_id" value="'.$section_id.'"/>
	<textarea name="editor1">'.$section['text_top'].'</textarea>
	<div class="section_description">Текст снизу раздела:</div>
	<input type="hidden" name="section_id" value="'.$section_id.'"/>
	<textarea name="editor2">'.$section['text_bottom'].'</textarea>

	<script type="text/javascript">
		menu_type_select('.$m['id'].');
		CKEDITOR.replace( \'editor1\',
			{
				height: \'300px\',
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
			});
		CKEDITOR.replace( \'editor2\',
			{
				height: \'300px\',
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
			});
	</script>
	<br/>
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
	<br/>
	&nbsp;
	</form>
	';
}


?>
