<?php
defined('AUTH') or die('Restricted access');

include_once($root."/classes/Auth.php");

$title = 'Вход на сайт '.$domain;
$description = '';

$err = '';

if(isset($_SESSION['uid'])) unset($_SESSION['uid']);

// Заносим в базу данных
if($_SERVER["REQUEST_METHOD"] == 'POST')
{
	if(isset($_POST['email'])){$email = mb_strtolower(trim($_POST['email']));} else{$email = '';}
	if(isset($_POST['password'])){$psw = $_POST['password'];} else{$psw = '';}
	if(isset($_POST['captcha'])){$captcha = intval($_POST['captcha']);} else{$captcha = '';}
	if(isset($_POST['data'])){$url_return_code = $_POST['data'];} else{$url_return_code = '';}

	if (!preg_match("/^[^@]+@[^@]+\.[a-zа-я]{2,20}$/ui",$email)){$err .= 'Не правильно заполнено поле "Email"<br>';}
	if (!preg_match("/^[a-z0-9]{6,20}$/i",$psw)){$err .= 'Не правильно заполнено поле "Пароль"<br>';}


	// = Проверка кода на картинке ====================================================================
	if(isset($_SESSION['code']) && isset($captcha))
	{
		if($_SESSION['code'] != $captcha || $captcha == 0){$err .= 'Не верно указан код с картинки<br>';}
	}
	else {$err .= 'Отсутствует код с картинки<br>';}	

	if ($err == '') // нет ошибки
	{
		if(Auth::checkLogin($email, $psw))
		{
			Header ('Location: /account'); exit;			
		}
		else
		{
			$err .= '<div class="login_err">Неправильный логин или пароль.<br><br>';
			$err .= 'Возможно у вас выбрана другая раскладка клавиатуры или нажата клавиша "Caps Lock".</div>';
		}
	}	
}



function component()
{
	global $domain, $err;

	echo '
	<div class="registration_form">
		<h1 class="registration_h1">Ошибка</h1>
		<div class="registration_cont">'.$err.'</div>
	</div>
	';
}



?>