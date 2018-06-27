<?php
defined('AUTH') or die('Restricted access');

$mod_id  = intval($d[3]);

$head->addFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&.js');
$head->addFile('/modules/route/admin/tmp.js');

include_once($root.'/administrator/modules/classes/Admin.php');
$module = new ModulesAdmin($mod_id);

function a_com()
{
	global $db, $domain, $module, $mod_id;
	$m = $module->get_array();

	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if($m['enabled'] == "1")
	{
		echo '
		<div class="container">
			<h1><img border="0" src="/modules/route/admin/images/ico.png" style="float:left; margin-right:10px;" />Модуль "Поиск маршрута"</h1>

			<form method="POST" action="/admin/modules/route/update/'.$m['id'].'">

			<table class="admin_table_2">
				'.$module->description().'
				'.$module->pub().'
				'.$module->title().'
				'.$module->block().'
				'.$module->order().'
				<tr>
					<td width="200" height="25" style="vertical-align:middle;">Высота карты</td>
					<td><input class="input" type="number" min="300" max="600" step="10" required type="text" name="height" size="4" value="'.$m['p1'].'"></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<div>Кликните левой кнопкой мыши на карте для установки координаты</div>
			<div>&nbsp;</div>
			<input id="route_y" name="y" type="hidden" value="'.$m['p2'].'" />
			<input id="route_x" name="x" type="hidden" value="'.$m['p3'].'" />
			<div id="route_map" style="height:500px;"></div>
			<div>&nbsp;</div>
			<div class="seo_fon">
				<div class="accordion_head left_head">'.LANG_M_SIZE.'</div>
				<div class="accordion_body mod_icon_ab4">
					<div>&nbsp;</div>
					<table class="admin_table_2">
						'.$module->margin($m['p5']).'
						'.$module->padding($m['p6']).'
					</table>
					<div>&nbsp;</div>
				</div>
			</div>
			<br>
			<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
			<br>
			&nbsp;
			</form>
		</div>
		<script type="text/javascript">
			DAN.accordion("accordion_head", "accordion_body");
		</script>
		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">Модуль "route" не подключён</div>';
	}
} // конец функции


?>