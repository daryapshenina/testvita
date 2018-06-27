<?php
// DAN 2012
// Импорт данных из Excel

defined('AUTH') or die('Restricted access');

set_time_limit(90); // время работы скрипта 90 сек.

$introtext_update = intval($_POST["intro_text"]);
$fulltext_update = intval($_POST["full_text"]);
$image_update = intval($_POST["image"]);

function a_com()
{
	global $site, $root, $file_name_arr, $introtext_update, $fulltext_update, $image_update;	
	
	if ($image_update == 0){$step = 2;}else{$step = 3;}
	
	echo 
	'
	<table id="main-top-tab">
		<tr>
			<td class="imshop">Импорт данных из Excel - шаг 1 из '.$step.'</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">		
		<div>&nbsp;</div>		
		<div>&nbsp;</div>
		<div class="import_excel">Файл загружен - <font color="#009933">шаг 1 из '.$step.'</font></div>	
		<div>&nbsp;</div>	
		<form method="post" action="http://'.$site.'/admin/com/shop/import_and_export/import_excel_22" enctype="multipart/form-data">
			<input class="import_dalee" type="submit" value="Далее" name="bt">
			<input  type="hidden" value="'.$file_name_arr[1].'" name="ext">
			<input  type="hidden" value="'.$introtext_update.'" name="intro_text">
			<input  type="hidden" value="'.$fulltext_update.'" name="full_text">
			<input  type="hidden" value="'.$image_update.'" name="image">
		</form>		
	</div>	
	';	
}

?>