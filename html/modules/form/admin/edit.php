<?php
defined('AUTH') or die('Restricted access');
include_once __DIR__.'/lang/'.LANG.'.php';

include_once($root.'/administrator/modules/classes/Admin.php');

$head->addFile('/modules/form/admin/style.css');
$head->addFile('/modules/form/admin/tmp.js');

// id модуля
$mod_id  = intval($d[3]);
$module = new ModulesAdmin($mod_id);

function a_com()
{
	global $db, $domain, $mod_id, $module, $effects;
	
	$m = $module->get_array();

	$type_select = array_fill(0, 4, '');
	$type_select[$m['p10']] = 'selected="selected"';

	// устанавливаем признак для полей
	if ($m['titlepub'] == 1){$titlepub = 'checked';} else{$titlepub = '';}
	if ($m['content'] == 1){$text_check = 'checked';} else{$text_check = '';}	
	if ($m['p1'] == 1){$field_1_check = 'checked';} else{$field_1_check = '';}	
	if ($m['p3'] == 1){$field_2_check = 'checked';} else{$field_2_check = '';}
	if ($m['p5'] == 1){$field_3_check = 'checked';} else{$field_3_check = '';}
	if ($m['p7'] == 1){$file_check = 'checked';} else{$file_check = '';}	
	if ($m['p8'] == 1){$captcha_check = 'checked';} else{$captcha_check = '';}


	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{
		echo '
		<div class="container">
			<h1><img border="0" src="/modules/form/admin/images/ico.png" style="float:left; margin-right:10px;" />'.LANG_M_FORM_MAIN_TITLE.'</h1>
			<form method="POST" action="/admin/modules/form/'.$mod_id.'/update">
		</div>

		<div id="mod_form_accordion" class="seo_fon">
			<div class="accordion_head left_head">Основные настройки</div>
			<div class="accordion_body mod_form_ab1">
				<div>&nbsp;</div>
				<table class="admin_table_2">
					'.$module->pub().'
					'.$module->block().'
					<tr>
						<td style="width:230px;">Тип модуля</td>
						<td>
							<select class="input" name="type">
								<option value="1" '.$type_select[1].'>Вертикальный</option>
								<option value="2" '.$type_select[2].'>Горизонтальный</option>
								<option value="3" '.$type_select[3].'>Кнопка с вызовом модального окна</option>
							</select>
						</td>
					</tr>					
					'.$module->order().'
				</table>
			</div>

			<div class="accordion_head left_head">Размеры, эффект, цвет</div>
			<div class="accordion_body mod_form_ab2">
				<div>&nbsp;</div>
				<table class="admin_table_2">
					'.$module->width_prc().'
					'.$module->margin().'
					'.$module->effect_a().'
					'.$module->color().'
					'.$module->bg_color().'
				</table>				
			</div>
		</div>

		<div>&nbsp;</div>
		<table class="admin_table_2">
			<tr>
				<td style="width:150px;">'.LANG_M_FORM_TITLE.' <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="'.LANG_M_FORM_HELP.'">'.LANG_M_FORM_HELP_1.'</span></div></td>
				<td style="width:70px;"><input class="input" id="titlepub" name="titlepub" type="checkbox"  value="1" '.$titlepub.' ><label id="titlepub_label" for="titlepub"></label></td>
				<td><div id="editor1" class="editable_text max_width_360" contenteditable="true">'.$m['title'].'</div><input id="title" class="input" type="hidden" name="title" value="'.$m['title'].'"></td>
			</tr>
			<tr>
				<td>'.LANG_M_FORM_TEXT.' <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="'.LANG_M_FORM_HELP.'">'.LANG_M_FORM_HELP_1.'</span></div></td>
				<td><input class="input" id="text_pub" name="text_pub" type="checkbox"  value="1" '.$text_check.' ><label id="text_label" for="text_pub"></label></td>
				<td><div id="editor2" class="editable_text max_width_360" contenteditable="true">'.$m['content_2'].'</div><input id="text" type="hidden" name="text" value="'.htmlspecialchars($m['content_2']).'"></td>
			</tr>
			<tr>
				<td>'.LANG_M_FORM_FIELD.' 1</td>
				<td><input id="field_1_pub" class="input" name="field_1_pub" type="checkbox"  value="1" '.$field_1_check.' ><label id="field_1_label" for="field_1_pub"></label></td>
				<td><input id="field_1" class="input max_width_360" type="text" name="field_1" size="50" value="'.$m['p2'].'"></td>
			</tr>
			<tr>
				<td>'.LANG_M_FORM_FIELD.' 2</td>
				<td><input id="field_2_pub" class="input" name="field_2_pub" type="checkbox"  value="1" '.$field_2_check.' ><label id="field_2_label" for="field_2_pub"></label></td>
				<td><textarea id="field_2" class="input max_width_360" style="height:120px;" name="field_2">'.$m['p4'].'</textarea></td>
			</tr>
			<tr>
				<td>'.LANG_M_FORM_FIELD.' 3 - '.LANG_M_FORM_REQUIRED.'</td>
				<td><input id="field_3_pub" class="input" name="field_3_pub" type="checkbox"  value="1" '.$field_3_check.' ><label id="field_3_label" for="field_3_pub"></label></td>
				<td><input id="field_3" class="input max_width_360" type="text" name="field_3" size="50" value="'.$m['p6'].'"></td>
			</tr>
			<tr>
				<td>'.LANG_M_FORM_FILE.'</td>
				<td><input id="file_pub" class="input" name="file_pub" type="checkbox"  value="1" '.$file_check.' ><label id="file_label" for="file_pub"></label></td>
				<td><input id="file" class="max_width_360" type="button" size="50" value="'.LANG_M_FORM_FILE.'"></td>
			</tr>
			<tr>
				<td>'.LANG_M_FORM_CAPTCHA.'</td>
				<td><input id="captcha_pub" class="input" name="captcha_pub" type="checkbox"  value="1" '.$captcha_check.' ><label id="captcha_label" for="captcha_pub"></label></td>
				<td><img id="captcha" src="/administrator/captcha/pic.php" align="middle"></td>
			</tr>
		</table>
		
		<div style="margin-top:40px;">
			<input type="hidden" name="id" value="1" '.$m['id'].' >
			<input id="button_save" class="greenbutton" type="submit" value="'.LANG_M_FORM_SAVE.'" name="bt_save">&nbsp;<input id="button_apply" class="bluebutton" type="submit" value="'.LANG_M_FORM_ACCEPT.'" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_FORM_CANCEL.'" name="bt_none">
		</div>		

		<script type="text/javascript">
			DAN.accordion("accordion_head", "accordion_body");		
		
			CKEDITOR.disableAutoInline = true;
			e_editor_2 = CKEDITOR.inline("editor2",
				{
					filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
				});
				
				var button_save = document.getElementById("button_save");
				var button_apply = document.getElementById("button_apply");
				button_save.onclick = hidden;
				button_apply.onclick = hidden;				
				function hidden()
				{
					document.getElementById("title").value = document.getElementById("editor1").innerHTML;
					document.getElementById("text").value = document.getElementById("editor2").innerHTML;					
				}

		</script>		

		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">'.LANG_M_FORM_MODULE_OFF.'</div>';
	}
}

?>