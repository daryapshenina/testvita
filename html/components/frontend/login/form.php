<?php
defined('AUTH') or die('Restricted access');

// Мета - теги
$title = 'Регистрация пользователя';
$description = '';

$head->addFile('/components/profile/frontend/login/tmp/style.css');

function component()
{
	global $root, $db, $domain, $domain_idn;

	$rand = rand(5, 10);

	echo '
	<form method="post" action="/profile/login/login">	
	<div class="registration_form">
		<h1 class="registration_h1">Вход на сайт '.$domain_idn.'</h1>
		<div class="registration_cont">
			<input class="input registration_email" type="email" name="email" size="20" placeholder="Email" autocomplete="off" maxlength="30" required title="Укажите корректный email">
			<input class="input" type="password" name="password" size="20" placeholder="Пароль" class="registration_password" autocomplete="off" maxlength="30" required pattern="[a-zA-Z0-9]{6,20}" title="Только английские буквы, и цифры без пробелов, от 6 до 20 символов"> 
		</div>
		<div class="registration_cont">
			<img src="/administrator/captcha/pic.php?'.$rand.'" class="registration_img"> 
			<input class="input registration_captcha" type="text" name="captcha" size="4" autocomplete="off" maxlength="4" required pattern="[0-9]{4}"  title="Введите 4 цифры с картинки">
			<span class="registration_captcha_text">Введите цифры с картинки</span>
		</div>		
		<div class="registration_cont">
			<input class="button_green" type="submit" value="Вход" name="send"> 
		</div>			
	</div>
	</form>
	';
}
?>