<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/flat_rotate/admin/style.css');
$head->addFile('/modules/flat_rotate/admin/tmp.js');
include_once __DIR__.'/lang/'.LANG.'.php';

$id = $d[3];


$stmt_flat_modules = $db->prepare("SELECT * FROM modules WHERE id = :id LIMIT 1");
$stmt_flat_modules->execute(array('id' => $id));
$m = $stmt_flat_modules->fetch();	

switch($m['effect'])
{
	case 1: $head->addFile('/modules/flat_rotate/frontend/style_1.css'); break;
	case 2: $head->addFile('/modules/flat_rotate/frontend/style_2.css'); break;	
	default: 
		$head->addFile('/modules/flat_rotate/frontend/style_0.css');
		$head->addFile('/modules/flat_rotate/frontend/flat_rotate.js');
}	

function a_com()
{
	global $root, $db, $domain, $m;

	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	$block_query = $db->query("SELECT * FROM block");

	$block_option = '';
	if($block_query->rowCount() > 0)
	{
		while($b = $block_query->fetch())
		{
			$b_id = $b['id'];
			$b_name = $b['block'];
			$b_description = $b['description'];

			if ($b_name == $m['block']){$selected = 'selected';} else {$selected = '';}
			$block_option .= '<option '.$selected.' value='.$b_name.'>'.$b_description.'</option>';
		}
	}
	// ======== / загрузка блоков вывода =======

	// устанавливаем признак публикации заголовка модуля
	if ($m['titlepub'] == 1){$titlepub = "checked";} else{$titlepub = "";}

	// устанавливаем признак публикации
	$pub_0 = $pub_1 = $pub_2 = $pub_3 = '';

	switch($m['pub'])
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
	
	$effect_arr = array_fill(0, 3, '');
	$effect_arr[intval($m['effect'])] = 'selected';

	// ширина блока с отступом
	$width_td = $m['p1'] + 20;

	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{
		echo '
		<div class=""container">
			<h1><img border="0" src="/modules/flat_rotate/admin/images/ico.png" style="float:left; margin-right:10px;" />'.LANG_M_FLAT_ROTATE_MAIN_TITLE.'</h1>

			<form method="POST" action="/admin/modules/flat_rotate/'.$m['id'].'/update">
			<table class="admin_table_2">
				<tr>
					<td style="width:200px;">'.LANG_M_FLAT_ROTATE_TITLE.'</td>
					<td><input class="input" type="text" name="title" size="20" value="'.$m['title'].'"></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_DESCRIPTION.'</td>
					<td>'.$m['description'].'</td>
				</tr>
				<tr>
					<td width="200" height="25">'.LANG_M_FLAT_ROTATE_VIEW_MODULE.'</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub_1.'>'.LANG_M_FLAT_ROTATE_VIEW_MODULE_ON.'</option>
							<option value="2" '.$pub_2.'>'.LANG_M_FLAT_ROTATE_VIEW_MODULE_PC.'</option>
							<option value="3" '.$pub_3.'>'.LANG_M_FLAT_ROTATE_VIEW_MODULE_PHONE.'</option>
							<option value="0" '.$pub_0.'>'.LANG_M_FLAT_ROTATE_VIEW_MODULE_OFF.'</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_POSITION.'</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						&nbsp;'.LANG_M_FLAT_ROTATE_POSITION_DESCRIPTION.'
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_EFFECT.'</td>
					<td>
						<select class="input" size="1" name="effect">
							<option value="0" '.$effect_arr[0].'>Поворот</option>
							<option value="1" '.$effect_arr[1].'>Наложение с прозрачностью</option>
							<option value="2" '.$effect_arr[2].'>Всплывающий задний фон</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_ORDER.'</td>
					<td><input class="input" type="number" name="ordering" size="3" value="'.$m['ordering'].'" style="width:80px;"></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_SIZE.'</td>
					<td><input id="flat_width" class="input" type="number" min="150" max="600" name="width" size="3" value="'.$m['p1'].'" style="width:80px;"> х <input id="flat_height" class="input" type="number" min="100" max="600" name="height" size="3" value="'.$m['p2'].'" style="width:80px;"></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_MARGIN.'</td>
					<td><input id="margin_w" class="input" type="number" min="0" max="100" name="margin_w" size="3" value="'.$m['p5'].'" style="width:80px;"> / <input id="margin_h" class="input" type="number" min="0" max="100" name="margin_h" size="3" value="'.$m['p6'].'" style="width:80px;"></td>
				</tr>					
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_COLOR.'</td>
					<td><input id="color" type="color" name="color" value="'.$m['p3'].'" autocomplete="on" onchange="color_value();"></td>
				</tr>					
				<tr>
					<td>'.LANG_M_FLAT_ROTATE_LINK.'</td>
					<td><input type="text" class="input" size="50" name="link" value="'.$m['p4'].'"></td>
				</tr>				
			</table>
			<div>&nbsp;</div>
			<table>
				<tr>
					<td><div style="text-align:center; padding:0px 0px 5px 0px; font-weight:">Видимая область</div></td>
					<td style="width:10px;"></td>
					<td><div style="text-align:center;">Область при анимации</div></td>
					<td style="width:50px"></td>
					<td><div style="text-align:center;">Результат, текущее состояние</div></td>
				</tr>
				<tr>
					<td><div id="editor1" class="editor_div" contenteditable="true" style="width:'.($m['p1'] - 40).'px; height:'.($m['p2'] - 40).'px; background-color:#fff;">'.$m['content'].'</div></td>
					<td></td>
					<td><div id="editor2" class="editor_div" contenteditable="true" style="width:'.($m['p1'] - 40).'px; height:'.($m['p2'] - 40).'px; background-color:'.$m['p3'].';">'.$m['content_2'].'</div></td>
					<td></td>
					<td>
						<div class="mod_flat_rotate_result_container">
				';

				$frontend_edit = 0;
				include_once($root.'/modules/flat_rotate/frontend/main.php');
				
				echo'
						</div>					
					</td>
				</tr>
			</table>

			<script type="text/javascript">
				CKEDITOR.disableAutoInline = true;
				CKEDITOR.inline("editor1",
					{
						filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
					});
				CKEDITOR.inline("editor2",
					{
						filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
					});
			</script>
			<input type="hidden" name="id" value="'.$m['id'].'">
			<div style="margin-top:40px;">
			<input id="button_save" class="greenbutton" type="submit" value="'.LANG_M_FLAT_ROTATE_SAVE.'" name="bt_save">&nbsp;<input id="button_apply" class="bluebutton" type="submit" value="'.LANG_M_FLAT_ROTATE_ACCEPT.'" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_FLAT_ROTATE_CANCEL.'" name="bt_none">
			</div>
			<input id="editor1_input" name="editor1" type="hidden">
			<input id="editor2_input" name="editor2" type="hidden">
			</form>
		</div>

		<script type="text/javascript">

			var flat_width = document.getElementById("flat_width");
			flat_width.onchange = function(){
				document.getElementById("editor1").style.width = flat_width.value - 40 + "px";
				document.getElementById("editor2").style.width = flat_width.value - 40 + "px";
				document.getElementById("result").style.width = flat_width.value - 40 + "px";
			}

			var flat_height = document.getElementById("flat_height");
			flat_height.onchange = function(){
				document.getElementById("editor1").style.height = flat_height.value - 40 + "px";
				document.getElementById("editor2").style.height = flat_height.value - 40 + "px";
				document.getElementById("result").style.height = flat_height.value - 40 + "px";
			}
			
			var button_save = document.getElementById("button_save");
			var button_apply = document.getElementById("button_apply");
			button_save.onclick = hidden;
			button_apply.onclick = hidden;				
			function hidden()
			{
				document.getElementById("editor1_input").value = document.getElementById("editor1").innerHTML;
				document.getElementById("editor2_input").value = document.getElementById("editor2").innerHTML;					
			}

		</script>

		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">'.LANG_M_FLAT_ROTATE_MODULE_OFF.'</div>';
	}
} // конец функции


?>