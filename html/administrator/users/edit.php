<?php
// Сменить пароль
defined('AUTH') or die('Restricted access');

if(isset($_POST["none"])){$none = $_POST["none"];} else{$none = '';} // кнопка 'Отменить'

// Условие - отменить
if ($none == "Отменить"){Header ("Location: /admin"); exit;} 

function a_com()
{ 
	global $db, $domain;
	
	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/users.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Администратор сайта:</div>
		<div>&nbsp;</div>

		<form method="POST" action="/admin/users/update/">	
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-title-modules">Параметр</td>
				<td class="cell-desc-modules">Значение</td>			
			</tr>
		</table>		
	';	
	
	// вывод пользователей	
	$stmt_user = $db->query("SELECT * FROM users WHERE id = '2'");
	$m = $stmt_user->fetchAll();

	$users_id = $m['0']['id'];
	$users_login = $m['0']['login'];			
	$users_psw = $m['0']['psw'];					

			
	// вывод параметров	
	echo'		
		<table class="w100_bs1">		
			<tr>
				<td class="cell-title-modules"><b>Логин</b></td>
				<td class="cell-desc-modules">'.$users_login.'</td>				
			</tr>
			<tr>
				<td class="cell-title-modules"><b>Пароль</b></td>
				<td class="cell-desc-modules"><input id="psw1" type="password" name="psw1" size="20" required pattern="[a-zA-Z0-9]{8,20}" title="Только английские буквы и цифры, не менее 8 символов">  Не менее 8 символов, только английские буквы и цифры</td>				
			</tr>
			<tr>
				<td class="cell-title-modules"><b>Повторите пароль</b></td>
				<td class="cell-desc-modules"><input id="psw2" type="password" name="psw2" size="20" required pattern="[a-zA-Z0-9]{8,20}" title="Только английские буквы и цифры, не менее 8 символов"></td>				
			</tr>				
		</table>			
	';		
		
	// вывод параметров	
		echo'	
			<br/>
			&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
			<br/>
			&nbsp;			
			</form>				
		';			
	
		
} // конец функции компонента
?>