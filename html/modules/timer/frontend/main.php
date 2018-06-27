<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

$modules_title_editor = $modules_title;
$modules_titlepub_editor = $modules_titlepub;
$suf_editor = $modules_module_csssuf;


if ($modules_pub == "1")
{
	// Заголовок модуля
	if ($modules_titlepub_editor == "1"){$title_out = '<div class="mod-top">'.$modules_title_editor.'</div>';} else {$title_out = '';}
	
	$out = '<div class="mod-main'.$suf_editor.'">
	'.$title_out.'
		<div class="timer_container ">
			<div class="timer_title">До окончания акции осталось:</div>
			<div id="timer_value" data-end="'.$modules_content.'"></div>
			<div><div class="timer_date">Дней</div><div class="timer_date">Часов</div><div class="timer_date">Минут</div><div class="timer_date">Секунд</div></div>
		</div>
		<script>timer();</script>
	</div>
	';			

	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_timer" data-id="'.$modules_id.'">'.$out.'</div>';}
	else {echo $out;}	
}

?>