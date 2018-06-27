<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

function a_com()
{
	global $db, $domain, $mod_id;

	$stmt = $db->prepare("SELECT * FROM modules WHERE module = 'exchangerates' AND id = :id");
	$stmt->execute(array('id' => $mod_id));
	
	$m = $stmt->fetch();
	


	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	$block_query = $db->query("SELECT * FROM block");

	$block_option = '';
	if($block_query->rowCount() > 0)
	{
		while($b = $block_query->fetch())
		{
			if ($b['block'] == $m['block']){$selected = 'selected';} else {$selected = '';}
			$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';				
		}
	}
	// ======== / загрузка блоков вывода =======

	// устанавливаем признак публикации
	$pub_0 = $pub_1 = $pub_2 = $pub_3 = '';

	switch($m['pub'])
	{
		case 0:
			$pub_0 = 'selected="selected"';
			break;

		case 1:
			$pub_1 = 'selected="selected"';
			break;

		case 2:
			$pub_2 = 'selected="selected"';
			break;

		case 3:
			$pub_3 = 'selected="selected"';
			break;
	}

	// устанавливаем признак публикации заголовка модуля
	if ($m['titlepub'] == 1){$titlepub = "checked";} else{$titlepub = "";}

	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{
		echo '
		<div class="container">
			<h1><img border="0" src="/modules/exchangerates/admin/images/ico.png" style="float:left; margin-right:10px;" />Курс валют</h1>

			<form method="POST" action="/admin/modules/exchangerates/'.$m['id'].'/update">

			<table class="admin_table_2">
				<tr>
					<td style="width:200px;">Название модуля</td>
					<td><input class="input" type="text" name="title" size="20" value="'.$m['title'].'"></td>
				</tr>
				<tr>
					<td>Описание</td>
					<td>'.$m['description'].'</td>
				</tr>
				<tr>
					<td>Опубликовать модуль</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub_1.'>Всегда</option>
							<option value="2" '.$pub_2.'>Только на компьютерах</option>
							<option value="3" '.$pub_3.'>Только на телефонах</option>
							<option value="0" '.$pub_0.'>Никогда</option>
						</select>					
					</td>
				</tr>
				<tr>
					<td>Опубликовать заголовок</td>
					<td><input class="input" id="titlepub" name="titlepub" type="checkbox"  value="1" '.$titlepub.' ><label for="titlepub"></td>
				</tr>
				<tr>
					<td>Позиция вывода, блок</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						&nbsp;Определяет в каком месте (блоке) сайта вывести данный модуль
					</td>
				</tr>
				<tr>
					<td>Порядок следования</td>
					<td><input class="input" type="number" name="ordering" value="'.$m['ordering'].'" style="width:80px;"></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="1" '.$m['id'].' >
			<div style="margin-top:40px;">
			<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
			</div>
			</form>
		</div>
		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">Модуль <b>"Курс валюты"</b> не подключён</div>';
	}
} // конец функции


?>