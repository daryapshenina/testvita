<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/excel/export/excel.js');
$head->addFile('/components/shop/admin/excel/tmp/excel.css');

function a_com()
{
	echo
	'
		<h1>Экспорт данных из Excel</h1>

		<div>&nbsp;</div>

		1. Выберите действие

		<div>&nbsp;</div>

		<select id="excel_task">
			<option value="0">Экспортировать товары</option>
			<option value="1">Экспортировать изображения</option>
		</select>

		<div>&nbsp;</div>

		2. Нажмите кнопку и дождитесь выполнения

		<div>&nbsp;</div>

		<input id="excel_button_start" class="greenbutton" value="Запустить" type="button" onclick="excel.run()">
		<div id="excel_counter_main"></div>

		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>

		<h1>Журнал</h1>

		<div id="excel_log"></div>
	';
}
