<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';
include_once $root.'/classes/Auth.php';

$time_encode = Auth::encode(time());

$class_out = $m['effect_a'];
$text = '';
$field_1 = '';
$field_2 = '';
$field_3 = '';
$file = '';
$captcha = '';

$w_arr = explode(';', $m['width_p']);
if(count($w_arr) == 4)
{
	if(!($w_arr[0] == 100 && $w_arr[1] == 100 && $w_arr[2] == 100 && $w_arr[3] == 100))
	{
		$class_out .= ' col_d_'.$w_arr[0].' col_n_'.$w_arr[1].' col_t_'.$w_arr[2].' col_p_'.$w_arr[3];
	}
}

if(Settings::instance()->getValue('personal_information') == 1)
{
	$personal_information = '<tr><td colspan="2"><div class="mod_form_p_i_'.$m['p10'].'"><input required checked title="Вы должны дать согласие перед отправкой" type="checkbox">Отправляя персональные данные из данной формы, я даю согласие на <a href="/personal-information">обработку персональных данных</a></div></td></tr>';
}
else{$personal_information = '';}


// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = 'edit_mode ';
	$edit_data = 'data-type="mod_form" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}


// Вертикальный вывод модуля
if($m['p10'] == 1)
{
	if ($m['titlepub'] == 1){$title_out = '<div class="mod_title'.$m['module_csssuf'].'">'.$m['title'].'</div>';} else {$title_out = '';}	// Заголовок модуля
	if($m['content'] == 1) $text = '<div class="mod_form_text">'.$m['content_2'].'</div>';


	// Тема
	if ($m['p1'] == 1)
	{
		$field_1 =
		'<tr>
			<td colspan="2"><input class="mod_form_input input" type="text" name="field_1" value="" placeholder="'.$m['p2'].'"></td>
		</tr>
		';
	}

	if ($m['p3'] == 1)
	{
		$field_2 =
		'<tr>
			<td colspan="2"><textarea class="mod_form_textarea input" rows="3" name="field_2"  placeholder="'.$m['p4'].'"></textarea></td>
		</tr>
		';
	}

	if ($m['p5'] == 1)
	{
		$field_3 =
		'<tr>
			<td colspan="2"><input class="mod_form_input input" type="text" name="field_3" value="" placeholder="'.$m['p6'].'" required></td>
		</tr>
		';
	}

	if ($m['p7'] == 1)
	{
		$file =
		'<tr>
			<td colspan="2" style="height:40px;"><input class="mod_form_file" type="file" name="file"></td>
		</tr>
		';
	}

	if ($m['p8'] == 1)
	{
		$rand = rand(5, 10);
		$captcha =
		'<tr class="mod_form_captcha_tr">
			<td style="width:110px;"><img alt="" class="captcha_img" src="/administrator/captcha/pic.php?'.$rand.'" style="max-height:none;max-width:none;"></td>
			<td><input class="input mod_form_captcha" type="text" name="code" value="" placeholder="'.LANG_M_FORM_FOUR_NUMBER.'" title="'.LANG_M_FORM_FOUR_NUMBER.'" required pattern="[0-9]{4}" ></td>
		</tr>
		';
	}

	if($m['margin_w'] != 0 || $m['margin_h'] != 0){$margin = ' style="margin:'.$m['margin_h'].'px '.$m['margin_w'].'px;"';}
	else{$margin = '';}

	echo
	'<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'mod_form_main'.$m['module_csssuf'].' '.$class_out.'"'.$margin.'>
		<div class="mod_form_content"  style="background-color:'.$m['bg_color'].'">
			<form method="post" action="/form/mod_mail" enctype="multipart/form-data">
				'.$title_out.'
				'.$text.'
				<table class="mod_form_table">
					'.$field_1.'
					'.$field_2.'
					'.$field_3.'
					'.$file.'
					'.$captcha.'
					'.$personal_information.'
					<tr>
						<td colspan="2"><input type="submit" value="'.LANG_M_FORM_SEND.'" name="button" class="mod_form_button" style="background-color:'.$m['color'].'"></td>
					</tr>
				</table>
				<input class="mod_form_lastname" type="text" name="lastname" value="">
				<input type="hidden" name="m" value="'.$time_encode.'">
				<input type="hidden" name="id" value="'.$m['id'].'">
			</form>
		</div>
	</div>
	';
}


// Горизонтальный вывод модуля
if($m['p10'] == 2)
{
	if($m['content'] == 1) $text = '<div class="mod_form_text_2">'.$m['content_2'].'</div>';

	// Тема
	if ($m['p1'] == 1){$field_1 = '<div><input class="mod_form_input_2 input" type="text" name="field_1" value="" placeholder="'.$m['p2'].'"></div>';}
	if ($m['p3'] == 1){$field_2 ='<div><textarea class="mod_form_textarea_2" rows="1" name="field_2"  placeholder="'.$m['p4'].'"></textarea></div>';}
	if ($m['p5'] == 1){$field_3 = '<div><input class="mod_form_input_2 input" type="text" name="field_3" value="" placeholder="'.$m['p6'].'" required></div>';}
	if ($m['p7'] == 1){$file = '<div><input class="mod_form_file_2" type="file" name="file"></div>';}
	if ($m['p8'] == 1)
	{
		$rand = rand(5, 10);
		$captcha =
		'<table class="mod_form_table_2">
			<tr>
				<td style="width:110px;"><img alt="" class="captcha_img_2" src="/administrator/captcha/pic.php?'.$rand.'" style="max-height:none;max-width:none;"></td>
				<td><input class="input input_captcha_2" type="text" name="code" size="5" value="" placeholder="'.LANG_M_FORM_FOUR_NUMBER.'" title="'.LANG_M_FORM_FOUR_NUMBER.'" required pattern="[0-9]{4}" ></td>
			</tr>
		</table>
		';
	}

	if($m['margin_w'] != 0 || $m['margin_h'] != 0){$margin = 'margin:'.$m['margin_h'].'px '.$m['margin_w'].'px;';}
	else{$margin = '';}

	echo
	'<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'mod_form_main'.$m['module_csssuf'].'" style="width:100%;'.$margin.'">
		<div class="mod_form_content_2"  style="background-color:'.$m['bg_color'].'">
			<form method="post" action="/form/mod_mail" enctype="multipart/form-data">
				'.$text.'
				<div class="mod_form_i_2">
					'.$field_1.'
					'.$field_2.'
					'.$field_3.'
					'.$file.'
					'.$captcha.'
					<div><input type="submit" value="'.LANG_M_FORM_SEND.'" name="button" class="mod_form_button_2" style="background-color:'.$m['color'].'"></div>
					<input class="mod_form_lastname_2" type="text" name="lastname" value="">
					<input type="hidden" name="m" value="'.$time_encode.'">
					<input type="hidden" name="id" value="'.$m['id'].'">
				</div>
				'.$personal_information.'
			</form>
		</div>
	</div>
	';
}


// Вывод по кнопке.
if($m['p10'] == 3)
{
	if ($m['titlepub'] == 1){$title_out = '<div class="mod_form_container_3'.$class_out.'"><div onclick="mod_form_modal_'.$m['id'].'(mod_form_content_'.$m['id'].')" class="mod_form_but_modal_3'.$m['module_csssuf'].'" style="color:'.$m['color'].';background-color:'.$m['bg_color'].';">'.$m['title'].'</div></div>';} else {$title_out = '';}	// Заголовок модуля
	if($m['content'] == 1) $text = '<div class="mod_form_container_3'.$class_out.'"><div class="mod_form_text">'.$m['content_2'].'</div></div>';


	// Тема
	if ($m['p1'] == 1)
	{
		$field_1 ='<tr><td colspan="2"><input class="mod_form_input_3 input" type="text" name="field_1" value="" placeholder="'.$m['p2'].'"></td></tr>';
	}

	if ($m['p3'] == 1)
	{
		$field_2 ='<tr><td colspan="2"><textarea class="mod_form_textarea_3 input" rows="3" name="field_2"  placeholder="'.$m['p4'].'"></textarea></td></tr>';
	}

	if ($m['p5'] == 1)
	{
		$field_3 ='<tr><td colspan="2"><input class="mod_form_input_3 input" type="text" name="field_3" value="" placeholder="'.$m['p6'].'" required></td></tr>';
	}

	if ($m['p7'] == 1)
	{
		$file ='<tr><td colspan="2" style="height:40px;"><input class="mod_form_file_3" type="file" name="file"></td></tr>';
	}

	if ($m['p8'] == 1)
	{
		$rand = rand(5, 10);
		$captcha = '<tr class="mod_form_captcha_tr_3"><td><img alt="" class="mod_form_captcha_img_3" src="/administrator/captcha/pic.php?'.$rand.'" style="max-height:none;max-width:none;"></td><td><input class="input mod_form_input_captcha_3" type="text" name="code" value="" placeholder="'.LANG_M_FORM_FOUR_NUMBER.'" title="'.LANG_M_FORM_FOUR_NUMBER.'" required pattern="[0-9]{4}" ></td></tr>';
	}


	if($m['margin_w'] != 0 || $m['margin_h'] != 0){$margin = ' style="margin:'.$m['margin_h'].'px '.$m['margin_w'].'px;"';}
	else{$margin = '';}

	echo
	'<script type="text/javascript">

	var mod_form_content_'.$m['id'].' = \'<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'mod_form_main_3'.$m['module_csssuf'].' "'.$margin.'>\';
	mod_form_content_'.$m['id'].' += \'<div class="mod_form_content_3">\';
	mod_form_content_'.$m['id'].' += \'<form method="post" action="/form/mod_mail" enctype="multipart/form-data">\';
	mod_form_content_'.$m['id'].' += \''.$text.'\';
	mod_form_content_'.$m['id'].' += \'<table class="mod_form_table_3">\';
	mod_form_content_'.$m['id'].' += \''.$field_1.$field_2.$field_3.$file.$captcha.$personal_information.'\';
	mod_form_content_'.$m['id'].' += \'<tr>\';
	mod_form_content_'.$m['id'].' += \'<td colspan="2"><input type="submit" value="'.$m['title'].'" name="button" class="mod_form_but_modal_3'.$m['module_csssuf'].'" style="color:'.$m['color'].';background-color:'.$m['bg_color'].';"></td>\';
	mod_form_content_'.$m['id'].' += \'</tr>\';
	mod_form_content_'.$m['id'].' += \'</table>\';
	mod_form_content_'.$m['id'].' += \'<input class="mod_form_lastname_3" type="text" name="lastname" value="">\';
	mod_form_content_'.$m['id'].' += \'<input type="hidden" name="m" value="'.$time_encode.'">\';
	mod_form_content_'.$m['id'].' += \'<input type="hidden" name="id" value="'.$m['id'].'">\';
	mod_form_content_'.$m['id'].' += \'</form>\';
	mod_form_content_'.$m['id'].' += \'</div>\';
	mod_form_content_'.$m['id'].' += \'</div>\';

	function mod_form_modal_'.$m['id'].'(_id)
	{
		DAN.modal.add(mod_form_content_'.$m['id'].',300);
	}
	</script>

	'.$title_out.'
	';
}
