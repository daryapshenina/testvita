<?php
// DAN 2012
// Редактируем раздел

defined('AUTH') or die('Restricted access');

function a_com()
{
	global $site, $url_arr;

	// определяем содержимое формы
	$num = mysql_query("SELECT * FROM `com_form`") or die ("Невозможно сделать выборку из таблицы - 1");
	while($m = mysql_fetch_array($num)):
		$form_id = $m['id'];
		$form_name = $m['name'];
		$form_content = $m['content'];
		$form_pub = $m['pub'];

		if ($form_id == 1)
		{
			$tag_title = $m['tag_title'];
			$tag_description = $m['tag_description'];
		}

		// вводный текст формы обратной связи
		if ($form_id == 1)
		{
			$form_title = $form_name;
			$form_description = $form_content;
		}

		// тема
		if ($form_name == "theme")
		{
			$form_theme = $form_content;
			$form_theme_pub = $form_pub;

			if ($form_theme_pub == "1")
			{
				$form_theme_check = 'checked';
			}
			else
			{
				$form_theme_checkbox = '';
			}
		}

		// текст сообщения
		if ($form_name == "message")
		{
			$form_message = $form_content;
			$form_message_pub = $form_pub;

			if ($form_message_pub == "1")
			{
				$form_message_check = 'checked';
			}
			else
			{
				$form_message_checkbox = '';
			}
		}

		// ФИО
		if ($form_name == "fio")
		{
			$form_fio = $form_content;
			$form_fio_pub = $form_pub;

			if ($form_fio_pub == "1")
			{
				$form_fio_check = 'checked';
			}
			else
			{
				$form_fio_checkbox = '';
			}
		}

		// Контактные данные
		if ($form_name == "contact")
		{
			$form_contact = $form_content;
			$form_contact_pub = $form_pub;

			if ($form_contact_pub == "1")
			{
				$form_contact_check = 'checked';
			}
			else
			{
				$form_contact_checkbox = '';
			}
		}

		// Email
		if ($form_name == "email")
		{
			$form_email = $form_content;
			$form_email_pub = $form_pub;

			if ($form_email_pub == "1")
			{
				$form_email_check = 'checked';
			}
			else
			{
				$form_email_checkbox = '';
			}
		}

		// Телефон
		if ($form_name == "tel")
		{
			$form_tel = $form_content;
			$form_tel_pub = $form_pub;

			if ($form_tel_pub == "1")
			{
				$form_tel_check = 'checked';
			}
			else
			{
				$form_tel_checkbox = '';
			}
		}

		// Вложение
		if ($form_name == "file")
		{
			$form_file = $form_content;
			$form_file_pub = $form_pub;

			if($form_file_pub == 1){$form_email_file_checkbox = 'checked';}
		}
		else
		{
			$form_email_file_checkbox = '';
			$form_file = '';			
		}

		// Каптча
		if ($form_name == "captcha")
		{
			if ($form_pub == "1")
			{
				$form_captcha_check = 'checked';
			}
			else
			{
				$form_captcha_check = '';
			}
		}

	endwhile;

	// определяем содержимое меню
	$num = mysql_query("SELECT * FROM `menu` WHERE `component` = 'form'") or die ("Невозможно сделать выборку из таблицы - 2");

	$menu_top_selected = "";
	$menu_left_selected = "";

	while($m = mysql_fetch_array($num)):
		$menu_id = $m['id'];
		$menu_type = $m['menu_type'];
		$menu_name = $m['name'];
		$menu_pub = $m['pub'];
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


	// устанавливаем признак публикации
	if ($menu_pub == 1){$pub = "checked";} else{$pub = "";}

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



	<h1>ФОРМА ОБРАТНОЙ СВЯЗИ: Редактировать форму</h1>

	<form method="POST" action="/admin/com/form/update">

	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Заголовок формы <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок формы обратной связи</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value="'.$form_title.'" required ></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Наименование пункта меню</td>
			<td><input type="text" name="menu" size="20" value="'.$menu_name.'" required ></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Опубликовать форму</td>
			<td><input type="checkbox" name="pub" value="1" '.$pub.'/></td>
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
			<td width="200" height="25">Родительский пункт меню  <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Родительский пункт</em>Если вы хотите сделать чтобы форма обратной связи была внутри раздела - укажите родительский пункт меню раздела. Иначе – оставьте это поле пустым или выберите опцию -  <b>Нет родительского пункта</b></span></div></td>
			<td><div id="menu_parent"></div></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Порядок расположения</td>
			<td><input type="number" min="0" max="1000" name="ordering" value="'.$menu_ordering.'" size="5"></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	';

	// если есть в массиве ЧПУ - заменяем
	$p_qs = 'form/all/1';

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
					<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>заголовок формы – название сайта</b>, например: <b>Задать вопрос - Строймонтаж</b></span></div></td>
					<td>
						<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, релеантное, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
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
	</div>
	';



	echo'
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td height="25" colspan="2">Текст над формой обратной связи:</td>
		</tr>
	</table>
	<textarea name="editor1">'.$form_description.'</textarea>

	<script type="text/javascript">
		CKEDITOR.replace( \'editor1\',
			{
				height: \'100px\',
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
			});
	</script>
		<p align="center"><b>Поля ввода формы обратной связи:</b></p>
		<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="theme_pub" value="1" '.$form_theme_check.'></td>
			<td><input type="text" name="theme" size="40" value="'.$form_theme.'"> <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Поля ввода</em> Поля ввода можно влючить / отключить / переименовать</span></div></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="message_pub" value="1" '.$form_message_check.'></td>
			<td><input type="text" name="message" size="40" value="'.$form_message.'"> форма, обязательная для заполнения</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="fio_pub" value="1" '.$form_fio_check.'></td>
			<td><input type="text" name="fio" size="40" value="'.$form_fio.'"> форма, обязательная для заполнения</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="contact_pub" value="1" '.$form_contact_check.'></td>
			<td><input type="text" name="contact" size="40" value="'.$form_contact.'"> форма, обязательная для заполнения</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="email_pub" value="1" '.$form_email_check.'></td>
			<td>'.$form_email.'</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="tel_pub" value="1" '.$form_tel_check.'></td>
			<td>'.$form_tel.'</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="file_pub" value="1" '.$form_email_file_checkbox.'></td>
			<td><input type="text" name="file_text" size="40" value="'.$form_file.'" placeholder="Надпись над полем"> Вложение</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="20" height="25"><input type="checkbox" name="captcha_pub" value="1" '.$form_captcha_check.'></td>
			<td>Проверочный код</td>
		</tr>
	</table>

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
