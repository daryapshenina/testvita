<?php
// Добавить пользователя
defined('AUTH') or die('Restricted access');

if(isset($_POST["bt"]))
{
	if(isset($_POST["login"])){$login = zapros($_POST["login"]);} else{$login = '';}
	if(isset($_POST["psw"])){$password_1 = $_POST["psw"];} else{$password_1 = '';}
	
	// ======= Проверка поля "Пароль" ===================================================================
	// удаляем опасные символы
	$login = trim(pregtrim($login));
	$password_1 = trim(pregtrim($password_1));
	// ======= / проверка поля "Пароль" =================================================================
	
	$pass_md5 = md5('dan'.$password_1);
	
	// Проверяем что бы не было такого логина
	$stmt_user = $db->prepare("SELECT * FROM users WHERE login = :login");
	$stmt_user->execute(array('login' => $login));
	
	if (preg_match("/^[a-z0-9]{4,20}$/is",$login) and preg_match("/^[a-z0-9]{8,20}$/is",$password_1))
	{
		if ($stmt_user->rowCount() == 0)
		{
			// Добавляем запись в бд
			$stmt_insert = $db->prepare("INSERT INTO users SET login = :login, description = 'Доп. логин', psw = :psw, level = '2', active = '1'");
			$stmt_insert->execute(array('login' => $login, 'psw' => $pass_md5));

			$log = '&nbsp;&nbsp;Пользователь <b style="color:#ff0000;">'.$login.'</b> добавлен с установленным паролем паролем <b style="color:#ff0000;">'.$password_1.'</b>';
		}
		else
		{
			$log = '&nbsp;&nbsp;<b style="color:#ff0000;">Пользователь существует!</b>';
		}
	}
	else
	{
		$log = '&nbsp;&nbsp;<b style="color:#ff0000;">Пользователь не добавлен!</b>';
	}
}

function a_com()
{
	global $site, $log;
		
	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/users.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Добавление нового пользователя</div>
		<div>&nbsp;</div>
		
		<form method="POST" action="/admin/users/adduser">	
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-title-modules">Параметр</td>
				<td class="cell-desc-modules">Значение</td>			
			</tr>
		</table>
		
		<table class="w100_bs1">		
			<tr>
				<td class="cell-title-modules"><b>Логин нового пользователя</b></td>
				<td class="cell-desc-modules"><input type="text" name="login" size="20" required pattern="[a-zA-Z0-9]{4,20}" title="Только английские буквы и цифры, не менее 4 символов" ></td>				
			</tr>
			<tr>
				<td class="cell-title-modules"><b>Пароль нового пользователя</b></td>
				<td class="cell-desc-modules"><input id="psw1" type="password" name="psw" size="20" required pattern="[a-zA-Z0-9]{8,20}" title="Только английские буквы и цифры, не менее 8 символов" ></td>				
			</tr>
		</table>
		
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">
		<br/>
		&nbsp;			
		</form>

		'.$log.'
	';
} // конец функции компонента
?>