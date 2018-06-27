<?php
// DAN 2015
defined('AUTH') or die('Restricted access'); 

$head->addFile('/components/shop/admin/chars/chars.css');

function a_com()
{ 
	global $db, $SITE;

	$stmt_select = $db->prepare("SELECT * FROM com_shop_char_name WHERE id = :id");
	$stmt_select->execute(array('id' => $SITE->d[5]));
	
	while($m = $stmt_select->fetch())
	{
		$id = $m['id'];
		$name = $m['name'];
		$unit = $m['unit'];
		$type = $m['type'];
		$ordering = $m['ordering'];		
	}
	
	$selected_number = '';
	$selected_string = '';
	
	if($type == 'number')
	{
		$type_out = 'число';
		$selected_number = 'selected';
	}
	else
	{
		$type_out = 'строка';
		$selected_string = 'selected';		
	}	
	
	echo '
		<form enctype="multipart/form-data" method="POST" action="/admin/com/shop/chars/update/'.$id.'/">
		<div class="container">
			<h1>Редактировать характеристику "'.$name.'"</h1>
			<div class="help_40">
				<span class="tooltip">
					<img src="/administrator/tmp/images/question-50.png" alt="Помощь" />
					<em>Типы характеристик</em>
					Типы: <b>Число</b>.<br><br>К числу применяются фильтры с числами, пример:<br><br>
					<table class="shop_char_help_table">
						<tr>
							<td style="width:80px;"><b>Ширина от:</b></td><td><input class="shop_char_input_50" type="number" value="20"> <b>до:</b><input class="shop_char_input_50" type="number" value="80"><b>см.</b></td>
						</tr>
						<tr>
							<td><b>Ширина:</b></td><td><input type="range" min="20" max="80" step="1"></td>
						</tr>				
					</table>
					<hr style="margin:40px 0px 40px 0px;">
					Типы: <b>Строка</b>.<br><br>К строке применяются фильтры с фиксированными значениями, пример:<br><br>
					<table class="shop_char_help_table">
						<tr>
							<td style="width:80px;"><b>Ширина:</b></td><td><select><option>20</option><option>40</option><option>60</option><option>80</option></select> <b>см.</b></td>
						</tr>
						<tr>
							<td><b>Длина:</b></td><td><input type="checkbox" value="20"><b>20 см.</b> &nbsp; <input type="checkbox" value="40"><b>40 см.</b> &nbsp; <input type="checkbox" value="60"><b>60 см.</b> &nbsp; <input type="checkbox" value="80"><b>80 см.</b></td>
						</tr>
						<tr>
							<td style="width:80px;"><b>Цвет:</b></td><td><select><option>белый</option><option>красный</option><option>чёрный</option></td>
						</tr>				
					</table>							
				</span>
			</div>			
			<table class="admin_table">
				<tr admin_table_tr_1>
					<td style="text-align:right;">&nbsp</td>
					<td>&nbsp</td>
				</tr>			
				<tr admin_table_tr_1>
					<td style="width:300px; text-align:right;">Название</td>
					<td><input class="input" name="name" type="text" value="'.$name.'" size="30"></td>
				</tr>
				<tr admin_table_tr_2>
					<td style="text-align:right;">Единица измерения</td>
					<td><input class="input" name="unit" type="text" value="'.$unit.'" style="width:90px;"></td>
				</tr>
				<tr admin_table_tr_2>
					<td style="text-align:right;">Тип</td>
					<td>
						<select class="input" name="type" style="width:100px;">
							<option value="string" '.$selected_string.'>Строка</option>
							<option value="number" '.$selected_number.'>Число</option>
						</select>
					</td>
				</tr>
				<tr admin_table_tr_2>
					<td style="text-align:right;">Порядок следования</td>
					<td><input class="input" name="ordering" type="number" value="'.$ordering.'" style="width:90px;"></td>
				</tr>				
			</table>
			<div style="margin-top:40px;">
				<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
			</div>
		</div>
		</form>
	';
}

?>
