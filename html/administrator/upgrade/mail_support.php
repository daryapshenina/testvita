<?php
// DAN 2010
// Настройки сайта
defined('AUTH') or die('Restricted access');

	
// === Отправка на почту ==================================================
function mailsupport($error)
{
	global $domain;
	
	$data = date( d.'.'.m.'.'.Y );	
	
	$ip=GetUserIP();
	
	$to1 = 'info@5za.ru'; 
	$to2 = 'site@5za.ru';

	// SUBJECT тема
	$subject = "Ошибка с сайта www.".$domain." ";
	
	$site_code = '=?UTF-8?B?'.base64_encode($domain).'?=';
	
	// Для отправки HTML-почты Content-type. 
	$headers  = "MIME-Version: 1.0 \r\n";
	$headers .= "Content-type: text/html; charset=UTF-8 \r\n";
	$headers .= "From: www.".$site_code." <".$to2."> \r\n"; 
	
	// IP + сообщение 
	$error = '<p>IP '.$ip.'</p>'.$error;
	
	// = MAIL = 
	mail($to1, '=?UTF-8?B?'.base64_encode($subject).'?=', $error, $headers);
}

?>