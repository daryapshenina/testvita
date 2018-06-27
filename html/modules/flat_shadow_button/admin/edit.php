<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/flat_shadow_button/frontend/style.css');
$head->addFile('/modules/flat_shadow_button/admin/style.css');
$head->addFile('/modules/flat_shadow_button/admin/tmp.js');

function a_com()
{
	global $root, $db, $d, $domain;
	
	$padding = 10; // padding плашки

	$id = $d[3];

	$stmt_mod = $db->prepare("SELECT * FROM modules WHERE module = 'flat_shadow_button' AND id = :id LIMIT 1");
	$stmt_mod->execute(array('id' => $id));	
	
	$m = $stmt_mod->fetch();

	// ======= Блоки вывода ========
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
	// ======== / Блоки вывода =======


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
	if($m['p3'] < 150 || $m['p3'] > 800){$m['p3'] = 200;}

	$margin = explode(';',$m['p5']);
	
	if($m['p1'] == 1) // Фиксированная ширина
	{
		$w_c_out = 'width:'.($m['p3'] + 2*$margin[0]).'px;';
		$w_out = 'width:'.($m['p3'] - 2*$padding).'px;';
		$h_out = 'height:'.($m['p2'] - 2*$padding).'px;';
	
		$class_out = '';
	}
	else // % Ширина
	{
		$w_c_out = '';
		$w_out = '';
		$h_out = '';	
		$style_arr = explode(';', $m['p4']);
		$class_out = 'col_d_'.$style_arr[0].' col_n_'.$style_arr[1].' col_t_'.$style_arr[2].' col_p_'.$style_arr[3];
	}

	$width_d_sel['16'] = $width_d_sel['20'] = $width_d_sel['25'] = $width_d_sel['33'] = $width_d_sel['50'] = $width_d_sel['66'] = $width_d_sel['75'] = $width_d_sel['83'] = $width_d_sel['100'] = '';
	$width_n_sel['16'] = $width_n_sel['20'] = $width_n_sel['25'] = $width_n_sel['33'] = $width_n_sel['50'] = $width_n_sel['66'] = $width_n_sel['75'] = $width_n_sel['83'] = $width_n_sel['100'] = '';
	$width_t_sel['16'] = $width_t_sel['20'] = $width_t_sel['25'] = $width_t_sel['33'] = $width_t_sel['50'] = $width_t_sel['66'] = $width_t_sel['75'] = $width_t_sel['83'] = $width_t_sel['100'] = '';
	$width_p_sel['16'] = $width_p_sel['20'] = $width_p_sel['25'] = $width_p_sel['33'] = $width_p_sel['50'] = $width_p_sel['66'] = $width_p_sel['75'] = $width_p_sel['83'] = $width_p_sel['100'] = '';

	$width_prc = explode(';',$m['p4']);
	
	$width_d_sel[$width_prc[0]] = 'selected';
	$width_n_sel[$width_prc[1]] = 'selected';
	$width_t_sel[$width_prc[2]] = 'selected';
	$width_p_sel[$width_prc[3]] = 'selected';
	
	$button_sel[0] = $button_sel[1] = '';
	$button_sel[$m['p6']] = 'selected';
	
	if($m['p6'] == 0){$button_bg = 'background:'.$m['p8'].';';}
	else{$button_bg = 'background:none; border:3px solid #ffffff;';}
	
	// RGB - подложка с прозрачностью
	$hex = $m['p9'];
	list($r, $g, $b) = sscanf($hex, "#%2x%2x%2x");	
	$sub_bg = 'background:rgba('.$r.', '.$g.', '.$b.', '.$m['p10'].');';

	if ($m['enabled'] == "1")
	{
		echo '
		<div class=""container">
			<h1><img border="0" src="/modules/flat_shadow_button/admin/images/ico.png" style="float:left; margin-right:10px;" />'.LANG_M_FLAT_SHADOW_BUTTON_MAIN_TITLE.'</h1>

			<form method="POST" action="/admin/modules/flat_shadow_button/'.$m['id'].'/update">
			<table class="admin_table_2">
				<tr>
					<td style="width:200px;">'.LANG_M_FLAT_SHADOW_BUTTON_TITLE.'</td>
					<td><input class="input" type="text" name="title" size="50" value="'.$m['title'].'"></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_DESCRIPTION.'</td>
					<td>'.$m['description'].'</td>
				</tr>
				<tr>
					<td width="200" height="25">'.LANG_M_FLAT_SHADOW_BUTTON_VIEW_MODULE.'</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub_1.'>'.LANG_M_FLAT_SHADOW_BUTTON_VIEW_MODULE_ON.'</option>
							<option value="2" '.$pub_2.'>'.LANG_M_FLAT_SHADOW_BUTTON_VIEW_MODULE_PC.'</option>
							<option value="3" '.$pub_3.'>'.LANG_M_FLAT_SHADOW_BUTTON_VIEW_MODULE_PHONE.'</option>
							<option value="0" '.$pub_0.'>'.LANG_M_FLAT_SHADOW_BUTTON_VIEW_MODULE_OFF.'</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_POSITION.'</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						&nbsp;'.LANG_M_FLAT_SHADOW_BUTTON_POSITION_DESCRIPTION.'
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_ORDER.'</td>
					<td><input class="input" type="number" name="ordering" size="3" value="'.$m['ordering'].'" style="width:80px;"></td>
				</tr>
				<tr>
					<td colspan="2"><hr class="hr_admin"></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_SIZE.'</td>
					<td>
						<div>
							<select class="input" name="size" id="size">
								<option value="0" '.$size_sel[0].'>'.LANG_M_FLAT_SHADOW_BUTTON_SIZE_PRC.'</option>						
								<option value="1" '.$size_sel[1].'>'.LANG_M_FLAT_SHADOW_BUTTON_SIZE_FIX.'</option>
							</select>
						</div>
					</td>
				</tr>			
				<tr id="size_prc">
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_WIDTH.'</td>
					<td>
						<span class="devices_type">'.LANG_M_FLAT_SHADOW_BUTTON_COMP_D.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_FLAT_SHADOW_BUTTON_COMP_D_T.'</em></span></span>
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
						<span class="devices_type">'.LANG_M_FLAT_SHADOW_BUTTON_COMP_N.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_FLAT_SHADOW_BUTTON_COMP_N_T.'</em></span></span>
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
						<span class="devices_type">'.LANG_M_FLAT_SHADOW_BUTTON_COMP_T.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_FLAT_SHADOW_BUTTON_COMP_T_T.'</em></span></span>
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
						<span class="devices_type">'.LANG_M_FLAT_SHADOW_BUTTON_COMP_P.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_FLAT_SHADOW_BUTTON_COMP_P_T.'</em></span></span>
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
				<tr id="size_fix">
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_WIDTH.' / '.LANG_M_FLAT_SHADOW_BUTTON_HEIGHT.'</td>
					<td><input id="flat_width" class="input" type="number" min="150" max="600" name="flat_width" size="3" value="'.$m['p3'].'" style="width:80px;"> / <input id="flat_height" class="input" type="number" min="150" max="600" name="height" size="3" value="'.$m['p2'].'" style="width:80px;"> px</td>
				</tr>			
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_MARGIN.'</td>
					<td><input id="margin_w" class="input" type="number" min="0" max="100" name="margin_w" size="3" value="'.$margin[0].'" style="width:80px;"> / <input id="margin_h" class="input" type="number" min="0" max="100" name="margin_h" size="3" value="'.$margin[1].'" style="width:80px;"> px</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_BUTTON_TYPE.'</td>
					<td>
						<select id="button_type" class="input" name="button_type">
							<option value="0" '.$button_sel[0].'>'.LANG_M_FLAT_SHADOW_BUTTON_BUTTON_COLOR.'</option>						
							<option value="1" '.$button_sel[1].'>'.LANG_M_FLAT_SHADOW_BUTTON_BUTTON_TRANSPARENT.'</option>							
						</select>
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_TEXT.'</td>
					<td><input class="input" type="text" name="button_text" value="'.$m['p7'].'" size="20"></td>
				</tr>				
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_BUTTON_COLOR_2.'</td>
					<td><input id="color" type="color" name="button_color" value="'.$m['p8'].'" autocomplete="on" onchange="color_value();"></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_SUB_COLOR.'</td>
					<td><input id="color" type="color" name="sub_color" value="'.$m['p9'].'" autocomplete="on" onchange="color_value();"></td>
				</tr>				
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_TRANSPARENT.'</td>
					<td><input id="transparent" type="range" name="transparent" min="0" step="0.1" max="1"  value="'.$m['p10'].'"><span id="transparent_out">'.$m['p10'].'</span></td>
				</tr>
				<tr>
					<td>'.LANG_M_FLAT_SHADOW_BUTTON_LINK.'</td>
					<td><input class="input" type="text" name="link" size="50" value="'.$m['content_2'].'"></td>
				</tr>
				<tr>
					<td>
						<div><b>'.LANG_M_FLAT_SHADOW_BUTTON_EDIT.' / '.LANG_M_FLAT_SHADOW_BUTTON_RESULT.'</b></div>
					</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="flat_container" class="mod_flat_shadow_button_container '.$class_out.'" style="'.$w_c_out.'">
							<div id="flat_white" class="mod_flat_shadow_button_white" contenteditable="true" style="'.$w_out.' '.$h_out.' margin:'.$margin[1].'px '.$margin[0].'px;">
								'.$m['content'].'
							</div>
						</div>
						<div class="mod_flat_shadow_button_container '.$class_out.'" style="'.$w_c_out.'">
							<a href="'.$m['content_2'].'" class="mod_flat_shadow_button_white" style="'.$w_out.' '.$h_out.' margin:'.$margin[1].'px '.$margin[0].'px;">
								<div style="'.$sub_bg.'"><span style="'.$button_bg.'">'.$m['p7'].'</span></div>
								'.$m['content'].'
							</a>
						</div>
					</td>
				</tr>				
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>				
				<tr>
					<td colspan="2">
						<input type="hidden" name="id" value="'.$m['id'].'">
						<input id="button_save" class="greenbutton" type="submit" value="'.LANG_M_FLAT_SHADOW_BUTTON_SAVE.'" name="bt_save">&nbsp;<input id="button_apply" class="bluebutton" type="submit" value="'.LANG_M_FLAT_SHADOW_BUTTON_ACCEPT.'" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_FLAT_SHADOW_BUTTON_CANCEL.'" name="bt_none">
						<input id="content" name="content" type="hidden">				
					</td>
				</tr>					
			</table>
			</form>
		</div>

		<script type="text/javascript">

			CKEDITOR.disableAutoInline = true;
			CKEDITOR.inline("flat_white",
				{
					filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
				});		

			var button_save = document.getElementById("button_save");
			var button_apply = document.getElementById("button_apply");
			button_save.onclick = hidden;
			button_apply.onclick = hidden;				
			function hidden()
			{
				var data = CKEDITOR.instances.flat_white.getData();
				document.getElementById("content").value = data;
			}

		</script>
		';

	} // 'enabled'
	else
	{
		echo '<div id="main-top">'.LANG_M_FLAT_SHADOW_BUTTON_MODULE_OFF.'</div>';
	}
}



?>