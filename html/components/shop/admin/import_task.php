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
			<td class="imshop"><b>Импорт данных</b></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">
		<div>&nbsp;</div>
		<div class="import_excel"><a href="http://'.$site.'/admin/com/shop/import/excel">Загрузка данных из Excel</a></div>
		<div>&nbsp;</div>		
	</div>	
	';	


} // конец функции

?>