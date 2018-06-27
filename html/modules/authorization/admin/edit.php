<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/modules/authorization/admin/edit.js');

$mod_id  = intval($d[3]);

include_once($root.'/administrator/modules/classes/Admin.php');
$module = new ModulesAdmin($mod_id);

function a_com()
{ 
	global $db, $domain, $module, $module, $mod_id;
	$m = $module->get_array();
	$url_select = array_fill (0, 3, '');
	$url_select[$m['p1']] = "selected";
		
	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{		
		echo '
		<div class="container">
			<h1><img border="0" src="/modules/authorization/admin/images/ico.png" style="float:left; margin-right:10px;" />Модуль "Авторизация пользователя"</h1>
	
			<form method="POST" action="/admin/modules/authorization/update/'.$m['id'].'">
			<table class="admin_table_2">			
				'.$module->pub().'
				<tr>
					<td style="width:200px;">Текст над формой ввода</td>
					<td><input class="input" type="text" name="text" size="50" value="'.$m['content'].'"></td>
				</tr>
				'.$module->block().'
				<tr>
					<td>Страница возврата</td>
					<td>
						<select onchange="f_url();" id="mod_authorization_url_select" class="input" name="url_select">
							<option value="1" '.$url_select[1].'>Интернет магазин - заказы</option>
							<option value="0" '.$url_select[0].'>Другой адрес</option>
						</select>
					</td>
				</tr>
				<tr id="mod_mod_authorization_url_tr">
					<td>Введите URL страницы возврата</td>
					<td><input class="input" type="text" name="url" size="50" value="'.$m['p2'].'"></td>
				</tr>	
				'.$module->order().'
			</table>
			<br>
			<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
			<br>
			&nbsp;
			</form>
		</div>
		<script type="text/javascript">
			f_url();
			DAN.accordion("accordion_head", "accordion_body");		
		</script>		
		';
	} // конец проверки 'enabled'
	else 
	{			
		echo '<div id="main-top">Модуль "authorization" не подключён</div>';
	}
} // конец функции


?>