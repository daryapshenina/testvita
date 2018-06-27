<?php
namespace Modules\Editor;
defined('AUTH') or die('Restricted access');

$margin = explode(';',$m['p5']);
if(empty($margin[0])){$margin[0] = 0;}
if(empty($margin[1])){$margin[1] = 0;}

$padding = explode(';',$m['p6']);
$class_out = '';

$margin_out = '';
$margin_c_out = '';

if($m['p1'] == 1) // Фиксированная ширина
{
	if(count($margin) == 2 && !($margin[0] == 0 && $margin[1] == 0))
	{
		$margin_c_out = 'style="display:inline-block;';

		if($m['p3'] > 0)
			$margin_c_out .= 'width:'.($m['p3'] + 2*$margin[0]).'px;';

		$margin_c_out .= '"';
		$margin_out = 'margin:'.$margin[1].'px '.$margin[0].'px;';
	}
	else
	{
		$margin_c_out = 'style="display:inline-block;';

		if($m['p3'] > 0)
			$margin_c_out .= 'width:'.$m['p3'].'px;';

		$margin_c_out .= '"';
	}
}
else // % Ширина
{
	if((count($margin) == 2) && !($margin[0] == 0 && $margin[1] == 0)){$margin_out = 'margin:'.$margin[1].'px '.$margin[0].'px;';}

	$w_arr = explode(';', $m['p4']);
	if(count($w_arr) == 4)
	{

		if($w_arr[0] == 100 && $w_arr[1] == 100 && $w_arr[2] == 100 && $w_arr[3] == 100)
		{
			$class_out .= ' w_100';
		}
		else
		{
			$class_out .= ' col_d_'.$w_arr[0].' col_n_'.$w_arr[1].' col_t_'.$w_arr[2].' col_p_'.$w_arr[3];			
		}
	}
}

if(count($padding) == 2){$padding_out = 'padding:'.$padding[1].'px '.$padding[0].'px;';}else{$padding_out = '';}

// Заголовок модуля
if ($m['titlepub'] == "1"){$title_out = '<div class="mod-title'.$m['module_csssuf'].'">'.$m['title'].'</div>';}
else {$title_out = '';}

// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = 'edit_mode ';
	$edit_data = 'data-type="mod_editor" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}

if($m['p7'] == 1 && $m['p8'] != ''){$bg_color = 'background-color:'.$m['p8'].';';} else{$bg_color = '';}

echo
'<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'mod-main'.$m['module_csssuf'].' '.$class_out.'" '.$margin_c_out.'>
	<div class="mod-content" style="height:calc(100% - '.($margin[1]*2).'px);'.$margin_out.$padding_out.$bg_color.'">
		'.$title_out.$m['content'].'
	</div>
</div>
';


if($m['p9'] != '')
{
	echo '<script type="text/javascript">DAN.appearance("mod_'.$m['id'].'", "'.$m['p9'].'");</script>	';
}


?>