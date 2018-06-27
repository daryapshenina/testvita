<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/excel/import/excel.js');
$head->addFile('/components/shop/admin/excel/tmp/excel.css');

function a_com()
{
	echo
	'
		<h1>Импорт данных из Excel</h1>

		<div>&nbsp;</div>

		1. Выберите действие
		<div>&nbsp;</div>
		<select id="excel_task">
			<option value="0">Загрузить товары</option>
			<option value="1">Загрузить товары (без изображений)</option>
			<option value="2">Загрузить изображения</option>
			<option value="3">Удалить все изображения</option>
			<option value="4">Удалить все характеристики</option>
			<option value="5">Очистить интернет - магазин</option>
		</select>

		<div>&nbsp;</div>

		2. Выберите файлы
		<div>&nbsp;</div>
		<input id="excel_files" type="file" name="file" multiple>

		<div>&nbsp;</div>

		3. Нажмите кнопку и дождитесь выполнения
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
