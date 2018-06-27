<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/account/frontend/restore/tmp/restore.css');
$psw = 'DAN_restore';

$title = 'Восстановление пароля';
$description = '';

function component()
{
	global $domain, $err, $psw;
	
	// ХЕШ
	$rand = rand(0, 999999);
	$vector = substr(md5($rand), 0, 16);

	$str = time();
	$hash = openssl_encrypt($str, 'AES-256-CTR', $psw, 0, $vector);	

	$rand = rand(1000,9999999);

	echo '<h1>Восстановление пароля</h1>';
	echo '<form method="post" action="/account/restore/send">';
	echo '<div class="auth_form_container">';		
	echo '<h3 class="auth_form_title">Восстановление пароля</h3>';
	echo '<div class="auth_form_div_email"><input class="input auth_form_email" type="email" name="email" placeholder="Email" autocomplete="off" maxlength="30" required title="Укажите корректный email"></div>';
	echo '<input type="hidden" name="h" value="'.$hash.'"><input type="hidden" name="v" value="'.$vector.'">';
	echo '<div class="auth_form_div_but"><input class="auth_form_but" type="submit" value="Отправить ссылку" name="send"></div>';
	echo '</div>';		
	echo '</form>';

}



?>