<?php

echo '
<h1>ИНТЕРНЕТ - МАГАЗИН: Редактировать товар</h1>
'.$form_action_out.'
<table class="main_tab vam">
	<tr>
		<td style="width:20px;">&nbsp;</td>
		<td style="width:250px;">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">Название товара <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название товара</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
		<td><input type="text" name="title" size="50" value="'.$item['title'].'" required></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">Цена</td>
		<td>
			<input type="text" name="price" size="10" value="'.$item['price'].'" required>
			'.$currency_out.'
		</td>
	</tr>
	'.$price_type_out.'
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">'.$shopSettings->sticker_sale.' <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Товар со скидкой</em>Товар маркируется стикером "'.$shopSettings->sticker_sale.'".<br>Появляется дополнительное поле "Старая цена", которая выводится на сайте в перечёркнутом виде, рядом с обычной ценой.</span></td>
		<td><input id="discount" type="checkbox" name="discount" value="1" '.$pub_discount.'> <span id="price_old_display">Старая цена: <input type="text" name="price_old" size="10" value="'.$item['price_old'].'"></span></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">'.$shopSettings->sticker_new.' <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Новинка</em>Товар маркируется стикером "'.$shopSettings->sticker_new.'".</span></td>
		<td><input type="checkbox" name="new" value="1" '.$pub_new.'></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">'.$shopSettings->sticker_hit.' <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Хит</em>Товар маркируется стикером "'.$shopSettings->sticker_hit.'".</span></td>
		<td><input type="checkbox" name="hit" value="1" '.$pub_hit.'></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">'.$shopSettings->sticker_rating.' от 0 до 5</td>
		<td><input type="number" name="rating" size="10" value="'.$item['rating'].'" min="0" max="5" step="0.01" pattern="[0-9 \.\,]{0,4}" title="Значение рейтинга от 1 до 5"></td>
	</tr>
	'.$item_quantity_out.'
	<tr>
		<td>&nbsp;</td>
		<td>Фотография <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Фотографии товара</em>Загружайте одну или несколько фотографий товара. Передвигая фотографии расставьте их в нужном порядке. Первая фотография будет главной фотографией товара. <br>Для удаления изображения - выберите изображение и нажмите правую кнопку мыши.<span></div></td>
		<td id="drag_trg" data-id="'.$item['id'].'">'.$img.$img_more_out.'</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">Загрузить ещё изображение</td>
		<td><input onchange="img_ajax(this.files);" type="file" name="photo"/><span id="img_status"></span><span style="padding-left:40px;color:#FF0000">Загружаемый размер изображения - не более 2 мегабайт.</style></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">Опубликовать товар <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать товар</em>Галочка стоит - товар отображается. Нет галочки - товар не отображается с внешней стороны сайта.</span></td>
		<td><input type="checkbox" name="pub" value="1" '.$pub_checked.'></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">Категория</td>
		<td>
		<select size="10" name="section" id="shop_select_tree_cat">';
		tree($item);
		echo'
		</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="height:25px;">Порядок размещения товара</td>
		<td><input type="text" name="ordering" size="3" value="'.$item['ordering'].'"></td>
	</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="height:25px;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<input id="images_order" name="images_order" type="hidden" value="'.$img_more_input.'">
	';

	// если есть в массиве ЧПУ - заменяем
	$p_qs = 'shop/item/'.$item['id'];

	if(isset($url_arr[$p_qs]) && $url_arr[$p_qs] != '')
	{
		$sef = $url_arr[$p_qs];
	}
	else
	{
		$sef = '';
	}

	echo '
	<div class="seo_fon">
		<div class="accordion_head left_head">С этим товаром покупают</div>
		<div class="accordion_body a_1">
			<div style="margin:20px;">
				<div style="height:40px;"><span id="related_item_add" onclick="item_list(0);" class="char_add" title="Добавить товар">Добавить товар</span></div>
				<div id="related_items">'.$related_items_out.'</div>
				<input id="related_order" name="related_order" type="hidden" value="'.$related_items_input.'">
			</div>
		</div>	
		<div class="accordion_head left_head">Характеристики товара</div>
		<div class="accordion_body a_2">
			<div style="margin:20px;">
				<div style="height:40px;"><a href="/admin/com/shop/chars" class="char_add_small" title="Добавить характеристику">+</a><span id="char_add" class="char_add" title="Добавить значение характеристики">Добавить значение</span></div>
				<table class="main_tab char_title_tab">
					<tr>
						<td style="width:40px;">&nbsp</td>
						<td style="width:300px;">Наименование</td>
						<td style="width:70px;">Ед.изм.</td>
						<td style="width:70px;">Тип</td>
						<td style="width:300px;">Значение</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<div id="char_list">'.$chars_out.'</div>
			</div>
		</div>
		<div class="accordion_head left_head">Дополнительные параметры</div>
		<div class="accordion_body a_3">
			<div style="margin:20px;">
				<table class="main_tab vam">
					<tr>
						<td style="width:200px;">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="height:25px;">Идентификатор товара <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Идентификатор товара (не обязателен)</em>Идентификатор товара содержит уникальное значение, необходимое для идентификации товара при загрузке из 1С или Excel или XML - файла. По нему ищется товар.<br><br>В случае загрузки из 1С идентификатор товара равен идентификатору товару в файле "offers", состояцему из идентификатора товара + # + идентификатор набора характеристик.</span></div></td>
						<td><input type="text" name="identifier" size="20" value="'.htmlspecialchars($item['identifier']).'"/></td>
					</tr>
					<tr>
						<td style="height:25px;">Идентификатор группы <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Идентификатор группы (не обязателен)</em>Многие однотипные товары можно группировать в группы. Например, у Вас есть три одинаковых товара "футболка мужская", которые отличаются только тем, что имеют разный цвет и размер. Вывод однотипных товаров в разделе, особенно одинакового цвета может запутать вашего покупателя. Вы можете сгруппировать все товары в один, указав у всех футболок одинаковый идентификатор группы (допустимы русские буквы). <br><br>При включении в настройках интернет-магазина группировки - вместо 3 одинаковых футболок покажется одна футбока с различными значениями характеристик.</span></div></td>
						<td><input type="text" name="group_identifier" size="20" value="'.htmlspecialchars($item['group_identifier']).'"/></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="accordion_head left_head">SEO</div>
		<div class="accordion_body a_4">
			<div style="margin:20px;">
				<table>
					<tr>
						<td style="width:200px; height:25px;">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название товара – название сайта</b>, например: <b>Платье Kitti - интернет-магазин детской одежды</b></span></div></td>
						<td style="width:420px;">
							<textarea rows="2" name="tag_title" class="w400">'.$item['tag_title'].'</textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание товара</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br>При выводе товара, если этот тег не заполнен - система управления достаточно грамотно генерирует этот тег в таком формате: <b>Название товара. Цена. Краткое описание.</b> пример: <b>Платье Kitti. Цена: 350 руб. рост 90 и 120. Производство Китай. Состав: хлопок 100%</b>.</span></div></td>
						<td>
							<textarea rows="5" name="tag_description" class="w400">'.$item['tag_description'].'</textarea>
						</td>
					</tr>
					<tr>
						<td height="25">ЧПУ URL</td>
						<td>Формируется только автоматически</td>
						<td style="vertical-align: middle;"><div id="url_status"></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<script type="text/javascript">DAN.accordion("accordion_head", "accordion_body");</script>

	<div style="margin:20px 0px 5px 0px;">Описание товара, вводный текст:</div>
	<textarea name="editor1">'.$item['intro_text'].'</textarea>

	<script type="text/javascript">
		e_editor_1 = CKEDITOR.replace( \'editor1\',
			{
				height: \''.$h_img.'px\',
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
			});
	</script>
	<table>
		<tr>
			<td width="20">&nbsp;</td>
			<td style="height:25px;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td style="height:25px;">Описание товара, полный текст:</td>
			<td>(выводится в детальном описании товара)</td>
		</tr>
	</table>
	<textarea name="editor2">'.$item['full_text'].'</textarea>

	<script type="text/javascript">
		e_editor_2 = CKEDITOR.replace( \'editor2\',
		{
			height: \'400px\',
			filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
		});

		document.getElementById("shop_select_tree_cat").onchange = function()
		{
			get_char_list(document.getElementById("shop_select_tree_cat").options[document.getElementById("shop_select_tree_cat").selectedIndex].value,'.$item['id'].');
		};
	</script>


	<div><input class="etext_enabled" type="checkbox" '.$etext_enabled_checked.' name="etext_enabled" onclick="etext_hide()" value="1"> Электронный товар
		<div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Электронный товар</em>Это товар с возможностью получения доступа или ссылок для скачивания, (например электронный курс) сразу после оплаты электронными деньгами. <br><br>После оплаты покупатель попадает на страницу получения товара. Страница получения товара станет доступной в административной части, если Вы поставите галочку <b>Электронный товар</b>. Это поле поле выделяется зелёной рамкой.</span></div>
	</div>
	<div>&nbsp;</div>
	<div id="etext"><textarea name="editor3">'.$item['etext'].'</textarea></div>
	<div>&nbsp;</div>
	<script type="text/javascript">
		e_editor_3 = CKEDITOR.replace( \'editor3\',
			{
				height: \'400px\',
				filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
			});
	</script>
	<script type="text/javascript">
		function etext_hide()
		{
			var etext_enabled = document.getElementsByName("etext_enabled");

			if(etext_enabled[0].checked)
			{
				document.getElementById("etext").style.display = "block";
			}
			else
			{
				document.getElementById("etext").style.display = "none";
			}
		}
		etext_hide();
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

			if ($item['section'] == $section_id_com && $section_component == 'shop') {$selected = 'selected';}
			else {$selected = '';}

			// Если пункт не является корнем и не является магазином то не выводим
			if ($section_p1 != 'all' && $section_component == 'shop')
			{
				echo'<option value="'.$section_id_com.'" '.$selected.' >'.$otstup.$section_title.'</option>';
			}

			tree($item, $menu_id); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским

			$lvl--;
		}
	} // конец проверки $result > 0
} // конец функции tree

?>