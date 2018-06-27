<?php
// Модуль авторизации
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';
include_once($root."/classes/Auth.php");

$u = Auth::check();

if($m['p1'] == '1'){$url = 'shop/account';} // shop
else{$url = $m['p2'];}

// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = ' edit_mode';
	$edit_data = 'data-type="mod_authorization" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}

if(empty($u)) // Нет авторизации пользователя
{
	if ($m['pub'] == "1")
	{
		$account_settings = accountSettings::getInstance();

		if($account_settings->registration_allow) $reg = '<div onclick="DAN_modal(260, 280, \'\', mod_authorization_reg)">Регистрация</div>';
			else $reg = '';

		echo '<script type="text/javascript">var mod_authorization_enter = \''.Auth::formLogin("Вход", $url).'\';var mod_authorization_reg = \''.Auth::formReg("Регистрация", $url).'\';</script>';
		echo '<div '.$edit_data.' class="mod_authorization_container'.$edit_class.'"><div class="mod_authorization_img"></div><div class="mod_authorization_text"><div onclick="DAN_modal(280, 280, \'\', mod_authorization_enter)">Вход</div>'.$reg.'</div></div>';
	}
}
else // Авторизация пользователя
{

	$user = Auth::getUser();
	echo '<div '.$edit_data.' class="mod_authorization_container'.$edit_class.'"><div class="mod_authorization_img"></div><div class="mod_authorization_text"><div><a class="mod_authorization_link" href="/'.$url.'">'.$user['email'].'</a></div><div><a class="mod_authorization_link" href="/account/logout">Выход</a></div></div></div>';
}

?>