<?php
// DAN 2010
// Редактируем раздел

defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $site, $task; 
		
	echo 
	'
	
	<table id="main-top-tab">
		<tr>
			<td class="imshop"><b>Импорт / Экспорт данных</b></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">
		<div>&nbsp;</div>
		<div class="import_excel">Excel</div>
		<div>&nbsp;</div>
		<table border="0" width="100%" class="import_export_table" style="border-collapse: collapse">
		<tr>
			<td><a class="main_import_zagruzka" href="http://'.$site.'/admin/com/shop/import_and_export/import_excel">Загрузка данных из Excel</a></td>
		</tr>
		<tr>
			<td><a class="main_export_vigruzka" href="http://'.$site.'/admin/com/shop/import_and_export/export_excel">Выгрузка данных в Excel</a></td>
		</tr>
		</table>
		<div>&nbsp;</div>
		<hr>
		<div>&nbsp;</div>
		<div class="import_yandex">Яндекс Маркет</div>
		<div>&nbsp;</div>
		<table border="0" width="100%" class="import_export_table" style="border-collapse: collapse">
		<tr>
			<td><a class="main_import_yandex" href="#">Произвести выгрузку в Яндекс маркет</a></td>
		</tr>
		</table>
		<div>&nbsp;</div>
	</div>	
	';	


} // конец функции

?>