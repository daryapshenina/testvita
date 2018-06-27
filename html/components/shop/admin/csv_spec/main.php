<?php
// DAN 2015
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/csv_spec/csv.css');
$head->addFile('/components/shop/admin/csv_spec/csv.js');

function a_com()
{
	echo '
		<div class="container">

			<h1>Импорт данных из CSV</h1>

			<div>&nbsp;</div>

			1. Выберите действие

			<div>&nbsp;</div>

			<div>
				<select id="csv_type">
					<option value="0">Загрузка товаров</option>
					<option value="1">Загрузка товаров без изображений</option>
					<option value="2">Загрузка характеристик</option>
					<option value="3">Загрузка изображений</option>
					<option value="4">Удалить все характеристики</option>
					<option value="5">Очистить интернет - магазин</option>
				</select>
			</div>

			<div>&nbsp;</div>
			<div>&nbsp;</div>

			2. Выберите csv файл или изображения

			<div>&nbsp;</div>

			<div>
				<input type="file" name="file" id="files" multiple>
			</div>

			<div>&nbsp;</div>
			<div>&nbsp;</div>

			3. Нажмите кнопку и дождитесь выполнения

			<div>&nbsp;</div>

			<div>
				<input id="csv_start" class="greenbutton" value="Запустить" type="button" onclick="csv.start()">
			</div>

			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<div>&nbsp;</div>

			<div id="csv_counter_main"></div>

			<div>&nbsp;</div>

			<h1>Журнал</h1>

			<div id="csv_log_main"></div>

		</div>
	';

}