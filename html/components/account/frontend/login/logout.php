<?php
defined('AUTH') or die('Restricted access');

include_once($root."/classes/Auth.php");
Auth::logOut();

function component()
{
	global $domain;

	echo '<h1 class="registration_h1">Выход из аккаунта произведён успешно.</h1>';
}

?>