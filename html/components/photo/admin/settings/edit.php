<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/photo/admin/settings/settings.css');

$item_id = intval($admin_d4);

function a_com()
{
	global $root, $db, $domain, $photo_settings;

	// Размеры большого и малого изображения
	$x_small = '<input type="number" min="50" max="500" name="x_small" size="3" value="'.$photo_settings['x_small'].'" required >';
	$y_small = '<input type="number" min="50" max="500" name="y_small" size="3" value="'.$photo_settings['y_small'].'" required >';

	$x_big = '<input type="number" min="400" max="1000" name="x_big" size="3" value="'.$photo_settings['x_big'].'" required >';
	$y_big = '<input type="number" min="400" max="1000" name="y_big" size="3" value="'.$photo_settings['y_big'].'" required >';

	// Метод ресайза
	$resize_method = array_fill(0, 3, '');
	switch($photo_settings['resize_method'])
	{
		case 1:$resize_method[0] = "checked";break;
		case 2:$resize_method[1] = "checked";break;
		case 3:$resize_method[2] = "checked";break;
	}


	// ======= РАЗДЕЛ =======
	// Количество изображений на странице
	$quantity = '<input type="number" min="10" max="1000"  name="quantity" size="3" value="'.$photo_settings['quantity'].'" required >';

	// Сортировка фотографий на странице
	$sorting_items = array_fill(0, 6, '');
	$sorting_items[$photo_settings['sorting_items']] = "selected";	

	echo '
		<h1>Настройки фотогалереи:</h1>
		<form method="POST" action="/admin/com/photo/settings/update">
		<table class="admin_table">
			<tr>
				<th style="width:50px">&nbsp;</th>
				<th style="width:250px">Параметр</th>
				<th>Значение</th>
			</tr>
			<tr>
				<td class="td_sep" style="width:50px">&nbsp;</td>
				<td class="td_sep" style="width:200px"><b>ИЗОБРАЖЕНИЯ:</b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>1</td>
				<td>Размер малого изображения</td>
				<td>по ширине: '.$x_small.' px. &nbsp;&nbsp; по высоте: '.$y_small.' px.</td>
			</tr>
			<tr>
				<td>2</td>
				<td>Размер большого изображения</td>
				<td>по ширине: '.$x_big.' px. &nbsp;&nbsp; по высоте: '.$y_big.' px.</td>
			</tr>
			<tr>
				<td>3</td>
				<td>Метод создания <br/>малого изображения:</td>
				<td>
					<span class="lineheight20">
						<input type="radio" value="1" '.$resize_method[0].' name="resize_method">умный ресайз <i>(вставка по большей стороне)</i> <br/>
						<input type="radio" value="2" '.$resize_method[1].' name="resize_method">подрезка <i>(подрезка большей стороны)</i><br/>
						<input type="radio" value="3" '.$resize_method[2].' name="resize_method">скукожить <i>(смять, пропорции игнорируются)</i><br/>
					</span>
				</td>
			</tr>
			<tr>
				<td class="td_sep">&nbsp;</td>
				<td class="td_sep"><b>РАЗДЕЛ:<b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>4</td>
				<td>Количество фотографий на странице</td>
				<td>'.$quantity.'</td>
			</tr>
			<tr>
				<td>5</td>
				<td>Сортировка фотографий:</td>
				<td>
					<select name="sorting_items">
						<option value="0" '.$sorting_items[0].'>Ручная (Настраивается при добавлении или редактировании товара)</option>
						<option value="1" '.$sorting_items[1].'>По дате (Новые сверху)</option>
						<option value="2" '.$sorting_items[2].'>По дате (Старые сверху)</option>
						<option value="3" '.$sorting_items[3].'>По алфавиту (по возрастанию)</option>
						<option value="4" '.$sorting_items[4].'>По алфавиту (по убыванию)</option>
					</select>
				</td>
			</tr>
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;
		</form>
	';

}
?>
