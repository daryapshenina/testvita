<?php
namespace Modules\Route;
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

$margin = explode(';',$m['p5']);
$padding = explode(';',$m['p6']);

$margin_out = '';
$padding_out = '';

if(count($margin) == 2 && !($margin[0] == 0 && $margin[1] == 0))
	$margin_out = 'margin:'.$margin[1].'px '.$margin[0].'px;';


if(count($padding) == 2)
	$padding_out = 'padding:'.$padding[1].'px '.$padding[0].'px;';

// Заголовок модуля
if ($m['titlepub'] == "1"){$title_out = '<div class="mod-title'.$m['module_csssuf'].'">'.$m['title'].'</div>';}
else {$title_out = '';}

// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = 'edit_mode ';
	$edit_data = 'data-type="mod_route" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}

echo
'<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'mod-main'.$m['module_csssuf'].' w_100">
	<div class="mod-content" style="'.$margin_out.$padding_out.'">
		'.$title_out.'
		<div class="route"><!--
			--><div id="route_map" style="height:'.$m['p1'].'px;"></div><!--
			--><div>
				<div>
					<div>'.LANG_M_ROUTE_ENTER_THE_ADDRESS.'</div>
					<div>
						<textarea id="route_address" class="input_1" placeholder="'.LANG_M_ROUTE_STREET.'"></textarea>
					</div>
					<div>
						<input id="route_type_0" class="input_1" type="radio" name="route_type" value="masstransit" checked /><label for="route_type_0"></label><label for="route_type_0"> '.LANG_M_ROUTE_TRANSPORT.'</label>
					</div>
					<div>
						<input id="route_type_1" class="input_1" type="radio" name="route_type" value="auto" /><label for="route_type_1"></label><label for="route_type_1"> '.LANG_M_ROUTE_AUTO.'</label>
					</div>
					<div>
						<div id="route_button" class="button_orange">'.LANG_M_ROUTE_BUTTON.'</div>
					</div>
					<input type="hidden" id="route_pos_y" name="route_pos_y" value="'.$m['p2'].'" />
					<input type="hidden" id="route_pos_x" name="route_pos_x" value="'.$m['p3'].'" />
				</div>
			</div><!--
		--></div>
	</div>
</div>
';


if($m['p9'] != '')
{
	echo '<script type="text/javascript">DAN.appearance("mod_'.$m['id'].'", "'.$m['p9'].'");</script>	';
}


?>