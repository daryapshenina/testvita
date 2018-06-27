<?php
// Авторизация пользователя
// Получаем логин и пароль
defined('AUTH') or die('Restricted access');

$login_in = '';
$pass_in = '';

if(isset($_POST["login_in"])){$login_in = checkingeditor($_POST["login_in"]);}
if(isset($_POST["pass_in"])){$pass_in = checkingeditor($_POST["pass_in"]);}
if(isset($_POST["but"])){$but_in = $_POST["but"];} else {$but_in = '';}

// = Проверка кода на картинке ====================================================================
if(isset($_SESSION['code']) && isset($_POST['cod']))
{
	if($_SESSION['code'] == intval($_POST['cod']) && !empty($_SESSION['code']))
	{
		$cpt = 1;
	}
}

$stmt_users = $db->prepare("SELECT * FROM users WHERE login = :login");
$stmt_users->execute(array('login' => $login_in));

if($stmt_users->rowCount() > 0)
{
	$m = $stmt_users->fetchAll();

	$users_id = $m['0']['id'];
	$users_login = $m['0']['login'];
	$users_psw = $m['0']['psw'];
}

$psw_in = 'dan'.$pass_in;
$pass = md5($psw_in);

// сессия
if(isset($users_psw))
{
	$ses = '5za'.$users_psw;
	$sess = md5($ses);

	if (isset($users_login) && $users_login != '') // Если данные формы переданы (значение логина не пустое)
	{
		if ($users_login === $login_in && $users_psw === $pass && $cpt == 1) // Проверяем логин, пароль, капчу
		{
			$_SESSION['s5za'] = $sess;
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];

			// Если вошли методом POST - перезапрашиваем методом GET (метод пост при возврате требует повторную отправку формы)
			if($_SERVER["REQUEST_METHOD"] == 'POST'){Header ('Location: /admin'); exit;}

		}
		else
		{
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."/");
	  		exit;
		}
	}
}

if (isset($d) && $d[1]=="logout") // Признак выхода. Уничтожаем сессии
{
  	session_destroy();
	header("Location: http://".$_SERVER['HTTP_HOST']."/");
	exit;
}

if (isset($_SESSION['s5za']) && $_SESSION['ip'] == $_SERVER['REMOTE_ADDR']) return; // Проверка существования сессии и IP
//if (isset($_SESSION['s5za'])) return; // Проверка существования сессии и IP
else {
	$rand_url = mt_rand();

	echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="keywords" content="Вход в систему управления сайтом" />
  <meta name="description" content="Система управления сайтом 5za" />
  <title>Вход в систему управления сайтом</title>
<link rel="stylesheet" href="/administrator/tmp/admin_style.css" type="text/css" />
</head>
<body class="bw">
<table class="main_tab">
	<tr>
		<td class="w30pc-h100px">&nbsp;</td>
		<td class="w40pc-h100px">&nbsp;</td>
		<td class="w30pc-h100px">&nbsp;</td>
	</tr>
	<tr>
		<td class="w30pc-h220px">&nbsp;</td>
		<td class="w40pc-h220px">
			<div align="center">
				<table class="main-tab-lg">
					<tr>
						<td class="w120px-h80px">&nbsp;</td>
						<td class="vhod">Вход</td>
						<td >&nbsp;</td>
					</tr>
					<tr>
						<td class="w120px-h120px">&nbsp;</td>
						<td class="logintext">
							<form method="post" action="/admin/">
								Имя пользователя<br/>
								<input class="inp" name="login_in" size="20"/>
								<div>&nbsp;</div>
								Пароль<br/>
								<input class="inp" name="pass_in" type="password" size="20"/>
								<div>&nbsp;</div>
								Введите число с картинки<br/>
								<img src="/administrator/captcha/pic.php?'.$rand_url.'" align="middle">
								<input class="inp" type="text" name="cod" size="4" maxlength="4">
								<div>&nbsp;</div>
								<input class="cursor-pointer" type="submit" value="Вход" name="but"/>
								<div>&nbsp;</div>
							</form>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="h40px">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</td>
		<td class="w30pc-h220px">&nbsp;</td>
	</tr>
</table>

</body>
</html>

';
}
exit;


?>
