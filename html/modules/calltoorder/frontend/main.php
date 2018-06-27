<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

if ($m['pub'] == "1")
{
	if(Settings::instance()->getValue('personal_information') == 1) $pi = 'true';
	else $pi = 'false';

	if ($m['p1'] == 0)
	{
		$out = '
			<div style="text-align:center;">
				<div id="calltoorder" onclick="f_calltoorder('.$pi.')">'.LANG_M_CALLTOORDER_REQUEST_CALL.'</div>
			</div>
		';
	}
	else
	{
		$out = '
			<div id="mod_calltoorder_circle" onclick="f_calltoorder('.$pi.')" style="width:'.($m['p2'] * 2).'px; height:'.($m['p2'] * 2).'px; right:'.$m['p3'].'px; bottom:'.$m['p4'].'px;">
				<div class="mod_calltoorder_circle" style="background-color:'.$m['p5'].'; width:'.$m['p2'].'px; height:'.$m['p2'].'px;"></div>
				<div class="mod_calltoorder_circle_wave_out" style="background-color:'.$m['p5'].'; width:'.$m['p2'].'px; height:'.$m['p2'].'px;"></div>
				<div class="mod_calltoorder_circle_wave_in" style="border-color:'.$m['p5'].'; width:'.($m['p2'] - 2).'px; height:'.($m['p2'] - 2).'px;"></div>
			</div>
		';
	}

	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_calltoorder" data-id="'.$m['id'].'">'.$out.'</div>';}
	else {echo $out;}
}

?>
