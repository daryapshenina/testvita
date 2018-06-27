<?php
// автоизация
defined('AUTH') or die('Restricted access');

// ======= HTTP АВТОРИЗАЦИЯ =====================================================================
// ------- CGI ----------------------------------------------------------------------------------
// Дописываем в .htaccess
// RewriteEngine On    # Должна уже стоять
// RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization},L]

if(isset($_SERVER["REMOTE_USER"])){$remote_user = $_SERVER["REMOTE_USER"];}else{$remote_user = $_SERVER["REDIRECT_REMOTE_USER"];}
$remote = base64_decode(substr($remote_user,6));
if ($remote){list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $remote);}


if (!isset($_SERVER['PHP_AUTH_USER'])) // --- mod_php ---
{
    header('WWW-Authenticate: Basic realm=""');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Доступ запрещен';
    exit;
}
else // --- Авторизация ---
{
	$login_in = $_SERVER['PHP_AUTH_USER'];
	$psw_in = $_SERVER['PHP_AUTH_PW'];
	// проверяем наличие недопустимых символов
	if (!preg_match("/^[a-z0-9_-]{3,20}$/is",$login_in) || !preg_match("/^[a-z0-9_-]{3,20}$/is",$_SERVER['PHP_AUTH_PW']))
	{
		echo 'Доступ запрещен - неверный логин / пароль';
		exit;
	}
	else
	{		
		// Проверка логина / пароля
		if ($login != $login_in || $settings->c1_psw != $psw_in){echo 'Доступ запрещен'; exit;}
	}
}
// ======= / HTTP АВТОРИЗАЦИЯ  / ================================================================
?>