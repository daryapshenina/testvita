<?php

$cto_name = strip_tags($_POST["cto_name"]);
$cto_phone = strip_tags($_POST["cto_phone"]);
$site = 'www'.$_SERVER['SERVER_NAME'];

if ($cto_name != '' and $cto_phone != '')
{

	include("../../../config.php");

	// === MySQL ======================================================
	$conn = mysql_connect ($host, $user, $passwd) or die ("Соединение с MySQL не установлено!"); 
	mysql_select_db($dbname) OR die ("Соединение с базой данных не установлено");
	mysql_query('SET CHARACTER SET utf8');

	$getemail_db = mysql_query("SELECT * FROM `com_form` WHERE `name` = 'email_recipient'");
	$getemail = mysql_fetch_array($getemail_db);

	// Если не пусто то отправляем
	$data = date( d.'.'.m.'.'.Y.' в '.H.':'.i );	

	$mail_message = '<p><b>Имя:</b> '.$cto_name.'</p><p><b>Телефон:</b> '.$cto_phone.'</p>';

	// SUBJECT тема
	$subject = "Заказ звонка с сайта www.".$site." ";
	
	$site_code = '=?UTF-8?B?'.base64_encode($site).'?=';
	
	/* Для отправки HTML-почты Content-type. */
	$headers  = "MIME-Version: 1.0 \r\n";
	$headers .= "Content-type: text/html; charset=utf-8 \r\n";
	$headers .= "From: www.".$site_code." <".$getemail[content]."> \r\n";
	
	$mail_message_sent = $mail_message.'<p><b>Дата:</b> '.$data.'</p>';
	$form_email_recipient = $getemail[content];
	
	mail($form_email_recipient, '=?utf-8?B?'.base64_encode($subject).'?=', $mail_message_sent, $headers);
}

// После всех действий перекидываем
Header ("Location: /"); exit;
?>
