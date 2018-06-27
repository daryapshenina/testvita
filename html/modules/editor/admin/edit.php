<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/administrator/modules/effects.php');

$head->addFile('/modules/editor/admin/style.css');
$head->addFile('/modules/editor/admin/tmp.js');

// id модуля
$mod_id  = intval($d[3]);

function a_com()
{
	global $db, $domain, $mod_id, $effects;

	$stmt_modules = $db->prepare("SELECT * FROM modules WHERE id = :id");
	$stmt_modules->execute(array('id' => $mod_id));

	$m = $stmt_modules->fetch();

	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	$block_query = $db->query("SELECT * FROM block");

	$block_option = '';
	if($block_query->rowCount() > 0)
	{
		while($b = $block_query->fetch())
		{
			if ($b['block'] == $m['block']){$selected = 'selected';} else {$selected = '';}
			$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';
		}
	}
	// ======== / загрузка блоков вывода =======

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

	$size_sel[0] = $size_sel[1] = '';
	$size_sel[$m['p1']] = 'selected';

	if($m['p2'] < 150 || $m['p2'] > 600){$m['p2'] = 300;}
	if($m['p3'] < 0 || $m['p3'] > 800){$m['p3'] = 200;}

	$margin = explode(';',$m['p5']);
	if(count($margin) != 2 ){$margin[0] = 0;$margin[1] = 0;}

	$padding = explode(';',$m['p6']);
	if(count($padding) != 2 ){$padding[0] = 0;$padding[1] = 0;}

	if($m['p1'] == 1) // Фиксированная ширина
	{
		$w_c_out = 'width:'.($m['p3'] + 2*$margin[0]).'px;';
		$w_out = 'width:'.$m['p3'].'px;';
		//$h_out = 'height:'.($m['p2'] - 2*$padding).'px;';

		$class_out = '';
	}
	else // % Ширина
	{
		$w_c_out = '';
		$w_out = '';
		//$h_out = 'min-height:'.$m['p2'].'px;';
		$style_arr = explode(';', $m['p4']);
		if(count($style_arr) == 4)
		{
			$class_out = 'col_d_'.$style_arr[0].' col_n_'.$style_arr[1].' col_t_'.$style_arr[2].' col_p_'.$style_arr[3];
		}
		else
		{
			$class_out = 'col_d_100 col_n_100 col_t_100 col_p_100';
		}
	}

	$width_d_sel['16'] = $width_d_sel['20'] = $width_d_sel['25'] = $width_d_sel['33'] = $width_d_sel['50'] = $width_d_sel['66'] = $width_d_sel['75'] = $width_d_sel['83'] = $width_d_sel['100'] = '';
	$width_n_sel['16'] = $width_n_sel['20'] = $width_n_sel['25'] = $width_n_sel['33'] = $width_n_sel['50'] = $width_n_sel['66'] = $width_n_sel['75'] = $width_n_sel['83'] = $width_n_sel['100'] = '';
	$width_t_sel['16'] = $width_t_sel['20'] = $width_t_sel['25'] = $width_t_sel['33'] = $width_t_sel['50'] = $width_t_sel['66'] = $width_t_sel['75'] = $width_t_sel['83'] = $width_t_sel['100'] = '';
	$width_p_sel['16'] = $width_p_sel['20'] = $width_p_sel['25'] = $width_p_sel['33'] = $width_p_sel['50'] = $width_p_sel['66'] = $width_p_sel['75'] = $width_p_sel['83'] = $width_p_sel['100'] = '';

	$width_prc = explode(';',$m['p4']);

	if(count($width_prc) == 4)
	{
		$width_d_sel[$width_prc[0]] = 'selected';
		$width_n_sel[$width_prc[1]] = 'selected';
		$width_t_sel[$width_prc[2]] = 'selected';
		$width_p_sel[$width_prc[3]] = 'selected';
	}
	else
	{
		$width_d_sel[100] = 'selected';
		$width_n_sel[100] = 'selected';
		$width_t_sel[100] = 'selected';
		$width_p_sel[100] = 'selected';
	}

	$effect_out = '<option value="">Нет</option>';

	foreach ($effects as $key => $value)
	{
		if($m['p9'] == $key){$selected = 'selected';}
		else{$selected = '';}
		$effect_out .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
	}

	if($m['p7'] == 1 ){$bg_color_enable = 'checked';}else{$bg_color_enable = '';}

	// устанавливаем признак публикации заголовка модуля
	if ($m['titlepub'] == 1){$titlepub = "checked";} else{$titlepub = "";}

	// Автовыравнивание
	if ($m['p10'] == 1){$autoheight = "checked";} else{$autoheight = "";}


	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{
		echo '
		<div>
			<h1><img border="0" src="/modules/editor/admin/images/ico.png" style="float:left; margin-right:10px;" />'.LANG_M_EDITOR_MAIN_TITLE.'</h1>
			<form method="POST" action="/admin/modules/editor/'.$mod_id.'/update">

			<div id="mod_editor_accordion" class="seo_fon">
			<div class="accordion_head left_head">Основные настройки</div>
			<div class="accordion_body mod_editor_ab1">
				<div>&nbsp;</div>
				<table class="admin_table_2">
					<tr>
						<td width="200" height="25">'.LANG_M_EDITOR_VIEW_MODULE.'</td>
						<td>
							<select class="input" name="pub">
								<option value="1" '.$pub_1.'>'.LANG_M_EDITOR_VIEW_MODULE_ON.'</option>
								<option value="2" '.$pub_2.'>'.LANG_M_EDITOR_VIEW_MODULE_PC.'</option>
								<option value="3" '.$pub_3.'>'.LANG_M_EDITOR_VIEW_MODULE_PHONE.'</option>
								<option value="0" '.$pub_0.'>'.LANG_M_EDITOR_VIEW_MODULE_OFF.'</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>'.LANG_M_EDITOR_PUBLIC_TITLE.'</td>
						<td><input class="input" id="titlepub" name="titlepub" type="checkbox"  value="1" '.$titlepub.' ><label for="titlepub"></td>
					</tr>
					<tr>
						<td>'.LANG_M_EDITOR_POSITION.'</td>
						<td>
							<select class="input" size="1" name="block">
							'.$block_option.'
							</select>
							&nbsp;'.LANG_M_EDITOR_POSITION_DESCRIPTION.'
						</td>
					</tr>
					<tr>
						<td>'.LANG_M_EDITOR_ORDER.'</td>
						<td><input class="input" type="number" name="ordering" size="3" value="'.$m['ordering'].'" style="width:80px;"></td>
					</tr>
				</table>
			</div>

			<div class="accordion_head left_head">Размеры модуля</div>
			<div class="accordion_body mod_editor_ab2">
				<div>&nbsp;</div>
				<table class="admin_table_2">
					<tr>
						<td style="width:200px;">'.LANG_M_EDITOR_SIZE.'</td>
						<td>
							<div>
								<select class="input" name="size_type" id="size_type">
									<option value="0" '.$size_sel[0].'>'.LANG_M_EDITOR_SIZE_PRC.'</option>
									<option value="1" '.$size_sel[1].'>'.LANG_M_EDITOR_SIZE_FIX.'</option>
								</select>
							</div>
						</td>
					</tr>
					<tr id="width_prc">
						<td>'.LANG_M_EDITOR_WIDTH.'</td>
						<td>
							<span class="devices_type">'.LANG_M_EDITOR_COMP_D.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_EDITOR_COMP_D_T.'</em></span></span>
								<select id="width_d" class="input" name="width_d">
									<option value="16" '.$width_d_sel['16'].'>16%</option>
									<option value="20" '.$width_d_sel['20'].'>20%</option>
									<option value="25" '.$width_d_sel['25'].'>25%</option>
									<option value="33" '.$width_d_sel['33'].'>33%</option>
									<option value="50" '.$width_d_sel['50'].'>50%</option>
									<option value="66" '.$width_d_sel['66'].'>66%</option>
									<option value="75" '.$width_d_sel['75'].'>75%</option>
									<option value="83" '.$width_d_sel['83'].'>83%</option>
									<option value="100" '.$width_d_sel['100'].'>100%</option>
								</select>
							</span>
							<span class="devices_type">'.LANG_M_EDITOR_COMP_N.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_EDITOR_COMP_N_T.'</em></span></span>
							<select id="width_n" class="input" name="width_n">
								<option value="16" '.$width_n_sel['16'].'>16%</option>
								<option value="20" '.$width_n_sel['20'].'>20%</option>
								<option value="25" '.$width_n_sel['25'].'>25%</option>
								<option value="33" '.$width_n_sel['33'].'>33%</option>
								<option value="50" '.$width_n_sel['50'].'>50%</option>
								<option value="66" '.$width_n_sel['66'].'>66%</option>
								<option value="75" '.$width_n_sel['75'].'>75%</option>
								<option value="83" '.$width_n_sel['83'].'>83%</option>
								<option value="100" '.$width_n_sel['100'].'>100%</option>
							</select>
							</span>
							<span class="devices_type">'.LANG_M_EDITOR_COMP_T.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_EDITOR_COMP_T_T.'</em></span></span>
							<select id="width_t" class="input" name="width_t">
								<option value="16" '.$width_t_sel['16'].'>16%</option>
								<option value="20" '.$width_t_sel['20'].'>20%</option>
								<option value="25" '.$width_t_sel['25'].'>25%</option>
								<option value="33" '.$width_t_sel['33'].'>33%</option>
								<option value="50" '.$width_t_sel['50'].'>50%</option>
								<option value="66" '.$width_t_sel['66'].'>66%</option>
								<option value="75" '.$width_t_sel['75'].'>75%</option>
								<option value="83" '.$width_t_sel['83'].'>83%</option>
								<option value="100" '.$width_t_sel['100'].'>100%</option>
							</select>
							</span>
							<span class="devices_type">'.LANG_M_EDITOR_COMP_P.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_EDITOR_COMP_P_T.'</em></span></span>
							<select id="width_p" class="input" name="width_p">
								<option value="16" '.$width_p_sel['16'].'>16%</option>
								<option value="20" '.$width_p_sel['20'].'>20%</option>
								<option value="25" '.$width_p_sel['25'].'>25%</option>
								<option value="33" '.$width_p_sel['33'].'>33%</option>
								<option value="50" '.$width_p_sel['50'].'>50%</option>
								<option value="66" '.$width_p_sel['66'].'>66%</option>
								<option value="75" '.$width_p_sel['75'].'>75%</option>
								<option value="83" '.$width_p_sel['83'].'>83%</option>
								<option value="100" '.$width_p_sel['100'].'>100%</option>
							</select>
							</span>
						</td>
					</tr>
					<tr id="width_fix">
						<td>'.LANG_M_EDITOR_WIDTH.'</td>
						<td><input id="width_f" class="input" type="number" min="0" max="600" name="width" size="3" value="'.$m['p3'].'" style="width:80px;"> px</td>
					</tr>
					<tr>
						<td>'.LANG_M_EDITOR_AUTOHEIGHT.' <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_EDITOR_AUTOHEIGHT_T.'</em></span></span></td>
						<td><input class="input" id="autoheight" name="autoheight" type="checkbox" value="1" '.$autoheight.' ><label for="autoheight"></label></td>
					</tr>
					<tr>
						<td>'.LANG_M_EDITOR_MARGIN.'</td>
						<td><input id="margin_w" class="input" type="number" min="0" max="100" name="margin_w" size="3" value="'.$margin[0].'" style="width:80px;"> / <input id="margin_h" class="input" type="number" min="0" max="100" name="margin_h" size="3" value="'.$margin[1].'" style="width:80px;"> px</td>
					</tr>
					<tr>
						<td>'.LANG_M_EDITOR_PADDING.'</td>
						<td><input id="padding_w" class="input" type="number" min="0" max="100" name="padding_w" size="3" value="'.$padding[0].'" style="width:80px;"> / <input id="padding_h" class="input" type="number" min="0" max="100" name="padding_h" size="3" value="'.$padding[1].'" style="width:80px;"> px</td>
					</tr>
				</table>
			</div>

			<div class="accordion_head left_head">'.LANG_M_EDITOR_EFFECTS.'</div>
			<div class="accordion_body mod_editor_ab3">
				<div>&nbsp;</div>
				<table class="admin_table_2">
					<tr>
						<td style="width:200px;">'.LANG_M_EDITOR_EFFECTS_APPEARANCE.'</td>
						<td>
							<div>
								<select class="input" name="effect">
								'.$effect_out.'
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td>Цвет фона вкл/выкл.</td>
						<td>
							<div class="bg_color_enable_container"><input class="input" id="bg_color_enable" name="bg_color_enable" type="checkbox"  value="1" '.$bg_color_enable.' ><label for="bg_color_enable"></div>
							<input id="bg_color" type="color" name="bg_color" value="'.$m['p8'].'" autocomplete="on">
						</td>
					</tr>
				</table>
			</div>
		</div>
		';

			echo '
			<div>&nbsp;</div>
			<table class="admin_table_2">
				<tr>
					<td style="width:200px;">'.LANG_M_EDITOR_TITLE.'</td>
					<td><input class="input" type="text" name="title" size="50" value="'.$m['title'].'"></td>
				</tr>
			</table>
			<table id="mod_editor_ec" class="table_main">
				<tr>
					<td colspan="2">&nbsp;&nbsp;'.LANG_M_EDITOR_EDITABLE_AREA_1.'</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="editor_container" class="'.$class_out.'" style="'.$w_out.'">
							<textarea id="editor1" name="content" style="margin:'.$margin[1].'px '.$margin[0].'px;">'.$m['content'].'</textarea>
						</div>
					</td>
				</tr>
			</table>

			<div style="margin-top:40px;">
				<input type="hidden" name="id" value="1" '.$m['id'].' >
				<input id="button_save" class="greenbutton" type="submit" value="'.LANG_M_EDITOR_SAVE.'" name="bt_save">&nbsp;<input id="button_apply" class="bluebutton" type="submit" value="'.LANG_M_EDITOR_ACCEPT.'" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_EDITOR_CANCEL.'" name="bt_none">
			</div>

			<script type="text/javascript">
				e_editor = CKEDITOR.replace( \'editor1\',
					{
						height: \'400px\',
						filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\'
					});

				DAN.accordion("accordion_head", "accordion_body");
			</script>
			</form>

		</div>
		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">'.LANG_M_EDITOR_MODULE_OFF.'</div>';
	}
} // конец функции


?>
