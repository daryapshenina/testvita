<?php
// DAN обновлено январь 2014
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');

// определяем тип меню
$menu_t = intval($admin_d5);

// id_com
$id_com = intval($admin_d4);

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
	global $site, $d, $ordering, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected;


	// определяем содержимое меню
	$stmt = $db->prepare("SELECT * FROM `menu` WHERE `menu_type` = :menu_type: AND `id_com` = :id_com AND `component` = 'shop' AND `p1` = 'section' LIMIT 1");
	$stmt->execute(array(
		'menu_type' => $menu_type,
		'id_com' => $section_id
	));

	$m = $stmt->fetch();
	$menu_id = $m['id'];
	$menu_name = $m['name'];
	$menu_parent = $m['parent'];
	$menu_ordering = $m['ordering'];

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
	';



	echo '
	<h1>Создать страницу сайта</h1>

	<form method="POST" action="/admin/com/page/insert/">

	<table class="main_tab">
		<tr>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="25">Заголовок страницы <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок основного содержимого страницы</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50"></td>
		</tr>
		<tr>
			<td height="25">Наименование пункта меню</td>
			<td><input type="text" name="menu" size="20" required ></td>
		</tr>
		<tr>
			<td height="25">Опубликовать страницу <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать страницу</em>Галочка стоит - страница отображается. Нет галочки - страница не отображается с внешней стороны сайта.</span></div></td>
			<td><input type="checkbox" name="pub" value="1" checked></td>
		</tr>
		<tr>
			<td height="25">Тип меню</td>
			<td>
				<select onChange="menu_type_select('.$menu_id.')" name="menu_type" id="menu_type">
					<option '.$menu_top_selected.' id="menu_type_top" value="menu_top">Верхнее меню</option>
					<option '.$menu_left_selected.' id="menu_type_left" value="menu_left">Левое меню</option>
				</select>
			</td>
		</tr>
		<tr>
			<td height="25">Родительский пункт меню <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать подраздел (дочерний пункт меню), вы должны выбрать раздел (родительский пункт меню) для данного подраздела. Если подраздел не нужно создавать – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
			<td><div id="menu_parent"></div></td>
		</tr>
		<tr>
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
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>заголовок содержимого страницы – название сайта</b>, например: <b>Строительство коттеджей и загородных домов - Строймонтаж</b></span></div></td>
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
					<td height="25">ЧПУ URL <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Человеко - понятный URL</em>Для каждой страницы можно прописать свой адрес вручную. Например, для фотогалереи можно указать такой адрес: <b>http://site.ru/foto</b>, для страницы контакты: <b>http://site.ru/contacts</b><br><br>Если поле оставить пустым, система управления сгенерирует адрес в таком формате <b>http://site.ru/page/777</b>, где 777 - номер страницы</span></div></td>
					<td>
						<textarea rows="1" name="sef" id="sef" class="w400" onkeyup="url_ajax()"></textarea>
					</td>
					<td style="vertical-align: middle;"><div id="url_status"></div></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td height="25">&nbsp;</td>
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
					<td><input type="checkbox" name="access" value="1"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td height="25">Пароль:</td>
					<td><input type="text" name="password" size="20"><span class="gray-1"> - только цифры и английские буквы от 4 до 20 символов</span></td>
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
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>


	<textarea name="editor1"></textarea>

	<script type="text/javascript">
		CKEDITOR.replace( \'editor1\',
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
		menu_type_select('.$menu_id.');
	</script>
	';

} // конец функции


?>