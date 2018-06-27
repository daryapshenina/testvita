<?php
// DAN обновление - ямарт 2015
// 4 модуля изображений

defined('AUTH') or die('Restricted access');

if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else{$mod_pub = '';}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else{$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else{$mod_ordering = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else{$bt_save = '';} // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else{$bt_prim = '';} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';} // кнопка 'Отменить'

// Условие - отменить
if($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;}

// Условие публикации
if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";} else{$mod_pub = "1";}

// выбираем действие над модулем
if(isset($admin_d3) && $admin_d3 == "update")
{
	$strPathImage = '';
	$strLink = '';
	for($i = 0;$i < 16;$i++)
	{
		$r = array("http://", "https://");
		if($_POST['img4r_image_'.$i] != ''){$strPathImage .= $_POST['img4r_image_'.$i].'#'.str_replace($r, '', $_POST['img4r_link_'.$i]).'#'.$_POST['img4r_anchor_'.$i].';';}
	}

	// Обновляем данные в таблице "modules"
	$query_update_sql = "UPDATE `modules` SET `pub` = '".$mod_pub."', `block` = '".$mod_block."', `ordering` = '".$mod_ordering."', `content` = '".$strPathImage."' WHERE `module` = 'img4r'";

	mysql_query($query_update_sql) or die ("Невозможно обновить данные");

	if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
	else {Header ("Location: /admin/modules/img4r"); exit;}
}
else
{
	function a_com()
	{
		global $site, $i, $skitter_image_src, $slider_skitter_block;

		// вывод содержимого модуля
		$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'img4r'") or err_mail("Невозможно сделать выборку из таблицы - mod > img4r");

		while($m = mysql_fetch_array($num)):
			$id = $m['id'];
			$title = $m['title'];
			$pub = $m['pub'];
			$enabled = $m['enabled'];
			$description = $m['description'];
			$content = $m['content'];
			$ordering = $m['ordering'];
			$block =	$m['block'];
			$row_1 = $m['p1'];
			$row_2 = $m['p2'];
			$row_3 = $m['p3'];
			$row_4 = $m['p4'];
			$row_5 = $m['p5'];
		endwhile;

		// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
		$block_option = '';
		$block_query = mysql_query("SELECT * FROM `block`") or die ("Невозможно сделать выборку из таблицы - 1");
		$result = mysql_num_rows($block_query);
		if ($result > 0)
		{
			while($b = mysql_fetch_array($block_query)):
				$b_id = $b['id'];
				$b_name = $b['block'];
				$b_description = $b['description'];
				if ($b_name == $block){$selected = 'selected';} else {$selected = '';}
				$block_option .= '<option '.$selected.' value='.$b_name.'>'.$b_description.'</option>';
			endwhile;
		}
		// ======== / загрузка блоков вывода =======


		// устанавливаем признак публикации
		if ($pub == 1){$pub = "checked";} else{$pub = "";}
		
		// ======= ВЫВОДИМ ФОРМУ РЕДАКТИРОВАНИЯ ИЗОБРАЖЕНИЙ ==========================================
		$image_arr = explode(';', $content);

		$out = '';
		
		for($row = 1;$row < 5;$row++)
		{
			$out .= '
				<div class="mod_imglist">
					<div class="mod_img4r_title">Ряд изображений №'.$row.'</div>
					<table class="mod_img4r_tab">
						<tr>
			';

			for($col = 1;$col < 5;$col++)
			{
				$num = (($row - 1) * 4) + $col - 1;
				
				if (isset($image_arr[$num]))
				{
					$img_arr = explode('#', $image_arr[$num]);
					$img_src = $img_arr[0];
					if(isset($img_arr[1])){$img_link = $img_arr[1];} else{$img_link = '';}
					if(isset($img_arr[2])){$img_anchor = $img_arr[2];} else{$img_anchor = '';}
				}					

				/* Боюсь это место, надо что то придумать с http. Можно вырезать http на JS */
				$r = array("http://", "https://");
				if(is_file($_SERVER['DOCUMENT_ROOT'].str_replace($r.$site, '', $img_src)))
				{
					$pathToImg = 'url('.$img_src.')';
					$buttonHidden = 'display';
					$text = '';
				}
				else
				{
					$pathToImg = 'none';
					$buttonHidden = 'none';
					$text = 'Выбрать файл';
				}

				$out .= '
					<td>
						<div class="img4r_img" id="img4r_img_'.$num.'" style="background-image:'.$pathToImg.';" onclick="changeImage('.$num.');">'.$text.'</div>
						<input id="img4r_value_'.$num.'" class="input" name="img4r_image_'.$num.'" type="hidden" value="'.$img_src.'" />
						<div id="img4r_anc_'.$num.'" class="img4r_center" style="display:'.$buttonHidden.';"><input id="img4r_anchor_'.$num.'" class="img4r_input" name="img4r_anchor_'.$num.'" type="text" value="'.$img_anchor.'" placeholder="текст ссылки"></div>
						<div id="img4r_inp_'.$num.'" class="img4r_center" style="display:'.$buttonHidden.';"><input id="img4r_input_'.$num.'" class="img4r_input" name="img4r_link_'.$num.'" type="text" value="'.$img_link.'" placeholder="адрес ссылки"></div>
						<div id="img4r_button_'.$num.'" class="img4r_button_delete" onclick="deleteImage('.$num.');" style="display:'.$buttonHidden.';">Удалить</div>
					</td>
				';

			}

			$out .= '
						</tr>
					</table>
					<div>&nbsp;</div>
				</div>
				<div>&nbsp;</div>
			';
		}		
		

		// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
		if ($enabled == "1")
		{
			echo "
			<script type='text/javascript'>

				var numberImage = 0;
				
				 CKEDITOR.tools = 
				 {
					callFunction:function(funcNum, url, message) 
					{
						document.getElementById('img4r_img_'+numberImage).style.backgroundImage = 'url('+url+')';
						document.getElementById('img4r_img_'+numberImage).innerHTML = '';
						document.getElementById('img4r_value_'+numberImage).value = url;
						document.getElementById('img4r_button_'+numberImage).style.display = 'block';
						document.getElementById('img4r_inp_'+numberImage).style.display = 'block';
						document.getElementById('img4r_anc_'+numberImage).style.display = 'block';
					}
				 }				


				function changeImage(num)
				{
					numberImage = num;
					window.open('/administrator/plugins/browser/dan_browser.php?CKEditor=editor1&CKEditorFuncNum=2&langCode=ru');
				}

				function deleteImage(num)
				{
					document.getElementById('img4r_img_'+num).style.backgroundImage = 'none';
					document.getElementById('img4r_img_'+num).innerHTML = 'Выбрать файл';
					document.getElementById('img4r_anchor_'+num).value = '';
					document.getElementById('img4r_anc_'+num).style.display = 'none';
					document.getElementById('img4r_input_'+num).value = '';
					document.getElementById('img4r_inp_'+num).style.display = 'none';					
					document.getElementById('img4r_value_'+num).value = '';
					document.getElementById('img4r_button_'+num).style.display = 'none';
				}

			</script>
			";

			echo '
			<link rel="stylesheet" href="/modules/img4r/admin/style.css" type="text/css" />
			<div class="container">
				<h1><img border="0" src="/modules/img4r/admin/images/ico.png" style="float:left;margin-right:10px"/>Модуль "Адаптивные изображения"</h1>

				<form method="POST" action="/admin/modules/img4r/update/">

				<table class="admin_table_2">
					<tr>
						<td style="width:200px;">Опубликовать модуль</td>
						<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
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
						<td>Порядок следования</td>
						<td><input class="input" type="number" name="ordering" value="'.$ordering.'" style="width:80px;"></td>
					</tr>
				</table>
				<div>&nbsp;</div>
				'.$out.'
				<div style="margin:40px 0px 60px 0px;">
				<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
				</div>
				</form>
				</div>
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
