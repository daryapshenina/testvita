<?php
defined('AUTH') or die('Restricted access');

include_once($root."/components/shop/classes/classFilter.php");

$head->addFile('/components/shop/admin/section/section.css');
$head->addFile('/js/drag_drop/drag_drop.js');
$head->addFile('/components/shop/admin/section/section.js');

$section_id = $SITE->d[5];

if ($section_id != "") // проверка - если id раздела значение не пустое
{
	$stmt_section = $db->prepare("SELECT id FROM com_shop_section WHERE id = :id");
	$stmt_section->execute(array('id' => $section_id));
	$prv = $stmt_section->rowCount();
}
else {
	$prv = 0;
}

function a_com()
{
	global $db, $url_arr, $d, $section_id, $num, $prv;

	// $prv = 0 нет такой страницы $prv = 1 страница есть
	if ($prv == 1)
	{

	// определяем содержимое раздела
	$stmt_section = $db->prepare("SELECT * FROM com_shop_section WHERE id = :id");
	$stmt_section->execute(array('id' => $section_id));
	$section = $stmt_section->fetch();

	if ($section['pub'] == "1"){$s_pub = "checked";} else{$s_pub = "";} // устанавливаем признак публикации

	// определяем содержимое меню
	$stmt_menu = $db->prepare("SELECT * FROM menu WHERE id_com = :id_com AND component = 'shop' AND p1 = 'section' LIMIT 1");
	$stmt_menu->execute(array('id_com' => $section_id));	

	$menu = $stmt_menu->fetch();

	// сбрасываем значения
	$menu_top_selected = "";
	$menu_left_selected = "";

	if ($menu['pub'] == "1"){$m_pub = "checked";} else{$m_pub = "";} // устанавливаем признак публикации
	if ($menu['menu_type'] == "top") $menu_top_selected = "selected";
	if ($menu['menu_type'] == "left") $menu_left_selected = "selected";

	// Характеристики
	$filter = new classFilter;

	echo '
	<script language="JavaScript">

	select = \''.$filter->getSelect().'\';

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
		req.open(\'GET\', \'/administrator/modules/menu_parent.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$menu['parent'].', true);
		req.send(null);
		document.getElementById("menu_parent").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
	}
	/* ------- / AJAX - загрузка ------- */

	</script>


	<h1>ИНТЕРНЕТ - МАГАЗИН: Редактировать раздел</h1>

	<form method="POST" action="/admin/com/shop/section/update">

	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td height="25">Название раздела <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название раздела</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value="'.$section['title'].'" required ></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td height="25">Опубликовать раздел <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - раздел отображается. Нет галочки - раздел не отображается, пункт меню тоже не отображается.</span></div></td>
			<td><input type="checkbox" name="sectionpub" value="1" '.$s_pub.'/></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Наименование пункта меню</td>
			<td><input type="text" name="menuname" size="20" value="'.$menu['name'].'" required ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td height="25">Опубликовать пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать пункт меню</em> Пункт меню может быть опубликован только в том случае, если опубликован раздел. Однако раздел может быть опубликован вне зависимости от того - опубликован или нет пункт меню. Таким образом можно создавать разделы интернет - магазина, без публикации пунктов меню</span></div></td>
			<td><input type="checkbox" name="menupub" value="1" '.$m_pub.'/></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Тип меню</td>
			<td>
				<select onChange="menu_type_select('.$menu['id'].')" name="menu_type" id="menu_type">
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
			<td><input type="number" min="0" max="100000" name="menuordering" value="'.$menu['ordering'].'" size="5"></td>
		</tr>
			<tr>
				<td>&nbsp;</td>
				<td height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		';

		// если есть в массиве ЧПУ - заменяем
		$p_qs = 'shop/section/'.$section['id'];

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
				<table class="main_tab">
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

		echo '
		<div id="leftaccordion" class="left_list seo_fon">
			<div class="left_head">Фильтры поиска по характеристикам товаров</div>
		<div class="left_body">
				<div id="char_add" class="button_green" style="margin-top:20px;">+ Добавить характеристику</div>
				<table class="main_tab filter_title_tab" style="border-spacing:0; width:100%;">
					<tr>
						<td class="filter_dnd">&nbsp;</td>
						<td class="filter_char">Фильтр по характеристике:
							<div class="help">
								<span class="tooltip">
									<img src="/administrator/tmp/images/question-50.png" alt="Помощь" />
									<em>Фильтры</em>
									Фильтры выводятся в разделе, над выводом товаров, пример:
									<br><br>
									<b>Цвет:</b>
									<select size="1" name="D1">
										<option value="Характеристика 1">белый</option>
										<option value="Характеристика 2">синий</option>
										<option value="Характеристика 3">красный</option>
									</select>
									<br>
									<b>Ширина:</b>
									<select size="1" name="D1">
										<option value="Характеристика 1">100</option>
										<option value="Характеристика 2">120</option>
										<option value="Характеристика 3">140</option>
										<option value="Характеристика 4">160</option>
										<option value="Характеристика 5">180</option>
									</select>
									<br>
									<b>Длина:</b><input type="text" name="T1" size="5" value="80">см.&nbsp; до
									<input type="text" name="T2" size="5" value="120">см.
									<br>
								</span>
							</div>
						</td>
						<td class="filter_unit" title="Единица измерения">Ед. изм.</td>
						<td class="filter_type">Тип данных</td>
						<td>Значения полей фильтра
							<div class="help">
								<span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" />
									<em>Значение полей фильтра</em>
									Для типа данных <i>строка</i> вводите значения фильтра  через точку с запятой, например: <b>белый;синий;красный</b>
									<br><br>
									для типа данных <i>число</i> введите значения фильтра  <b>от</b> <i>число</i> <b>до</b> <i>число</i>
								</span>
							</div>
						</td>
					</tr>
				</table>
				<div id="drag_trg">'.$filter->getFilter($section['id']).'</div>
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

	<input type="hidden" name="section_id" value="'.$section['id'].'"/>
	<textarea name="editor1">'.$section['description'].'</textarea>

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
		menu_type_select('.$menu['id'].');
	</script>
	';
	} // конец проверки $prv = 0 нет такой страницы $prv = 1 страница есть
	else {echo '<div id="main-top">Нет такой страницы!</div>';}
} // конец функции



?>
