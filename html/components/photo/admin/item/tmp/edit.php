<?php

echo '
<h1>Изображение</h1>
<form enctype="multipart/form-data" method="POST" action="/admin/com/photo/item/'.$act.'">
	<table class="main_tab vam">
		<tr>
			<td style="width:20px;">&nbsp;</td>
			<td style="width:250px;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="height:42px;">Название изображения <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название изображения</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input class="input" type="text" name="title" size="50" value="'.$item['title'].'"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Опубликовать изображение <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать изображение</em>Галочка стоит - изображение отображается. Нет галочки - изображение не отображается с внешней стороны сайта.</span></td>
			<td><input class="input" id="photo_pub" type="checkbox" name="pub" value="1" '.$pub_checked.'><label for="photo_pub"></label></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td>'.$photo_out.'</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="height:35px;">Загрузить изображение</td>
			<td><input onchange="img_ajax(this.files);" type="file" name="photo"/><span id="img_status"></span><span style="padding-left:40px;color:#FF0000">Загружаемый размер изображения - не более 2 мегабайт.</style></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="height:25px;">Категория</td>
			<td>
			<select class="input" style="height:auto;" size="10" name="section" id="shop_select_tree_cat">';
			tree($item);
			echo'
			</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="height:25px;">Порядок размещения изображения</td>
			<td><input class="input" style="width:80px;" type="number" name="ordering" size="3" value="'.$item['ordering'].'"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Ссылка <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Ссылка</em>Если поле ссылки заполнено, то при нажатии на картинку происходит переход по адресу ссылки. Если поле пустое - то открывается увеличенная фотография</div></td>
			<td><input class="input" type="text" name="link" size="50" value="'.$item['link'].'"></td>
		</tr>		
		<tr>
			<td>&nbsp;</td>
			<td style="height:25px;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	'.$input_hidden.'
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
						<textarea rows="2" name="tag_title" class="w400">'.$item['tag_title'].'</textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>			
					<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание страницы</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br> Не перечисляёте здесь набор ключевых слов – это признак спама и дурного тона. Помните, большое количество слов в этом теге – тоже признак спама. Поисковики это не любят и занижают позиции. Пишите описание страницы для людей – понятное, логическое, интересное  с цифрами и фактами, 12  - 15 слов. <br><br> Если поле не будет заполнено система управления подставит в этот тег при выводе страницы описание сайта, которое вы заполнили в настройках сайта.</span></div></td>
					<td>
						<textarea rows="5" name="tag_description" class="w400">'.$item['tag_description'].'</textarea>
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
	<script type="text/javascript">DAN.accordion("accordion_head", "accordion_body");</script>

	<div style="margin:20px 0px 5px 0px;">Описание изображения, текст:</div>
	<textarea name="editor1">'.$item['text'].'</textarea>

	<script type="text/javascript">
		e_editor_1 = CKEDITOR.replace( \'editor1\',
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


// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======
function tree($item, $i = 0) // $i = 0 начальный уровень меню, $lvl - уровень меню
{
	global $db, $domain, $lvl;
	$lvl++;

	$stmt_menu = $db->prepare('SELECT * FROM menu WHERE parent = :parent ORDER BY `ordering` ASC');
	$stmt_menu->execute(array('parent' => $i)); // активируем стартамент

	//$otstup = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",($lvl -1));  // отступ слева у пункта меню
	if ($lvl == 4) { $otstup = " - - - "; }
	elseif ($lvl == 3) { $otstup = " - - "; }
	elseif ($lvl == 2) { $otstup = " - "; }
	else {$otstup = "";}

	if($stmt_menu->rowCount() > 0)
	{
		while($m = $stmt_menu->fetch())
		{
			$menu_id = $m['id'];
			$section_pub = $m['pub'];
			$section_id_com = $m['id_com'];
			$section_parent = $m['parent'];
			$section_component = $m['component'];
			$section_ordering = $m['ordering'];
			$section_title = $m['name'];
			$section_p1 = $m['p1'];

			if ($item['section'] == $section_id_com && $section_component == 'photo') {$selected = 'selected';}
			else {$selected = '';}

			// Если пункт не является корнем и не является магазином то не выводим
			if ($section_p1 != 'all' && $section_component == 'photo')
			{
				echo'<option value="'.$section_id_com.'" '.$selected.' >'.$otstup.$section_title.'</option>';
			}

			tree($item, $menu_id); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским

			$lvl--;
		}
	} // конец проверки $result > 0
} // конец функции tree

?>