<?php
// DAN обновление - январь 2014
// Редактируем страницу

defined('AUTH') or die('Restricted access');

// определяем id раздела
$item_id = intval($admin_d4);

$component_head = '<link rel="stylesheet" href="http://'.$site.'/components/shop/admin/tmp/item_edit.css" type="text/css" />';
$component_head .= '<script type="text/javascript" src="http://'.$site.'/js/dan.framework.js"></script>';
$component_head .= '<script type="text/javascript" src="http://'.$site.'/components/shop/admin/item_edit.js"></script>';

$component_head .= '
	<script type="text/javascript">
			
		var req = getXmlHttp();

		function get_char_list(section_id,item_id)
		{
			req.open("POST", "/components/shop/admin/shop_section_char_get.php", true);
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send("section_select="+section_id+"&"+"item_id="+item_id);

			req.onreadystatechange = function() 
			{
				if (req.readyState == 4) 
				{
					if (req.status == 200) 
					{
						// Вставляем список характеристик
						document.getElementById("char_list").innerHTML = req.responseText;
					}
				}
			}
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
			req.open(\'GET\', \'http://'.$site.'/administrator/url/ajax.php?sef=\' + sef, true);
			req.send(null);
			document.getElementById("url_status").innerHTML = "<div align=\"left\"><img src=\"http://'.$site.'/administrator/tmp/images/loading.gif\" /></div>";
		}

		
		function img_ajax(files)
		{
			if (!files[0].type.match(/image.*/)) 
			{
				alert("Данный формат файла не поддерживается");
				return true;
			}		
		
			var reader = new FileReader();
			
			reader.onload = function(read_src) 
			{
				img_src = read_src.target.result;
				
				img_uri = encodeURIComponent(img_src);
				
				// console.log (read_src.target.result);
				
				req.open("POST", "http://'.$site.'/admin/com/shop/img_upload_ajax", true);
				req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				req.send("act=upload&id='.$item_id.'&img_src="+img_uri);

				req.onreadystatechange = function() 
				{			
					if (req.readyState == 4) 
					{			
						if (req.status == 200) 
						{
							var data = eval("(" + req.responseText + ")");
							document.getElementById("drag_trg").innerHTML += "<img class=\"drag_drop\" style=\"width:'.$w_img.'px;height:'.$h_img.'px;\" src=\"/components/shop/photo/" + data.img_small + "\">";
							document.getElementById("images_order").value += data.img_small + ";";
							document.getElementById("img_status").innerHTML = "";
						}
					}
				}				
				
			}	
		
			reader.readAsDataURL(files[0]);
			document.getElementById("img_status").innerHTML = "<div align=\"left\"><img src=\"http://'.$site.'/administrator/tmp/images/loading.gif\" /></div>";
		}

	</script>
';	






function a_com()
{
	global $site, $url_arr, $menu_t, $menu_type, $item_id, $item_section_id, $h_img, $item_id;

	// находим родительский раздел
	$itemsection = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

	while($n = mysql_fetch_array($itemsection)):
		$item_artikul = $n['identifier'];
		$item_section_id = $n['section'];
		$item_ordering = $n['ordering'];
		$item_title = $n['title'];
		$item_price = $n['price'];
		$item_price_old = $n['priceold'];		
		$item_quantity = $n['quantity'];
		$item_photo = $n['photo'];
		$item_photomore = $n['photomore'];
		$item_new = $n['new'];
		$item_discount = $n['discount'];	
		$item_pub = $n['pub'];
		$item_introtext = $n['introtext'];
		$item_fulltext = $n['fulltext'];
		$etext_enabled = $n['etext_enabled'];
		$etext = $n['etext'];		
		$tag_title = $n['tag_title'];
		$tag_description = $n['tag_description'];		
	endwhile;
	
	// убираем у цены два нуля, после запятой для целочисленных значений
	if($item_quantity - ceil($item_quantity) == 0){$item_quantity = intval($item_quantity);}
	
	// вывод настроек, необходим, что бы узнать высоту картинки, по ней и выровнять высоту первого редактора
	$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
	while($m = mysql_fetch_array($num)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parameter = $m['parametr'];

		// размер по "y" малого изображения
		if ($setting_name == "y_small")
		{
			$h_img = $setting_parameter;
			// если высота меньше 50px, то высота = 50px, иначе = высоте картинки
			if($h_img < 50){$h_img = 50;}
		}
		
		// размер по "x" малого изображения
		if ($setting_name == "x_small")
		{
			$w_img = $setting_parameter;
		}		
		
		// учитывать количество товаров
		if($setting_name == "item_quantity")
		{
			if($setting_parameter == 0)
			{
				$item_quantity_out = '';
			}		
			else
			{
				$item_quantity_out = '
				<tr>
					<td width="20">&nbsp;</td>		
					<td width="200" height="25">Количество <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Количество товаров</em>Товары с нолевым количеством обозначаются статусом <br><b>&quot;Под заказ&quot;</b>.</span></div></td>
					<td><input type="text" name="quantity" value="'.$item_quantity.'" size="10"></td>
				</tr>';
			}
		}	
	endwhile;	
	
	

	// Условия
	if($item_new === "1"){$pub_new = "checked";} else {$pub_new = "";}
	if($item_discount === "1"){$pub_discount = "checked";} else {$pub_discount = "";}	
	if($item_pub === "1"){$pub_checked = "checked";} else {$pub_checked = "";}
	if($etext_enabled === "1"){$etext_enabled_checked = "checked";} else {$etext_enabled_checked = "";}	
	if($item_photo == '')
	{
		$img = '';
		$img_more_input = '';
	}
	else 
	{
		$img = '<img class="drag_drop" style="width:'.$w_img.'px; height:'.$h_img.'px;" src="http://'.$site.'/components/shop/photo/'.$item_photo.'">';
		$img_more_input = $item_photo.';';
	}
	
	
	if ($item_photomore == '')
	{
		$img_more_out = '';
	}
	else
	{
		$photo_arr = explode(';', $item_photomore);
		$img_more_out = '';
		
		if(count($photo_arr) >= 1)
		{
			for($i=0; $i<count($photo_arr)-1; $i++)
			{
				$img_more_out .= '<img class="drag_drop" style="width:'.$w_img.'px; height:'.$h_img.'px;" src="http://'.$site.'/components/shop/photo/'.$photo_arr[$i].'" alt="">';
				$img_more_input .= $photo_arr[$i].';';
			}
			// пустое изображение в конце для drag & drop
			$img_more_out .= '<img id="img_zero" class="drag_drop" style="width:50px; height:'.$h_img.'px; opacity:0;" src="" alt="">';
		}		
	}
	
	

	echo '
	<div id="main-top">ИНТЕРНЕТ - МАГАЗИН: Редактировать товар</div>

	<form enctype="multipart/form-data" method="POST" action="http://'.$site.'/admin/com/shop/itemupdate/'.$item_id.'/">

	<table class="main-tab vam">
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Название товара <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Название товара</em>Выводится над основным содержимым в теле страницы.<br><br> Для поисковых роботов система управления прописывает его специальным тегом заголовка - h1</span></div></td>
			<td><input type="text" name="title" size="50" value="'.$item_title.'" required></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Артикул товара (не обязателен) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Артикул товара</em>Нужен для идентификации товара при групповой загрузке из Excel или XML - файла</span></div></td>
			<td><input type="text" name="artikul" size="20" value="'.$item_artikul.'"/></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Цена</td>
			<td><input type="text" name="price" size="10" value="'.$item_price.'" required> руб.</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Скидка <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Товар со скидкой</em>Товар маркируется статусом "Скидка".<br>Появляется дополнительное поле "Старая цена", которая выводится на сайте в перечёркнутом виде, рядом с обычной ценой.</span></td>
			<td><input id="discount" type="checkbox" name="discount" value="1" '.$pub_discount.'> <span id="price_old_display">Старая цена: <input type="text" name="price_old" size="10" value="'.$item_price_old.'"> руб.</span></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Новинка <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Новинка</em>Товар маркируется статусом "Новинка".</span></td>
			<td><input type="checkbox" name="new" value="1" '.$pub_new.'></td>
		</tr>		
		'.$item_quantity_out.'		
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200">Фотография <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Фотографии товара</em>Загружайте одну или несколько фотографий товара. Передвигая фотографии расставьте их в нужном порядке. Первая фотография будет главной фотографией товара. <br>Для удаления изображения - выберите изображение и нажмите правую кнопку мыши.<span></div></td>
			<td id="drag_trg" data-id="'.$item_id.'">'.$img.$img_more_out.'</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Загрузить ещё изображение</td>
			<td><input onchange="img_ajax(this.files);" type="file" name="photo"/><span id="img_status"></span><span style="padding-left:40px;color:#FF0000">Загружаемый размер изображения - не более 2 мегабайт.</style></td>
		</tr>	
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Опубликовать товар <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Опубликовать товар</em>Галочка стоит - товар отображается. Нет галочки - товар не отображается с внешней стороны сайта.</span></td>
			<td><input type="checkbox" name="pub" value="1" '.$pub_checked.'></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Категория</td>
			<td>
			<select size="10" name="section" id="shop_select_tree_cat">';
			tree(0,0);
			echo'
			</select>
			</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>
			<td width="200" height="25">Порядок размещения товара</td>
			<td><input type="text" name="ordering" size="3" value="'.$item_ordering.'"></td>
		</tr>
			<tr>
				<td>&nbsp;</td>			
				<td height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>			
		</table>
		<input id="images_order" name="images_order" type="hidden" value="'.$img_more_input.'">
		';
		
		// если есть в массиве ЧПУ - заменяем
		$p_qs = 'shop/item/'.$item_id;
		
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
						<td height="25">&lt;title&gt; (заголовок) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Заголовок страницы</em>Этот тег отображается во вкладке браузера и сообщает поисковым роботам - о чем идет речь на странице. Тег должен быть коротким, и релевантным содержимому страницы. <br><br> Если поле оставить пустым – система управления при выводе страницы сгенерирует его автоматически в таком формате: <b>название товара – название сайта</b>, например: <b>Платье Kitti - интернет-магазин детской одежды</b></span></div></td>
						<td>
							<textarea rows="2" name="tag_title" class="w400">'.$tag_title.'</textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td height="25">&lt;description&gt; (описание) <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Описание товара</em>Тег не виден на странице человеку, но виден  поисковому роботу.  Очень часто этот тег используется поисковиком в качестве сниппета.<br><br>При выводе товара, если этот тег не заполнен - система управления достаточно грамотно генерирует этот тег в таком формате: <b>Название товара. Цена. Краткое описание.</b> пример: <b>Платье Kitti. Цена: 350 руб. рост 90 и 120. Производство Китай. Состав: хлопок 100%</b>.</span></div></td>
						<td>
							<textarea rows="5" name="tag_description" class="w400">'.$tag_description.'</textarea>
						</td>
						<td>&nbsp;</td>
					</tr>				
					<tr>
						<td>&nbsp;</td>			
						<td height="25">ЧПУ URL</td>
						<td>Формируется только автоматически</td>
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
			<div class="left_head section_head_seo">Характеристики товара</div>
			<div class="left_body" id="char_list"></div>
		</div>		
		';
		
		echo'
		<table class="main-tab">
			<tr>
				<td>&nbsp;</td>			
				<td height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>				
			<tr>
				<td width="20">&nbsp;</td>
				<td width="200" height="25">Описание товара, вводный текст:</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<input type="hidden" name="menu_t" value="'.$menu_t.'"/>
		<textarea name="editor1">'.$item_introtext.'</textarea>

		<script type="text/javascript">
			CKEDITOR.replace( \'editor1\',
				{
					height: \''.$h_img.'px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
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
				<td width="200" height="25">Описание товара, полный текст:</td>
				<td>(выводится в детальном описании товара)</td>
			</tr>
		</table>
		<textarea name="editor2">'.$item_fulltext.'</textarea>

		<script type="text/javascript">
			CKEDITOR.replace( \'editor2\',
				{
					height: \'400px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
				});
				
			get_char_list('.$item_section_id.','.$item_id.');

			document.getElementById("shop_select_tree_cat").onchange = function()
			{
				get_char_list(document.getElementById("shop_select_tree_cat").options[document.getElementById("shop_select_tree_cat").selectedIndex].value,'.$item_id.');
			};			
		</script>
		
		
		<div><input class="etext_enabled" type="checkbox" '.$etext_enabled_checked.' name="etext_enabled" onclick="etext_hide()" value="1"> Электронный товар 
			<div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Электронный товар</em>Это товар с возможностью получения доступа или ссылок для скачивания, (например электронный курс) сразу после оплаты электронными деньгами. <br><br>После оплаты покупатель попадает на страницу получения товара. Страница получения товара станет доступной в административной части, если Вы поставите галочку <b>Электронный товар</b>. Это поле поле выделяется зелёной рамкой.</span></div>
		</div>
		<div>&nbsp;</div>
		<div id="etext"><textarea name="editor3">'.$etext.'</textarea></div>
		<div>&nbsp;</div>
		<script type="text/javascript">	
			CKEDITOR.replace( \'editor3\',
				{	        
					height: \'400px\',
					filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
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
	
	

} // конец функции



// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======

function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{
	global $site, $item_section_id;
	global $lvl; // уровень
	$lvl++;
	$numtree = mysql_query("SELECT * FROM `menu` WHERE `parent` = '$i' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");

	//$otstup = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	if ($lvl == 4) { $otstup = " - - - "; }
	elseif ($lvl == 3) { $otstup = " - - "; }
	elseif ($lvl == 2) { $otstup = " - "; }
	else {$otstup = "";}
	
	
	$result = mysql_num_rows($numtree);

	if ($result > 0) 
	{
		while($m = mysql_fetch_array($numtree)):
			$section_id = $m['id'];
			$section_pub = $m['pub'];
			$section_id_com = $m['id_com'];
			$section_parent = $m['parent'];
			$section_component = $m['component'];
			$section_ordering = $m['ordering'];
			$section_title = $m['name'];
			$section_p1 = $m['p1'];

	   // устанавливаем состояние выбрано для родительского раздела
		if ($section_id_com == $item_section_id AND $section_component == 'shop'){$selected = "selected";} else {$selected = "";}

		// Если пункт не является корнем и не является магазином то не выводим
		if ($section_p1 != 'all' AND $section_component == 'shop')
		{
			echo'<option value="'.$section_id_com.'" '.$selected.' >'.$otstup.$section_title.'</option>';
		}

		tree($section_id, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским

		$lvl--;

		endwhile;

	} // конец проверки $result > 0
} // конец функции tree




?>
