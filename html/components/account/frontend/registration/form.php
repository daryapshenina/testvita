<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';
include_once($root."/classes/Auth.php");

$account_settings = accountSettings::getInstance();
if(!$account_settings->registration_allow){Header ("Location: /"); exit;}

$head->addFile('/lib/css/account/form.css');
$head->addFile('/lib/css/font-awesome/css/font-awesome.min.css');

$title = 'Регистрация на сайте '.$domain;
$description = '';

function component()
{
	global $domain;
	echo '<h1 class="title">Регистрация на сайте '.$domain.'</h1>';
	echo '<div style="height:50px;"></div>';
	echo '<div style="margin:0 auto; width:260px; height:235px; border-radius:5px; background: #E9E9E9; padding:20px;">';
	echo Auth::formReg("Регистрация", "/profile");
	echo '</div>';
}

?>