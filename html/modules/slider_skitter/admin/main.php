<?php
// DAN обновление - январь 2014
// Слайдер

defined('AUTH') or die('Restricted access');

$act = $admin_d3;

if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else {$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else {$mod_titlepub = 0;}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else {$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else {$mod_ordering = 0;}
if(isset($_POST["image_src"])){$image_src = $_POST["image_src"];} else {$image_src = '';}

for($i = 0; $i < 10; $i++)
{
	if(isset($_POST["image_pub"][$i])){$image_pub[$i] = intval($_POST["image_pub"][$i]);}
	else {$image_pub[$i] = 0;}
}

if(isset($_POST["image_desc"])){$image_desc = $_POST["image_desc"];} else{$image_desc = '';} // проверим попозже, когда разберём на массив
if(isset($_POST["image_link"])){$image_link = $_POST["image_link"];} else{$image_link = '';} // проверим попозже, когда разберём на массив

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;}

if(isset($_POST["animation"])){$settings_animation = htmlspecialchars($_POST["animation"]);} else{$settings_animation = '';}
if(isset($_POST["style_slider"])){$settings_style_slider = intval($_POST["style_slider"]);} else{$settings_style_slider = 0;}
if(isset($_POST["time_img"])){$settings_time_img = intval($_POST["time_img"]);} else{$settings_time_img = 0;}
if(isset($_POST["label"])){$settings_label = intval($_POST["label"]);} else{$settings_label = 0;}
if(isset($_POST["button_slide"])){$settings_button_slide = intval($_POST["button_slide"]);} else{$settings_button_slide = 0;}
if(isset($_POST["button_nav"])){$settings_button_nav = intval($_POST["button_nav"]);} else{$settings_button_nav = 0;}
if(isset($_POST["speed_animation"])){$settings_speed_animation = intval($_POST["speed_animation"]);} else{$settings_speed_animation = 0;}

if(isset($_POST["skitter_height"])){$skitter_height = $_POST["skitter_height"];} else{$skitter_height = '';}
if(isset($_POST["skitter_width"])){$skitter_width = $_POST["skitter_width"];} else{$skitter_width = '';}

if ($settings_label == 1){$settings_label = 'true';}else{$settings_label = 'false';}
if ($settings_button_slide == 1){$settings_button_slide = 'true';}else{$settings_button_slide = 'false';}
if ($settings_button_nav == 1){$settings_button_nav = 'true';}else{$settings_button_nav = 'false';}

$settings_speed_animation = $settings_speed_animation/10;


// Условие публикации
if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";}

// выбираем действие над модулем
if ($act == "update")
{
	// Обновляем данные в таблице "modules"
	$query_updatedit_modeule_skitter = "UPDATE `modules` SET `title` = '".$mod_title."', `pub` = '".$mod_pub."', `titlepub` = '".$mod_titlepub."', `block` = '".$mod_block."', `ordering` = '".$mod_ordering."', `p1` = '".$settings_animation."', `p2` = '".$settings_label."', `p3` = '".$settings_style_slider."', `p4` = '".$settings_time_img."', `p5` = '".$settings_button_slide."', `p6` = '".$settings_button_nav."', `p7` = '".$settings_speed_animation."', `p9` = '".$skitter_height."', `p10` = '".$skitter_width."' WHERE `module` = 'slider_skitter'";

	$sql_module_skitter = mysql_query($query_updatedit_modeule_skitter) or die ("Невозможно обновить данные");

	$slider_skitter_xml_out =
'<?xml version="1.0"?>
<skitter>
';

	for ( $i=0; $i<=9; $i++)
	{

		if ($image_pub[$i] == 1 && $image_src[$i] !="" )
		{

		$image_link_out = htmlspecialchars($image_link[$i]);
		$image_desc_out = htmlspecialchars($image_desc[$i]);

	$slider_skitter_xml_out .=
'	<slide>
		<link>'.$image_link_out.'</link>
		<image type="directionTop">/files/slider_skitter/'.$image_src[$i].'</image>
		<label>'.$image_desc_out.'</label>
	</slide>
';
		}
	}

	$slider_skitter_xml_out .= '</skitter>';

	$pt = $_SERVER['DOCUMENT_ROOT'];
	$slider_dir = "/modules/slider_skitter/frontend/";
	$file = $pt.$slider_dir."settings.xml";
	// записываем файл
	file_put_contents($file, $slider_skitter_xml_out);

	if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
	else {Header ("Location: /admin/modules/slider_skitter"); exit;}
}
else {

	function a_com()
	{

	global $site, $i, $skitter_image_src, $slider_skitter_block;

		// вывод содержимого модуля
		$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'slider_skitter'") or die ("Невозможно сделать выборку из таблицы - 1");

		while($m = mysql_fetch_array($num)):
			$module_id = $m['id'];
			$module_title = $m['title'];
			$module_pub = $m['pub'];
			$module_titlepub = $m['titlepub'];
			$module_enabled = $m['enabled'];
			$module_description = $m['description'];
			$module_content = $m['content'];
			$module_ordering = $m['ordering'];
			$module_block =	$m['block'];
			$animation = $m['p1'];
			$label = $m['p2'];
			$style_slider = $m['p3'];
			$time_img = $m['p4'];
			$button_slide = $m['p5'];
			$button_nav = $m['p6'];
			$speed_animation = $m['p7'];
			$skitter_height = $m['p9'];
			$skitter_width = $m['p10'];
		endwhile;

		// Если не указаны размеры слайдера, ставим = размерам блока
		if ($skitter_height == "" || $skitter_height == 0){$skitter_height = $block_height;}
		if ($skitter_width == "" || $skitter_width == 0){$skitter_width = $block_width;}

		// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
		// загрузка XML-файла тем.
		$pt=$_SERVER['DOCUMENT_ROOT'];
		$xmltemplate = simplexml_load_file($pt.'/tmp/template.xml');

		if ($xmltemplate)
		{
			foreach ($xmltemplate->block as $b)
			{
				$name_block = $b->name;
				$w = $b->width;
				$h = $b->height;
				if ($module_block == $name_block)
				{
						$block_width = $w;
						$block_height = $h;
				}
			}
		}
		// ======== / загрузка блоков вывода =======

		// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
		$block_query = mysql_query("SELECT * FROM `block`") or die ("Невозможно сделать выборку из таблицы - 1");
		$result = mysql_num_rows($block_query);
		$block_option = '';
		if ($result > 0)
		{
			while($b = mysql_fetch_array($block_query)):
				$b_id = $b['id'];
				$b_name = $b['block'];
				$b_description = $b['description'];

			if ($b_name == $module_block){$selected = 'selected';} else {$selected = '';}
			$block_option .= '<option '.$selected.' value='.$b_name.'>'.$b_description.'</option>';
			endwhile;
		}
		// ======== / загрузка блоков вывода =======

		// устанавливаем признак публикации
		$pub_0 = $pub_1 = $pub_2 = $pub_3 = '';

		switch($module_pub)
		{
			case 0:
				$pub_0 = 'selected="selected"';
				break;

			case 1:
				$pub_1 = 'selected="selected"';
				break;

			case 2:
				$pub_2 = 'selected="selected"';
				break;

			case 3:
				$pub_3 = 'selected="selected"';
				break;
		}

		// устанавливаем признак публикации заголовка модуля
		if ($module_titlepub == 1){$titlepub = "checked";} else{$titlepub = "";}

		// настройки - активный пунк анимации
		if ($animation == 'none'){$animation_select_1 = "random";} else {$animation_select_1 = '';}
		if ($animation == 'fade'){$animation_select_2 = "selected";} else {$animation_select_2 = '';}
		if ($animation == 'cube'){$animation_select_3 = "selected";} else {$animation_select_3 = '';}
		if ($animation == 'cubeRandom'){$animation_select_4 = "selected";} else {$animation_select_4 = '';}
		if ($animation == 'block'){$animation_select_5 = "selected";} else {$animation_select_5 = '';}
		if ($animation == 'cubeStop'){$animation_select_6 = "selected";} else {$animation_select_6 = '';}
		if ($animation == 'cubeHide'){$animation_select_7 = "selected";} else {$animation_select_7 = '';}
		if ($animation == 'cubeSize'){$animation_select_8 = "selected";} else {$animation_select_8 = '';}
		if ($animation == 'horizontal'){$animation_select_9 = "selected";} else {$animation_select_9 = '';}
		if ($animation == 'showBars'){$animation_select_10 = "selected";} else {$animation_select_10 = '';}
		if ($animation == 'showBarsRandom'){$animation_select_11 = "selected";} else {$animation_select_11 = '';}
		if ($animation == 'tube'){$animation_select_12 = "selected";} else {$animation_select_12 = '';}
		if ($animation == 'fadeFour'){$animation_select_13 = "selected";} else {$animation_select_13 = '';}
		if ($animation == 'paralell'){$animation_select_14 = "selected";} else {$animation_select_14 = '';}
		if ($animation == 'blind'){$animation_select_15 = "selected";} else {$animation_select_15 = '';}
		if ($animation == 'blindHeight'){$animation_select_16 = "selected";} else {$animation_select_16 = '';}
		if ($animation == 'blindWidth'){$animation_select_17 = "selected";} else {$animation_select_17 = '';}
		if ($animation == 'directionTop'){$animation_select_18 = "selected";} else {$animation_select_18 = '';}
		if ($animation == 'directionBottom'){$animation_select_19 = "selected";} else {$animation_select_19 = '';}
		if ($animation == 'directionRight'){$animation_select_20 = "selected";} else {$animation_select_20 = '';}
		if ($animation == 'directionLeft'){$animation_select_21 = "selected";} else {$animation_select_21 = '';}
		if ($animation == 'cubeStopRandom'){$animation_select_22 = "selected";} else {$animation_select_22 = '';}
		if ($animation == 'cubeSpread'){$animation_select_23 = "selected";} else {$animation_select_23 = '';}
		if ($animation == 'cubeJelly'){$animation_select_24 = "selected";} else {$animation_select_24 = '';}
		if ($animation == 'glassCube'){$animation_select_25 = "selected";} else {$animation_select_25 = '';}
		if ($animation == 'glassBlock'){$animation_select_26 = "selected";} else {$animation_select_26 = '';}
		if ($animation == 'circles'){$animation_select_27 = "selected";} else {$animation_select_27 = '';}
		if ($animation == 'circlesInside'){$animation_select_28 = "selected";} else {$animation_select_28 = '';}
		if ($animation == 'circlesRotate'){$animation_select_29 = "selected";} else {$animation_select_29 = '';}
		if ($animation == 'cubeShow'){$animation_select_30 = "selected";} else {$animation_select_30 = '';}
		if ($animation == 'upBars'){$animation_select_31 = "selected";} else {$animation_select_31 = '';}
		if ($animation == 'downBars'){$animation_select_32 = "selected";} else {$animation_select_32 = '';}
		if ($animation == 'hideBars'){$animation_select_33 = "selected";} else {$animation_select_33 = '';}
		if ($animation == 'swapBars'){$animation_select_34 = "selected";} else {$animation_select_34 = '';}
		if ($animation == 'swapBarsBack'){$animation_select_35 = "selected";} else {$animation_select_35 = '';}
		if ($animation == 'swapBlocks'){$animation_select_36 = "selected";} else {$animation_select_36 = '';}
		if ($animation == 'cut'){$animation_select_37 = "selected";} else {$animation_select_37 = '';}

		//настройки - стиля слайдера
		if ($style_slider == '0'){$style_slider_select_0 = "selected";} else {$style_slider_select_0 = '';}
		if ($style_slider == '1'){$style_slider_select_1 = "selected";} else {$style_slider_select_1 = '';}
		if ($style_slider == '2'){$style_slider_select_2 = "selected";} else {$style_slider_select_2 = '';}
		if ($style_slider == '3'){$style_slider_select_3 = "selected";} else {$style_slider_select_3 = '';}
		if ($style_slider == '4'){$style_slider_select_5 = "selected";} else {$style_slider_select_4 = '';}

		//настройки - смены кадра
		if ($time_img == '1'){$time_img_select_1 = "selected";} else {$time_img_select_1 = '';}
		if ($time_img == '2'){$time_img_select_2 = "selected";} else {$time_img_select_2 = '';}
		if ($time_img == '3'){$time_img_select_3 = "selected";} else {$time_img_select_3 = '';}
		if ($time_img == '4'){$time_img_select_4 = "selected";} else {$time_img_select_4 = '';}
		if ($time_img == '5'){$time_img_select_5 = "selected";} else {$time_img_select_5 = '';}
		if ($time_img == '6'){$time_img_select_6 = "selected";} else {$time_img_select_6 = '';}
		if ($time_img == '7'){$time_img_select_7 = "selected";} else {$time_img_select_7 = '';}
		if ($time_img == '8'){$time_img_select_8 = "selected";} else {$time_img_select_8 = '';}

		//настройки - скорость анимации
		if ($speed_animation == '1'){$speed_animation_1 = "selected";} else {$speed_animation_1 = '';}
		if ($speed_animation == '0.7'){$speed_animation_2 = "selected";} else {$speed_animation_2 = '';}
		if ($speed_animation == '0.5'){$speed_animation_3 = "selected";} else {$speed_animation_3 = '';}
		if ($speed_animation == '0.3'){$speed_animation_4 = "selected";} else {$speed_animation_4 = '';}
		if ($speed_animation == '0.2'){$speed_animation_5 = "selected";} else {$speed_animation_5 = '';}

		//настройки - отображать описание, кнопки слайд и навигации
		if ($label == 'true'){$label_check = 'checked';} else{$label_check = '';}
		if ($button_slide == 'true'){$button_slide_check = 'checked';} else{$button_slide_check = '';}
		if ($button_nav == 'true'){$button_nav_check = 'checked';} else{$button_nav_check = '';}

		// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
		if ($module_enabled == "1")
		{

		// функция отображения по javascript
		echo '
		<script type="text/javascript">

			// Создаём массив высот изображений
			var imh = new Array()

			 function skitter_display_image(i)
		 	 {
				var img = document.getElementById("selectimg_"+i).value;
				document.getElementById("imgout_"+i).innerHTML = "<img id=\"img_warning_"+i+"\" onload=\"info(this,"+i+")\" style=\"max-width:900px;\" src=\"/files/slider_skitter/"+img+"\" />";
		 	 }

			function info(img,i)
			{
				// Получение ширины изображения
				var block_width = document.getElementById("block_width_"+i).innerHTML;
				// Вывод ширины
				document.getElementById("img_width_"+i).innerHTML = img.width;

				// Получение высоты изображения
				var block_height = document.getElementById("block_height_"+i).innerHTML;
				// Вывод высоты
				document.getElementById("img_height_"+i).innerHTML = img.height;

				// Предупреждение если высота или ширина изображения превышают размеры блока

				// Преобразуем к числу
				+block_height;
				+block_width;
				+img.height;


				// Заносим высоту изображения в массив
				//imh[i] = img.height;

				// Находим максимальную высоту изображения - это и бкдет высота слайдера
				//skitter_height = Math.max.apply(Math, imh);
				// Заносм в форму в скрытое поле
				//if (skitter_height > 0)
				//{
				//	document.getElementById("skitter_height").value = skitter_height;
				//}



				/* === ВЫСОТА === */
				if (img.height > block_height && block_height > 0)
				{
					var message = "<div style=\"background-color:#ff0000;color:#ffffff;padding:5px;width:490px;\">ВНИМАНИЕ! Высота изображения больше размера блока!</style>";
					document.getElementById("message_height"+i).innerHTML = message;
					document.getElementById("img_warning_"+i).style.border="5px solid #ff0000";
				}
				else if (img.height < block_height && block_height > 0)
				{
					var message = "<div style=\"background-color:#ff0000;color:#ffffff;padding:5px;width:490px;\">ВНИМАНИЕ! Высота изображения меньше размера блока!</style>";
					document.getElementById("message_height"+i).innerHTML = message;
					document.getElementById("img_warning_"+i).style.border="5px solid #ff0000";
				}
				else
				{
					document.getElementById("message_height"+i).innerHTML = "";
					document.getElementById("img_warning_"+i).style.border="none";
				}
				/* === /высота === */



				/* === ШИРИНА === */
				if (img.width > block_width && block_width > 0)
				{
					var message = "<div style=\"background-color:#ff0000;color:#ffffff;padding:5px;width:490px;\">ВНИМАНИЕ! Ширина изображения больше размера блока!</style>";
					document.getElementById("message_width"+i).innerHTML = message;
					document.getElementById("img_warning_"+i).style.border="5px solid #ff0000";
				}
				else if (img.width < block_width && block_width > 0)
				{
					var message = "<div style=\"background-color:#ff0000;color:#ffffff;padding:5px;width:490px;\">ВНИМАНИЕ! Ширина изображения меньше размера блока!</style>";
					document.getElementById("message_width"+i).innerHTML = message;
					document.getElementById("img_warning_"+i).style.border="5px solid #ff0000";
				}
				else
				{
					document.getElementById("message_width"+i).innerHTML = "";
					document.getElementById("img_warning_"+i).style.border="none";
				}
				/* === /ширина === */
			}

			 function skitter_display_block(i)
		 	 {
				if (document.getElementById("skitter_check_"+i).checked == true)
				{
					document.getElementById("skitter_block_"+i).style.display = "block";
				}
				else
				{
					document.getElementById("skitter_block_"+i).style.display = "none";
					document.getElementById("selectimg_"+i).selectedIndex = 0;
					document.getElementById("imgout_"+i).innerHTML = "";

					// если блок скрыт, то высота изображения не считается
					//imh[i] = 0;

					// Находим максимальную высоту изображения - это и бкдет высота слайдера
					//skitter_height = Math.max.apply(Math, imh);
					// Заносм в форму в скрытое поле
					//if (skitter_height > 0)
					//{
					//	document.getElementById("skitter_height").value = skitter_height;
					//}
				}
		 	 }

			 CKEDITOR = function (funcNum, url, message){}
			 CKEDITOR.tools =
			 {
				callFunction:function()
				{
					window.location.reload();
				}
			 }

		</script>
		<link rel="stylesheet" href="/modules/slider_skitter/admin/style.css" type="text/css" />
		';

			echo '
			<h1><img onclick="CKEDITOR.tools.callFunction()" border="0" src="/modules/slider_skitter/admin/images/ico.png" style="width:25px; height:25px; float:left; padding-top:2px;">&nbsp;&nbsp;Модуль "Слайдер-skitter"</h1>

			<div class="padding-horizontal-20">

			<form method="POST" action="/admin/modules/slider_skitter/update/">

			<table class="admin_table_2">
				<tr>
					<td width="200" height="25">Название модуля</td>
					<td><input class="input" type="text" name="title" size="20" value="'.$module_title.'"></td>
					<td rowspan="4">
						<a target= "_blank" href="/administrator/plugins/browser/dan_browser.php?dir_current=slider_skitter&CKEditorFuncNum=2" id="buttonmanager"></a>
					</td>
				</tr>
				<tr>
					<td width="200" height="25">Отображать</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub_1.'>Всегда</option>
							<option value="2" '.$pub_2.'>Только на настольном компьютере</option>
							<option value="3" '.$pub_3.'>Только на телефоне</option>
							<option value="0" '.$pub_0.'>Никогда</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="200" height="25">Опубликовать заголовок модуля</td>
					<td><input type="checkbox" name="titlepub" value="1" '.$titlepub.' ></td>
				</tr>
				<tr>
					<td width="200" height="25">Позиция вывода, блок</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						&nbsp;- место вывода модуля
					</td>
				</tr>
				<tr>
					<td width="200" height="25">Порядок следования</td>
					<td><input class="input" type="text" name="ordering" size="3" value="'.$module_ordering.'"></td>
				</tr>
				<tr>
					<td width="200" height="25">Высота слайдера</td>
					<td><input class="input" type="text" id="skitter_height" name="skitter_height"  size="3" value="'.$skitter_height.'"><span> Высота блока: <b>'.$block_height.'</b> пикселей или процентов(%)</span></td>
				</tr>
				<tr>
					<td width="200" height="25">Ширина слайдера</td>
					<td><input class="input" type="text" id="skitter_width" name="skitter_width" size="3" value="'.$skitter_width.'"><span> Ширина блока: <b>'.$block_width.'</b> пикселей или процентов(%)</span></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<div id="leftaccordion" class="left_list slider_skitter_settings">
				<div class="left_head slider_skitter_head_settings">Дополнительные настройки</div>
				<div class="left_body" style="padding:10px;">
					<table class="admin_table_2">
						<tr>
							<td width="190" height="25">Тип анимации:</td>
							<td>
								<select class="input" name="animation">
									<option value="random" '.$animation_select_1.'>Случайный эффект</option>
									<option value="fade" '.$animation_select_2.'>Затухание</option>
									<option value="cube" '.$animation_select_3.'>Квадратами</option>
									<option value="cubeRandom" '.$animation_select_4.'>Квадратами с наплывом</option>
									<option value="block" '.$animation_select_5.'>Жалюзи слева</option>
									<option value="cubeStop" '.$animation_select_6.'>Квадратами с растворением слева</option>
									<option value="cubeHide" '.$animation_select_7.'>Бегущей полоской слева</option>
									<option value="cubeSize" '.$animation_select_8.'>Квадратами с растворением слева - диагональю</option>
									<option value="horizontal" '.$animation_select_9.'>Горизонтально справа - диагональю</option>
									<option value="showBars" '.$animation_select_10.'>Бегущей полоской слева - диагональю</option>
									<option value="showBarsRandom" '.$animation_select_11.'>Вертикальные полосы - случайно</option>
									<option value="tube" '.$animation_select_12.'>Вертикальные полосы - встряска</option>
									<option value="fadeFour" '.$animation_select_13.'>Перекрёстное растворение</option>
									<option value="paralell" '.$animation_select_14.'>Вертикальные полосы - сверху, от центра</option>
									<option value="blind" '.$animation_select_15.'>Жалюзи от центра</option>
									<option value="blindHeight" '.$animation_select_16.'>Вертикальные полоски слева - диагональю</option>
									<option value="blindWidth" '.$animation_select_17.'>Горизонтальные полосы слева</option>
									<option value="directionTop" '.$animation_select_18.'>Вертикальные полосы вверх</option>
									<option value="directionBottom" '.$animation_select_19.'>Вертикальные полосы вниз</option>
									<option value="directionRight" '.$animation_select_20.'>Вертикальные полосы вправо</option>
									<option value="directionLeft" '.$animation_select_21.'>Вертикальные полосы влево</option>
									<option value="cubeStopRandom" '.$animation_select_22.'>Квадратами с размытием</option>
									<option value="cubeSpread" '.$animation_select_23.'>Мозайка перемещение</option>
									<option value="cubeJelly" '.$animation_select_24.'>Мозайка переход</option>
									<option value="glassCube" '.$animation_select_25.'>Мозайка стыковка в середине</option>
									<option value="glassBlock" '.$animation_select_26.'>Жалюзи слева</option>
									<option value="circles" '.$animation_select_27.'>Круги из центра</option>
									<option value="circlesInside" '.$animation_select_28.'>Круги с краёв</option>
									<option value="circlesRotate" '.$animation_select_29.'>Круги с краёв с поворотом</option>
									<option value="cubeShow" '.$animation_select_30.'>Квадраты переход</option>
									<option value="upBars" '.$animation_select_31.'>Полосы снизу</option>
									<option value="downBars" '.$animation_select_32.'>Полосы сверху</option>
									<option value="hideBars" '.$animation_select_33.'>Полосы слева</option>
									<option value="swapBars" '.$animation_select_34.'>Полосы наплыв</option>
									<option value="swapBarsBack" '.$animation_select_35.'>Полосы наплыв 2</option>
									<option value="swapBlocks" '.$animation_select_36.'>Полосы снизу скачек</option>
									<option value="cut" '.$animation_select_37.'>Полосы снизу скачек 2</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="190" height="25">Стиль слайдера:</td>
							<td>
								<select class="input" name="style_slider">
										<option value="0" '.$style_slider_select_0.'>Основной</option>
										<option value="1" '.$style_slider_select_1.'>Лента превью</option>
										<option value="2" '.$style_slider_select_2.'>Номера</option>
										<option value="3" '.$style_slider_select_3.'>Точки</option>
										<option value="4" '.$style_slider_select_4.'>Точки + превью</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="190" height="25">Отображать описание:</td>
							<td><input type="checkbox" name="label" '.$label_check.' value="1"></td>
						</tr>
						<tr>
							<td width="190" height="25">Отображать кнопку слайдшоу:</td>
							<td><input type="checkbox" name="button_slide" '.$button_slide_check.' value="1"></td>
						</tr>
						<tr>
							<td width="190" height="25">Отображать кнопки навигации:</td>
							<td><input type="checkbox" name="button_nav" '.$button_nav_check.' value="1"></td>
						</tr>
						<tr>
							<td width="190" height="25">Время отображения изображения (мс):</td>
							<td>
								<select class="input" name="time_img">
										<option value="1" '.$time_img_select_1.'>1000</option>
										<option value="2" '.$time_img_select_2.'>1500</option>
										<option value="3" '.$time_img_select_3.'>2500</option>
										<option value="4" '.$time_img_select_4.'>3500</option>
										<option value="5" '.$time_img_select_5.'>5000</option>
										<option value="6" '.$time_img_select_6.'>7000</option>
										<option value="7" '.$time_img_select_7.'>10000</option>
										<option value="8" '.$time_img_select_8.'>Не анимировать</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="190" height="25">Скорость анимации:</td>
							<td>
								<select class="input" name="speed_animation">
									<option value="10" '.$speed_animation_1.'>Очень быстрая</option>
									<option value="7" '.$speed_animation_2.'>Быстрая</option>
									<option value="5" '.$speed_animation_3.'>Средняя</option>
									<option value="3" '.$speed_animation_4.'>Медленная</option>
									<option value="2" '.$speed_animation_5.'>Очень медленная</option>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div>&nbsp;</div>
			';

			// ======= ВЫВОДИМ ФОРМУ РЕДАКТИРОВАНИЯ ИЗОБРАЖЕНИЙ ==========================================

			// загрузка XML-файла изображений.
			$xmlslider_skitter= simplexml_load_file('modules/slider_skitter/frontend/settings.xml');

			if ($xmlslider_skitter) // если существует файл
			{
				$i = 0;
				//foreach ($xmlslider_skitter->slide as $b) // разбиваем foreach ($xmlthemes->images as $image)
				for ( $i=0; $i<=9; $i++ )
				{

					$skitter_slide_file = $xmlslider_skitter->slide[$i]->image[0];
					$skitter_slide_description = $xmlslider_skitter->slide[$i]->label[0];
					$skitter_slide_link = $xmlslider_skitter->slide[$i]->link[0];


					if (isset($skitter_slide_file)){$checked = 'checked';} else{$checked = '';}

					echo '
					<div class="imglist" style="background: #e6e6e6;">
					<table>
						<tr>
							<td width="200" height="25">Показать изображение:</td>
							<td><input onclick="skitter_display_block('.$i.')" id="skitter_check_'.$i.'" '.$checked.' type="checkbox" name="image_pub['.$i.']" value="1"></td>
						</tr>
					</table>
					<table id="skitter_block_'.$i.'">
						<tr>
							<td colspan="2"><div id="imgout_'.$i.'"></div></td>
						</tr>
						<tr>
							<td height="25">Изображение</td>
							<td>
								<select class="input" id="selectimg_'.$i.'" name="image_src[]" onChange="skitter_display_image('.$i.')">
								';

								if ($dir = opendir("files/slider_skitter"))
								{
									while (false !== ($file = readdir($dir)))
									{
										// разбиваем на расширения
										$l_file = strtolower($file); // переводим в нижний регистр
										$file_arr = preg_split('/\./', $l_file, -1 , PREG_SPLIT_NO_EMPTY);
										$name = $file_arr[0]; // имя файла.
										$ext = $file_arr[1];  // расширение.

										if ($ext =="jpg" || $ext =="jpeg" || $ext =="gif" || $ext =="png" )
										{
											// selected
											if ('/files/slider_skitter/'.$l_file == $skitter_slide_file){$selected = 'selected';}
											else {$selected = '';}

											echo '<option '.$selected.'>'.$file.'</option>';
										}
									}
									closedir($dir);
								}

								echo '
								</select>

								<script type="text/javascript">
									skitter_display_image('.$i.');
									skitter_display_block('.$i.');
								</script>
								</td>
							</tr>
							<tr>
							<td colspan="2">
								<p>
								<table border="1" width="500" cellspacing="0" style="border-collapse: collapse;" class="slider_skitter_message">
								<tr>
									<td>&nbsp;</td>
									<td>Изображение</td>
									<td>Блок</td>
								</tr>
								<tr>
									<td>Высота (px)</td>
									<td><span id="img_height_'.$i.'"></td>
									<td><span id="block_height_'.$i.'">'.$block_height.'</span></td>
								</tr>
								<tr>
									<td>Ширина (px)</td>
									<td><span id="img_width_'.$i.'"></span></td>
									<td><span id="block_width_'.$i.'">'.$block_width.'</span></td>
								</tr>
								</table>

								<div id="message_height'.$i.'"></div>
								<div id="message_width'.$i.'"></div>

								</p>

							</td>
							</tr>
							<tr>
								<td style="vertical-align:middle;" height="25">Описание:</td>
								<td><input class="input" type="text" name="image_desc[]" size="50" value="'.$skitter_slide_description.'"></td>
							</tr>
							<tr>
								<td style="vertical-align:middle;" height="25">Ссылка:</td>
								<td><input class="input" type="text" name="image_link[]" size="50" value="'.$skitter_slide_link.'"></td>
							</tr>
						</table>
						<div>&nbsp;</div>
						</div>
						<div>&nbsp;</div>
					';
				};
			}

			// ======= /  ВЫВОДИМ ФОРМУ РЕДАКТИРОВАНИЯ ИЗОБРАЖЕНИЙ ==========================================
			echo
			'
			&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
			<br>
			</form>
			</div>
			';

		} // конец проверки 'enabled'
		else
		{
			echo '<div id="main-top">Модуль "slider_skitter" не подключён</div>';
		}
	} // конец функции
}

?>
