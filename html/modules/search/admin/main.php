<?php
defined('AUTH') or die('Restricted access');

if($d[3] == 'add'){include $root."/modules/search/admin/add.php"; exit;}
if($d[4] == 'update'){include $root."/modules/search/admin/update.php"; exit;}

// Если эти два условия не сработали - подключаем вывод модуля для редактирования 
$mod_id = $d[3];


function a_com()
{ 
	global $db, $domain, $mod_id;
	
	$stmt_module = $db->prepare("SELECT * FROM modules WHERE module = 'search' AND id = :id");
	$stmt_module->execute(array('id' => $mod_id));
	$module = $stmt_module->fetch();

	$stmt_block = $db->query("SELECT * FROM block");

	$block_option = '';
	while($b = $stmt_block->fetch())
	{
		if($b['block'] == $module['block']){$selected = 'selected';} else {$selected = '';}
		$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';			
	}			
	
	$pub = array();
	for($i = 0; $i < 4; $i++)
	{
		if($module['pub'] == $i){$pub[$i] = 'selected';}else{$pub[$i] = '';}
	}	

	
	// устанавливаем признак публикации заголовка модуля
	if ($module['titlepub'] == 1){$titlepub = "checked";} else{$titlepub = "";} 		
	
	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($module['enabled'] == "1")
	{
		echo '
		<div class="container">
			<h1><img border="0" src="/modules/search/admin/images/ico.png" width="25" height="25" style="float: left; padding-top: 2px;" />&nbsp;&nbsp;Поиск</h1>
		
			<form method="POST" action="/admin/modules/search/'.$mod_id.'/update">				
			<table class="admin_table_2">		
				<tr>		
					<td>Отображать</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub[1].'>Всегда</option>
							<option value="2" '.$pub[2].'>Только на настольном компьютере</option>
							<option value="3" '.$pub[3].'>Только на телефоне</option>
							<option value="0" '.$pub[0].'>Никогда</option>
						</select>					
					</td>
				</tr>
				<tr>	
					<td style="width:200px;">Название модуля</td>
					<td><input class="input" type="text" name="title" size="20" value="'.$module['title'].'"></td>
				</tr>				
				<tr>		
					<td>Опубликовать заголовок модуля</td>
					<td><input class="input" type="checkbox" id="titlepub" name="titlepub" value="1" '.$titlepub.' ><label for="titlepub"></label></td>
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
					<td><input class="input" type="number" name="ordering" value="'.$module['ordering'].'" style="width:80px;"></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="1" '.$mod_id.' >
			<div style="margin:40px 0px 60px 0px"><input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none"></div>
			</form>
		</div>
		';
	} // конец проверки 'enabled'
	else 
	{			
		echo '<div id="main-top">Модуль <b>"Редактируемый модуль"</b> не подключён</div>';
	}
} // конец функции


?>