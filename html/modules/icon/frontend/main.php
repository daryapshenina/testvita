<?php
defined('AUTH') or die('Restricted access');

//include_once __DIR__.'/lang/'.LANG.'.php';

if ($m['pub'] == 1)
{
	$style_main = '';
	$style_content = '';
	$border_radius = '';
	$title_out = '';

	if($m['p1'] == 1) $border_radius = 'border-radius:50%;';

	$class_out = $m['effect_a']; // эффект
	$w_arr = explode(';', $m['p3']); // размер в %
	$margin = explode(';',$m['p5']);
	$padding = explode(';',$m['p6']);

	$font_size = intval($m['p7']/2.5);

	if($m['p2'] == 0) // Размер в %
	{
		if($m['p10'])$class_out .= ' col_auto_height ';
		else $class_out .= ' col_auto_row ';

		if(count($w_arr) == 4)
		{
			if(!($w_arr[0] == 100 && $w_arr[1] == 100 && $w_arr[2] == 100 && $w_arr[3] == 100))
			{
				$class_out .= 'col_d_'.$w_arr[0].' col_n_'.$w_arr[1].' col_t_'.$w_arr[2].' col_p_'.$w_arr[3];
			}
		}

		$style_content = 'style="height:calc(100% - '.($margin[1]*2).'px);margin:'.$margin[1].'px '.$margin[0].'px; padding:'.$padding[1].'px '.$padding[0].'px;"';
	}
	else // Фиксированный
	{
		$size_fix_arr = explode(';',$m['p4']);

		if(count($size_fix_arr) != 2){$size_fix_arr[0] = 0; $size_fix_arr[1] = 0;}

		if($size_fix_arr[0] < 100 || $size_fix_arr[0] > 600){$size_fix_arr[0] = 250;} // Ширина фиксированная
		if($size_fix_arr[1] < 100 || $size_fix_arr[1] > 800){$size_fix_arr[1] = 250;} // Высота фиксированная

		$style_main = 'style="display:inline-block; float:left; width:'.($size_fix_arr[0] + 2*$margin[0]).'px;"';
		$style_content = 'style="margin:'.$margin[1].'px '.$margin[0].'px; padding:'.$padding[1].'px '.$padding[0].'px; height:'.($size_fix_arr[1] - 2*$padding[1]).'px;"';
	}

	if($m['titlepub'] == 1)$title_out = '<div class="mod_title">'.$m['title'].'</div>';

	// frontend редактирование
	if($frontend_edit == 1)
	{
		$edit_class = 'edit_mode ';
		$edit_data = 'data-type="mod_icon" data-id="'.$m['id'].'"';
	}
	else
	{
		$edit_class = '';
		$edit_data = '';
	}


	echo
	'<style type="text/css">#mod_'.$m['id'].' .mod_icon_content:hover .mod_icon_text{color:'.$m['p8'].'}</style>
	<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'mod_icon_main'.$m['module_csssuf'].' '.$class_out.'" '.$style_main.'>
		<div class="mod_icon_content" '.$style_content.'>
			'.$title_out.'
			<div class="mod_icon_frame" style="width:'.$m['p7'].'px; height:'.$m['p7'].'px; box-shadow: 0 0 0 3px '.$m['p8'].' inset; '.$border_radius.'">
				<b style="background-color:'.$m['p8'].';"></b>
				<i class="fa fa-'.$m['content'].'" style="line-height:'.$m['p7'].'px; font-size:'.$font_size.'px; color:'.$m['p8'].';"></i>
			</div>
			<div class="mod_icon_text">'.$m['content_2'].'</div>
		</div>
	</div>
	';

}

?>