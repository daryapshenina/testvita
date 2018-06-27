<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

$modules_title_slider_skitter = $modules_title;
$modules_titlepub_slider_skitter = $modules_titlepub;

$domen = $_SERVER['SERVER_NAME'];

//Стиль слайдера
if ($modules_p3 == '0') {$style_slaid = 'numbers: false, preview: false';}
elseif ($modules_p3 == '1') {$style_slaid = 'thumbs: true';}
elseif ($modules_p3 == '2') {$style_slaid = 'numbers: true';}
elseif ($modules_p3 == '3') {$style_slaid = 'dots: true';}
elseif ($modules_p3 == '4') {$style_slaid = 'dots: true, preview: true';}
else {$style_slaid = 'thumbs: true';}

//Время задержки кадра
if ($modules_p4 == 1) { $modules_p4 = 'interval:1000';}
elseif ($modules_p4 == 2) { $modules_p4 = 'interval:1500';}
elseif ($modules_p4 == 3) { $modules_p4 = 'interval:2500';}
elseif ($modules_p4 == 4) { $modules_p4 = 'interval:3500';}
elseif ($modules_p4 == 5) { $modules_p4 = 'interval:5000';}
elseif ($modules_p4 == 6) { $modules_p4 = 'interval:7000';}
elseif ($modules_p4 == 7) { $modules_p4 = 'interval:10000';}
elseif ($modules_p4 == 8) { $modules_p4 = 'auto_play:false';}
else {$modules_p4 = 'interval:2500';}

// Заголовок модуля
if ($modules_titlepub_slider_skitter == "1"){$title_out = '<div class="mod-title">'.$modules_title_slider_skitter.'</div>';} else {$title_out = '';}

$out = '
	<div class="mod-main">
		<div class="mod-top">'.$title_out.'</div>
		<div class="mod-mid">
			<div class="mod-padding">
				<script type="text/javascript">
				$(document).ready(function(){
					$(\'.box_skitter_large\').skitter({xml: "/modules/slider_skitter/frontend/settings.xml", animation: \''.$modules_p1.'\', label: '.$modules_p2.', '.$style_slaid.', controls: '.$modules_p5.', navigation: '.$modules_p6.', velocity: '.$modules_p7.', '.$modules_p4.', width:\''.$modules_p10.'\', height:\''.$modules_p9.'\'});
				});
				</script>
				<div class="box_skitter box_skitter_large" style="height:'.$modules_p9.'px;width:'.$modules_p10.'px;"></div>
			</div>
		</div>
		<div class="mod-bot"></div>
	</div>
';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_slider_skitter" data-id="'.$modules_id.'">'.$out.'</div>';}
else {echo $out;}

?>
