<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/administrator/modules/classes/lang/'.LANG.'.php');
$head->addCode('<style type="text/css">.mod_title_input{opacity:0.2;transition:0.3s;}.mod_title_label{float:left;margin:4px 10px 0px 0px;}.mod_title_input_checkbox:checked ~input{opacity:1;transition:0.3s;}</style>');

class ModulesAdmin
{
	public function __construct($_id)
	{
		global $db;

		$stmt = $db->prepare("SELECT * FROM modules WHERE id = :id");
		$stmt->execute(array('id' => $_id));

		$this->m = $stmt->fetch();
	}


	public function get_array()
	{
		return $this->m;
	}
	
	
	public function color()
	{
		return'
		<tr>
			<td>Цвет</td>
			<td><div><input id="color" type="color" name="color" value="'.$this->m['color'].'" autocomplete="on"></div></td>
		</tr>';
	}	
	

	public function bg_color()
	{
		return'
		<tr>
			<td>Цвет фона</td>
			<td><div><input id="bg_color" type="color" name="bg_color" value="'.$this->m['bg_color'].'" autocomplete="on"></div></td>
		</tr>';
	}


	public function block()
	{
		global $db;

		$block_query = $db->query("SELECT * FROM block");

		$block_option = '';
		if($block_query->rowCount() > 0)
		{
			while($b = $block_query->fetch())
			{
				if ($b['block'] == $this->m['block']){$selected = 'selected';} else {$selected = '';}
				$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';
			}
		}

		return
		'<tr>
			<td>'.LANG_M_POSITION.'</td>
			<td>
				<select class="input" size="1" name="block">
				'.$block_option.'
				</select>
				<span class="block_text">&nbsp;'.LANG_M_POSITION_DESCRIPTION.'</div>
			</td>
		</tr>';
	}


	public function button()
	{
		return
		'<div style="margin-top:40px;">
			<input type="hidden" name="id" value="1" '.$this->m['id'].' >
			<input id="button_save" class="greenbutton" type="submit" value="'.LANG_M_SAVE.'" name="bt_save">&nbsp;<input id="button_apply" class="bluebutton" type="submit" value="'.LANG_M_ACCEPT.'" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_CANCEL.'" name="bt_none">
		</div>
		';
	}

	public function effect_a($_value = '')
	{
		if($_value == '') $_value = $this->m['effect_a'];
		
		$effect_out = '<option value="">Нет</option>';

		foreach ($this->effects as $key => $value)
		{
			if($_value == $key){$selected = 'selected';}
			else{$selected = '';}
			$effect_out .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		}

		return
		'<tr>
			<td>'.LANG_M_EFFECTS_APPEARANCE.'</td>
			<td>
				<div>
					<select class="input" name="effect_a">
					'.$effect_out.'
					</select>
				</div>
			</td>
		</tr>
		';
	}


	public function enabled()
	{
		return $this->m['enabled'];
	}


	public function height_auto($_value)
	{
		// Автовыравнивание
		if ($_value == 1){$autoheight = "checked";} else{$autoheight = "";}

		return
		'<tr>
			<td>'.LANG_M_AUTOHEIGHT.' <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_AUTOHEIGHT_T.'</em></span></span></td>
			<td><input class="input" id="autoheight" name="autoheight" type="checkbox" value="1" '.$autoheight.' ><label for="autoheight"></label></td>
		</tr>
		';
	}


	public function height_fix($_value)
	{
		return
		'<tr id="height_fix_tr">
			<td>'.LANG_M_HEIGHT.'</td>
			<td><input id="height_fix" class="input" type="number" min="0" max="600" name="height_fix" size="3" value="'.$_value.'" style="width:80px;"> px</td>
		</tr>
		';
	}


	public function margin($_value = false)
	{
		if($_value)
		{
			$margin = explode(';',$_value);

			if(count($margin) != 2 )
			{
				$margin_w = 0;
				$margin_h = 0;
			}
			else
			{
				$margin_w = $margin[0];
				$margin_h = $margin[1];
			}
		}
		else
		{
			$margin_w = $this->m['margin_w'];
			$margin_h = $this->m['margin_h'];
		}

		return
		'<tr>
			<td>'.LANG_M_MARGIN.'</td>
			<td><input id="margin_h" class="input" type="number" min="0" max="100" name="margin_h" size="3" value="'.$margin_h.'" style="width:80px;"> / <input id="margin_w" class="input" type="number" min="0" max="100" name="margin_w" size="3" value="'.$margin_w.'" style="width:80px;"> px</td>
		</tr>
		';
	}


	public function order()
	{
		return
		'<tr>
			<td>'.LANG_M_ORDER.'</td>
			<td><input class="input" type="number" name="ordering" size="3" value="'.$this->m['ordering'].'" style="width:80px;"></td>
		</tr>
		';
	}


	public function padding($_value = false)
	{
		if($_value)
		{
			$padding = explode(';',$_value);
			if(count($padding) != 2 )
			{
				$padding_w = 0;
				$padding_h = 0;
			}
			else
			{
				$padding_w = $padding[0];
				$padding_h = $padding[1];
			}
		}
		else
		{
			$padding_w = $this->m['padding_w'];
			$padding_h = $this->m['padding_h'];
		}

		return
		'<tr>
			<td>'.LANG_M_PADDING.'</td>
			<td><input id="padding_w" class="input" type="number" min="0" max="100" name="padding_w" size="3" value="'.$padding_w.'" style="width:80px;"> / <input id="padding_h" class="input" type="number" min="0" max="100" name="padding_h" size="3" value="'.$padding_h.'" style="width:80px;"> px</td>
		</tr>
		';
	}


	public function pub()
	{
		// устанавливаем признак публикации
		$pub_0 = $pub_1 = $pub_2 = $pub_3 = '';

		switch($this->m['pub'])
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

		return
		'<tr>
			<td>'.LANG_M_VIEW_MODULE.'</td>
			<td>
				<select class="input" name="pub">
					<option value="1" '.$pub_1.'>'.LANG_M_VIEW_MODULE_ON.'</option>
					<option value="2" '.$pub_2.'>'.LANG_M_VIEW_MODULE_PC.'</option>
					<option value="3" '.$pub_3.'>'.LANG_M_VIEW_MODULE_PHONE.'</option>
					<option value="0" '.$pub_0.'>'.LANG_M_VIEW_MODULE_OFF.'</option>
				</select>
			</td>
		</tr>
		';
	}


	public function size_type($_type)
	{
		$size_type_sel[0] = $size_type_sel[1] = '';
		$size_type_sel[$_type] = 'selected';

		return
		'
		<tr>
			<td>'.LANG_M_SIZE.'</td>
			<td>
				<div>
					<select class="input" name="size_type" id="size_type">
						<option value="0" '.$size_type_sel[0].'>'.LANG_M_SIZE_PRC.'</option>
						<option value="1" '.$size_type_sel[1].'>'.LANG_M_SIZE_FIX.'</option>
					</select>
				</div>
			</td>
		</tr>
		';
	}


	public function title()
	{
		if ($this->m['titlepub'] == 1){$titlepub = "checked";} else{$titlepub = "";}

		return
		'<tr>
			<td>'.LANG_M_TITLE.'</td>
			<td><input class="input mod_title_input_checkbox" id="titlepub" name="titlepub" type="checkbox"  value="1" '.$titlepub.' ><label for="titlepub" class="mod_title_label"></label><input class="input mod_title_input" type="text" name="title" size="50" value="'.$this->m['title'].'"></td>
		</tr>';
	}


	public function description()
	{
		return
		'<tr>
			<td>'.LANG_M_DESCRIPTION.'</td>
			<td>'.$this->m['description'].'</td>
		</tr>';
	}


	public function width_fix($_value)
	{
		return
		'<tr id="width_fix_tr">
			<td>'.LANG_M_WIDTH.'</td>
			<td><input id="width_fix" class="input" type="number" min="100" max="600" name="width_fix" size="3" value="'.$_value.'" style="width:80px;"> px</td>
		</tr>
		';
	}


	public function width_prc($_prc = '')
	{
		if($_prc == '') $_prc = $this->m['width_p'];
		$w_c_out = '';
		$w_out = '';
		$width_arr = explode(';', $_prc);

		$width_d_sel['16'] = $width_d_sel['20'] = $width_d_sel['25'] = $width_d_sel['33'] = $width_d_sel['50'] = $width_d_sel['66'] = $width_d_sel['75'] = $width_d_sel['83'] = $width_d_sel['100'] = '';
		$width_n_sel['16'] = $width_n_sel['20'] = $width_n_sel['25'] = $width_n_sel['33'] = $width_n_sel['50'] = $width_n_sel['66'] = $width_n_sel['75'] = $width_n_sel['83'] = $width_n_sel['100'] = '';
		$width_t_sel['16'] = $width_t_sel['20'] = $width_t_sel['25'] = $width_t_sel['33'] = $width_t_sel['50'] = $width_t_sel['66'] = $width_t_sel['75'] = $width_t_sel['83'] = $width_t_sel['100'] = '';
		$width_p_sel['16'] = $width_p_sel['20'] = $width_p_sel['25'] = $width_p_sel['33'] = $width_p_sel['50'] = $width_p_sel['66'] = $width_p_sel['75'] = $width_p_sel['83'] = $width_p_sel['100'] = '';

		if(count($width_arr) == 4)
		{
			$class_out = 'col_d_'.$width_arr[0].' col_n_'.$width_arr[1].' col_t_'.$width_arr[2].' col_p_'.$width_arr[3];

			$width_d_sel[$width_arr[0]] = 'selected';
			$width_n_sel[$width_arr[1]] = 'selected';
			$width_t_sel[$width_arr[2]] = 'selected';
			$width_p_sel[$width_arr[3]] = 'selected';
		}
		else
		{
			$class_out = 'col_d_100 col_n_100 col_t_100 col_p_100';

			$width_d_sel[100] = 'selected';
			$width_n_sel[100] = 'selected';
			$width_t_sel[100] = 'selected';
			$width_p_sel[100] = 'selected';
		}

		return '
		<tr id="width_prc_tr">
			<td>'.LANG_M_WIDTH.'</td>
			<td>
				<span class="devices_type">'.LANG_M_COMP_D.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_COMP_D_T.'</em></span></span>
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
				<span class="devices_type">'.LANG_M_COMP_N.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_COMP_N_T.'</em></span></span>
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
				<span class="devices_type">'.LANG_M_COMP_T.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_COMP_T_T.'</em></span></span>
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
				<span class="devices_type">'.LANG_M_COMP_P.': <span class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь"><em>'.LANG_M_COMP_P_T.'</em></span></span>
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
		</tr>';
	}


	private $m = array();
	private $effects = array(
		'animation_top_1' => 'Появление сверху',
		'animation_top_2' => 'Появление сверху через 0.25 секунд',
		'animation_top_3' => 'Появление сверху через 0.5 секунд',
		'animation_top_4' => 'Появление сверху через 0.75 секунд',
		'animation_right' => 'Появление справа',
		'animation_bottom_1' => 'Появление снизу',
		'animation_bottom_2' => 'Появление снизу через 0.25 секунд',
		'animation_bottom_3' => 'Появление снизу через 0.5 секунд',
		'animation_bottom_4' => 'Появление снизу через 0.75 секунд',
		'animation_left' => 'Появление слева',
		'animation_transparency_1' => 'Появление',
		'animation_transparency_2' => 'Появление снизу 0.25 секунд',
		'animation_transparency_3' => 'Появление снизу 0.5 секунд',
		'animation_transparency_4' => 'Появление снизу 0.75 секунд',
	);
}

?>