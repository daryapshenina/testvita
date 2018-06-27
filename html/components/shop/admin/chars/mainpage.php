<?php
defined('AUTH') or die('Restricted access'); 

$head->addFile('/components/shop/admin/chars/chars.css');
$head->addFile('/js/dan.framework.js');
$head->addFile('/js/drag_drop/drag_drop.js');
$head->addFile('/components/shop/admin/chars/chars.js');


function a_com()
{ 
	global $db;
	
	$chars_query = $db->query("SELECT * FROM com_shop_char_name ORDER BY ordering");
	
	$out = '';
	
	while($char = $chars_query->fetch())
	{
		if($char['type'] == 'number'){$type_out = 'число';}
		else{$type_out = 'строка';}
		
		$out .= '<table class="drag_drop" style="border-spacing:0; width:100%;" draggable="true" data-id="'.$char['id'].'">';
		$out .= '<tr>';
		$out .= '<td class="char_td" style="width:42px;">'.$char['id'].'</td>';
		$out .= '<td class="char_td" style="width:42px;"><div class="drag_move" title="Перетащите, что бы изменить порядок следования">&#9016;</div></td>';
		$out .= '<td class="char_td" style="width:292px;"><a href="/admin/com/shop/chars/edit/'.$char['id'].'" draggable="false">'.$char['name'].'</a></td>';
		$out .= '<td class="char_td" style="width:192px;">'.$char['unit'].'</td>';
		$out .= '<td class="char_td" style="width:142px;">'.$type_out.'</td>';
		$out .= '<td class="char_td" style="width:192px;">'.$char['ordering'].'</td>';
		$out .= '<td class="char_td">&nbsp;</td>';
		$out .= '</tr>';
		$out .= '</table>';	
	}
	
	echo '
		<div class="container">
			<h1>Характеристики и фильтры товаров:</h1>
			<a href="/admin/com/shop/chars/add" class="greenbutton">+ Добавить характеристику</a>
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
				<tr>
					<th style="width:50px;">id</th>
					<th style="width:50px;">&#9016;</th>
					<th style="width:300px;">Наименование</th>
					<th style="width:200px;">Единица измерения</th>
					<th style="width:150px;">Тип</th>
					<th style="width:200px;">Порядок следования</th>
					<th>&nbsp;</th>
				</tr>
			</table>
			<div id="drag_trg">'.$out.'</div>
		</div>	
	';
}

?>