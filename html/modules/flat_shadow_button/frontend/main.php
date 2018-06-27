<?php
namespace Modules\Flat_shadow_button;
defined('AUTH') or die('Restricted access');

$padding = 10; // padding плашки

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

if($m['p6'] == 0){$button_bg = 'background:'.$m['p8'].';';}
else{$button_bg = 'background:none; border:3px solid #ffffff;';}

// RGB - подложка с прозрачностью
$hex = $m['p9'];
list($r, $g, $b) = sscanf($hex, "#%2x%2x%2x");	
$sub_bg = 'background:rgba('.$r.', '.$g.', '.$b.', '.$m['p10'].');';


if($frontend_edit == 1)
{
	$edit_class = 'edit_mode';	
	$edit_data = 'data-type="mod_flat_shadow_button" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';	
	$edit_data = '';
}

echo
'<div '.$edit_data.' class="'.$edit_class.' mod_flat_shadow_button_container '.$class_out.'" style="'.$w_c_out.'">
	<a href="'.$m['content_2'].'" class="mod_flat_shadow_button_white" style="'.$w_out.' '.$h_out.' margin:'.$margin[1].'px '.$margin[0].'px;">
		<div style="'.$sub_bg.'"><span style="'.$button_bg.'">'.$m['p7'].'</span></div>
		'.$m['content'].'
	</a>
</div>';

?>