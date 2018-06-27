<?php
// Скроллер
// content - url изображений, разделённых ;
// p1 - количество изображений (максимальное)
// p2 - минимальная ширина изображения
// p3 - скорость (время анимации)
// p4 - пауза
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/scroll_images/admin/style.css');
$head->addFile('/modules/scroll_images/admin/select_image.js');
$head->addFile('/js/drag_drop/drag_drop.js');


function a_com()
{
	global $db, $domain, $d;

	$id = $d[3];

	// вывод содержимого модуля
	$stmt_skroller = $db->prepare("SELECT * FROM modules WHERE module = 'scroll_images' && id = :id");
	$stmt_skroller->execute(array('id' => $id));

	$scroller = $stmt_skroller->fetch();

	$pub_s = array();
	for($i = 0; $i < 4; $i++)
	{
		if($scroller['pub'] == $i){$pub_s[$i] = 'selected';}else{$pub_s[$i] = '';}
	}

	if($scroller['titlepub']){$titlepub_check = 'checked';}else{$titlepub_check = '';}

	if($scroller['p5'] == '1')
		$autoscroll_check = 'checked';
	else
		$autoscroll_check = '';

	$num_s = array();
	for($i = 3; $i < 11; $i++)
	{
		if($scroller['p1'] == $i){$num_s[$i] = 'selected';}else{$num_s[$i] = '';}
	}

	$image_arr = explode(';', $scroller['content']);
	$images_out = '';

	foreach ($image_arr as $im)
	{
		if($im != ''){$images_out .= '<img class="drag_drop" src="'.$im.'" alt="">';}
	}


	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	$block_option = '';
	$stmt_block = $db->query("SELECT * FROM block");
	while($b = $stmt_block->fetch())
	{
		if ($b['block'] == $scroller['block']){$selected = 'selected';} else {$selected = '';}
		$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';
	}
	// ======== / загрузка блоков вывода =======

	echo '
		<div class="container">
			<h1><img border="0" src="/modules/scroll_images/admin/images/ico.png" style="float:left; margin-right:10px;" />Модуль "Скроллер"</h1>

			<form method="POST" action="/admin/modules/scroll_images/'.$id.'/update/">
			<table class="admin_table_2">
				<tr>
					<td width="200" height="25">Отображать</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub_s[1].'>Всегда</option>
							<option value="2" '.$pub_s[2].'>Только на настольном компьютере</option>
							<option value="3" '.$pub_s[3].'>Только на телефоне</option>
							<option value="0" '.$pub_s[0].'>Никогда</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Заголовок модуля</td>
					<td><input class="input" type="text" id="title" name="title" value="'.$scroller['title'].'" ></td>
				</tr>
				<tr>
					<td>Опубликовать заголовок</td>
					<td><input class="input" type="checkbox" id="titlepub" name="titlepub" value="1" '.$titlepub_check.' ><label for="titlepub"></label></td>
				</tr>
				<tr>
					<td>Позиция вывода, блок</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						&nbsp;- место вывода модуля
					</td>
				</tr>
				<tr>
					<td>Порядок следования</td>
					<td><input class="input" type="number" name="ordering" value="'.$scroller['ordering'].'" style="width:80px;"></td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td><b>Адаптивное отображение</b></td>
					<td>Скроллер адаптивный и подстраивается под ширину экрана. При уменьшении ширины экрана - картинки пропорционально уменьшаются до размеров минимального изображения (поле указанное ниже), если и в таком случае они не помещаются на экран - модуль уменьшает количество видимых изображений.<br>
					Высота скорллера и изображений устанавливается по первой картинке, что бы все они были одинаковой высоты.
					</td>
				</tr>
				<tr>
					<td colspan="2"> </td>
				</tr>
				<tr>
					<td>Видимых изображений</td>
					<td>
						<select class="input" name="num_max">
							<option value="3" '.$num_s[3].'>3</option>
							<option value="4" '.$num_s[4].'>4</option>
							<option value="5" '.$num_s[5].'>5</option>
							<option value="6" '.$num_s[6].'>6</option>
							<option value="7" '.$num_s[7].'>7</option>
							<option value="8" '.$num_s[8].'>8</option>
							<option value="9" '.$num_s[9].'>9</option>
							<option value="10" '.$num_s[10].'>10</option>
						</select>
						<span>Максимальное колчество видимых изображений</span>
					</td>
				</tr>
				<tr>
					<td>Минимальная ширина</td>
					<td><input id="width" type="range" min="100" max="300" step="10" name="width" value="'.$scroller['p2'].'"><span id="width_out">'.$scroller['p2'].' пикселей</span><span> Минимальная ширина отдельного изображения</span></td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td>Автоматическая прокрутка</td>
					<td>
						<input class="input" type="checkbox" id="autoscroll" name="autoscroll" value="1" '.$autoscroll_check.' ><label for="autoscroll"></label>
					</td>
				</tr>
				<tr>
					<td>Время анимации</td>
					<td>
						<input name="speed" id="speed" type="range" min="0.2" step="0.1" max="2" value="'.$scroller['p3'].'"><span id="speed_out">'.$scroller['p3'].' сек.</span>
					</td>
				</tr>
				<tr>
					<td>Длительность паузы</td>
					<td>
						<input name="pause" id="pause" type="range" min="0" max="5" step="1" value="'.$scroller['p4'].'"><span id="pause_out">'.$scroller['p4'].' сек.</span>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="image_add" class="greenbutton">Добавить изображение</div>
						<div class="help_40"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Изображения скроллера</em>Для изменения порядка следования - просто перетащите изображение в нужное место.<br><br>Для удаления - нажмите правую кнопку мыши.</span></div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><div id="image_list">'.$images_out.'</div></td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
			</table>
			<input type="hidden" id="images_order" name="images_order" value="'.$scroller['content'].'">
		</div>

		<div style="margin:40px 0px 60px 0px;">
			<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
		</div>
	</form>
	';
}
?>