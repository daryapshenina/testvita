<?php
// DAN разработка - январь 2014
// По ajax - запросу выводит список характеристик
define("AUTH", TRUE);

session_start();

include("../../../config.php");
include("../../../lib/lib.php");
include("../../../administrator/login.php");

$section_id = intval($_POST['section_select']);
$item_id = intval($_POST['item_id']);

// === MySQL ======================================================
$conn = mysql_connect ($host, $user, $passwd) or die ("Соединение с MySQL не установлено!"); 
mysql_select_db($dbname) OR die ("Соединение с базой данных не установлено");
mysql_query('SET CHARACTER SET utf8');

$text_type = "cтрока / число";
$bg_color = '';

// характеристики товара
$item_num = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
$item_num_rows = mysql_num_rows($item_num);

if($item_num_rows > 0) // характеристики
{
	while($z = mysql_fetch_array($item_num))
	{	
		for($i = 1; $i <= 30; $i++)
		{
			$item_characteristic[$i] = $z["characteristic_".$i];
		}
	}
}
else
{
	for($i = 1; $i <= 30; $i++)
	{
		$item_characteristic[$i] = '';
	}
}

// характеристики раздела	
$section_num = mysql_query("SELECT * FROM `com_shop_section` WHERE `id` = '$section_id' AND (`char_enable_1`='1' OR `char_enable_2`='1' OR `char_enable_3`='1' OR `char_enable_4`='1' OR `char_enable_5`='1' OR `char_enable_6`='1' OR `char_enable_7`='1' OR `char_enable_8`='1' OR `char_enable_9`='1' OR `char_enable_10`='1' OR `char_enable_11`='1' OR `char_enable_12`='1' OR `char_enable_13`='1' OR `char_enable_14`='1' OR `char_enable_15`='1' OR `char_enable_16`='1' OR `char_enable_17`='1' OR `char_enable_18`='1' OR `char_enable_19`='1' OR `char_enable_20`='1' OR `char_enable_21`='1' OR `char_enable_22`='1' OR `char_enable_23`='1' OR `char_enable_24`='1' OR `char_enable_25`='1' OR `char_enable_26`='1' OR `char_enable_27`='1' OR `char_enable_28`='1' OR `char_enable_29`='1' OR `char_enable_30`='1' ) LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
$section_num_rows = mysql_num_rows($section_num);

if($section_num_rows > 0) // характеристики
{
	echo '
	<div>&nbsp;</div>	
	<table class="main-tab">
		<tr>
			<td height="25" width="20">&nbsp;</td>
			<td width="150" style="text-align:right;"><b>Характеристики</b> <div class="help" style="text-align:left;"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Характеристики товара</em>Название характеристики можно редактировать в <a href="http://'.$site.'/admin/com/shop/sectionedit/'.$section_id.'">разделе</a></span></div></td>
			<td width="10">&nbsp;</td>
			<td width="300" height="25"><b>Значение характеристик</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Значение характеристик</em>Если данная характеристика имеет несколько значений, вводите значения через точку с запятой. <br>Пример:<br><br><b>белый;красный;синий</b></span></div></td>
			<td width="80"><b>Ед. изм.</b> <div class="help"><span class="tooltip"><img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Единица измерения</em>Наименование единиц измерения можно редактировать в <a href="http://'.$site.'/admin/com/shop/sectionedit/'.$section_id.'">разделе</a></span></div></td>
			<td><b>Тип данных</b>
				<div class="help">			
					<span class="tooltip">
					<img src="http://'.$site.'/administrator/tmp/images/question-50.png" alt="Помощь" />
					<em>Тип данных - строка или число</em>	
					Для характеристик типа <b>строка</b> в качестве значений могут быть как текстовые так и числовые поля.
					Используется такой тип фильтров (примеры):
					<br><br>
					1. <b>Выпадающий список:</b><br>
					<select size="1" name="D1">
						<option value="Характеристика 1">Характеристика 1</option>
						<option value="Характеристика 2">Характеристика 2</option>
						<option value="Характеристика 3">Характеристика 3</option>
						<option value="Характеристика 4">Характеристика 4</option>
						<option value="Характеристика 5">Характеристика 5</option>
						<option value="Характеристика 6">Характеристика 6</option>
						<option value="Характеристика 7">Характеристика 7</option>
					</select>
					<br><br>
					2. <b>Переключатель:</b><br>
					<input type="radio" value="V1" checked name="R1">36<br>
					<input type="radio" value="V1" name="R1">38<br>
					<input type="radio" value="V1" name="R1">40<br>
					<br>
					3. <b>Флаг:</b><br>
					<input type="checkbox" name="C1" value="ON" checked>Дополнительные опции<br>
					<hr>
					Для характеристик типа <b>число</b> используются только числовые значения.	
					Используется такие типы фильтров (пример):
					<br><br>
					Ширина от <input type="text" name="T1" size="5" value="80">см.&nbsp; до 
					<input type="text" name="T2" size="5" value="120">см.<br>
					</span>
				</div>
			</td>
		</tr>
	';

	while($s = mysql_fetch_array($section_num))
	{	
		for($i = 1; $i <= 30; $i++)
		{
			$section_char_enable[$i] = $s["char_enable_".$i];
			$section_characteristic[$i] = $s["characteristic_".$i];
			$section_char_unit[$i] = $s["char_unit_".$i];
			
			if($section_char_enable[$i] == 1)
			{
				if($i > 25){$text_type = 'число'; $bg_color = 'background: #c1efff;';}
				echo'
				<tr style="'.$bg_color.'">
					<td>&nbsp;</td>
					<td style="line-height:25px; text-align:right;">'.$section_characteristic[$i].'</td>
					<td>&nbsp;</td>
					<td><input type="text" name="characteristic_'.$i.'" value="'.$item_characteristic[$i].'" size="50" ></td>
					<td style="line-height:25px;">'.$section_char_unit[$i].'</td>
					<td style="line-height:25px; color: #999999;">'.$text_type.'</td>
				</tr>
				';
			}
			else
			{
				/*
				if($i > 7){$text_type = 'число'; $bg_color = 'background: #c1efff;';}
				echo'
				<tr style="color:#999999; '.$bg_color.'">
					<td>&nbsp;</td>
					<td style="line-height:25px; text-align:right;">'.$characteristic[$i].'</td>
					<td>&nbsp;</td>
					<td style="line-height:25px;">Характеристика отключена в данном <a style="color: #999999;" href="http://'.$site.'/admin/com/shop/sectionedit/'.$section_id.'">разделе</a></td>
					<td style="line-height:25px;">'.$char_unit[$i].'</td>
					<td style="line-height:25px;">'.$text_type.'</td>
				</tr>
				';	
				*/
			}
		}
	}

	echo '</table>
	<div>&nbsp;</div>';
}
else
{
	echo '<div style="padding:20px;">Характеристики отсутствуют. Установить характеристики для данного раздела можно в настройках <a href="http://'.$site.'/admin/com/shop/sectionedit/'.$section_id.'">раздела</a></div>';
}

?>
