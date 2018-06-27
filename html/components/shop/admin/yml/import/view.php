<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/yml/tmp/yml.css');
$head->addFile('/components/shop/admin/yml/import/yml.js');

function a_com()
{
	global $db;

	// --- ТИПЫ ЦЕН ---
	$stmt_price_type = $db->query("SELECT id, name FROM com_shop_price_type");

	if($stmt_price_type->rowCount() > 0)
	{
		$price_type_out = '';
		while($pt = $stmt_price_type->fetch())
		{
			$price_type_out .= '<tr><td>'.$pt['name'].'</td><td><input class="input price_type" style="width:80px;" type="number" name="price_user['.$pt['id'].']" size="3" value="0"> %</td></tr>';
		}
	}
	else
	{
		$price_type_out = '';		
	}

	echo
	'
		<h1>Импорт данных из <span style="color:#E52620;">Я</span>ндекс.Маркет</h1>

		<input id="yml_file" class="input_1" type="file">

		<div>&nbsp;</div>

		<b>Родительская категория:</b>
		<br />

		<select id="root" class="input">
			<option value="">Корень сайта</option>
			' . tree() . '
		</select>

		<div>&nbsp;</div>
		<div><b>Установить надбавку к загружаемым ценам:</b></div>
		<table id="prite_input_tab"; class="admin_table_2">
			<tr>
				<td style="width:150px;">Основная цена</td>
				<td><input id="price_main" class="input price_type" style="width:80px;" type="number" name="price_main" size="5" value="0"> %</td>
			</tr>
			'.$price_type_out.'
			<tr>
				<td style="width:150px;">Новинка</td>
				<td><input id="new" type="checkbox" class="input" name="new" value="1"><label for="new"></label></td>
			</tr>
			<tr>
				<td style="width:150px;">Акции / распродажа</td>
				<td><input id="sale" type="checkbox" class="input" name="sale" value="1"><label for="sale"></label></td>
			</tr>
			<tr>
				<td style="width:150px;">Удалить старые товары (старше 1 дня)</td>
				<td><input id="delete_old" type="checkbox" class="input" name="delete_old" value="1"><label for="delete_old"></label></td>
			</tr>
		</table>
		<div>&nbsp;</div>
		<input id="yml_button_start" class="greenbutton" value="Запустить" type="button" onclick="yml.run()">
		<div><span id="yml_counter_current"></span><span id="yml_counter_main"></span><span id="yml_counter_sum"></span></div>

		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>

		<h1>Журнал</h1>

		<div id="yml_log"></div>
	';
}

function tree($_id = 0, $_lvl = 0, &$_result = '')
{
	global $db;

	$PREPARE = $db->prepare('SELECT * FROM menu WHERE pub = 1 AND parent = :parent AND component = "shop" AND p1 = "section" ORDER BY ordering ASC');

	$PREPARE->execute(
		array(
			'parent' => $_id
		)
	);

	$sections = $PREPARE->fetchAll();
	$_lvl++;
	$prefix = ' ';

	for($i = 1;$i < $_lvl; $i++)
		$prefix .= '- ';

	foreach($sections as $iter)
	{
		$_result .= '<option value="' . $iter['id_com'] . '">' . $prefix . $iter['name'] . '</option>';
		tree($iter['id'], $_lvl, $_result);
	}

	return $_result;
}