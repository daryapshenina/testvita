<?php
namespace Modules\Calltoorder;
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

$title = $modules_title;
$titlepub = $modules_titlepub;
$suf_editor = $modules_module_csssuf;

if ($modules_pub == "1")
{
	if ($modules_p1 == 0)
	{
		// Заголовок модуля
		if ($titlepub == "1"){$title_out = '<div class="mod-title">'.$title.'</div>';}
		else {$title_out = '';}


		$out = '<div class="mod-main'.$suf_editor.'">
		<div class="mod-top">'.$title_out.'</div>
			<div class="mod-mid">
				<div class="mod-padding">
					<div style="text-align:center;">
						<div id="calltoorder">'.LANG_M_CALLTOORDER_REQUEST_CALL.'</div>
					</div>
				</div>
			</div>
			<div class="mod-bot"></div>
		</div>';
	}
	else
	{
		$out = '
			<div id="mod_calltoorder_circle" style="width:'.($modules_p2 * 2).'px; height:'.($modules_p2 * 2).'px; right:'.$modules_p3.'px; bottom:'.$modules_p4.'px;">
				<div class="mod_calltoorder_circle" style="background-color:'.$modules_p5.'; width:'.$modules_p2.'px; height:'.$modules_p2.'px;"></div>
				<div class="mod_calltoorder_circle_wave_out" style="background-color:'.$modules_p5.'; width:'.$modules_p2.'px; height:'.$modules_p2.'px;"></div>
				<div class="mod_calltoorder_circle_wave_in" style="border-color:'.$modules_p5.'; width:'.($modules_p2 - 2).'px; height:'.($modules_p2 - 2).'px;"></div>
			</div>
		';
	}

	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_calltoorder" data-id="">'.$out.'</div>';}
	else {echo $out;}
}

?>
