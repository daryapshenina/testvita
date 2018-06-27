<?php
define("AUTH", TRUE);
session_start();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include_once $_SERVER['DOCUMENT_ROOT']."/lib/lib.php";
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/Settings.php';

$theme = trim(strip_tags($_POST['theme']));
$email = trim(strip_tags($_POST['email']));
$question = trim(strip_tags($_POST['question']));

// Капча
if(isset($_SESSION['code']) && isset($_POST['code']))
{
	if($_SESSION['code'] !== intval($_POST['code']) || empty($_SESSION['code']))
	{
		echo '
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="./style.css" type="text/css" />
<meta http-equiv="refresh" content="2;URL='.$_SERVER['HTTP_REFERER'].'" />
</head>
	<body>
	<div align="center"><font color="#FF0000">Цифры с картинки указаны не верно</font></div>
	</body>
</html>';
		exit();
	}
}

function addField($_name, $_value)
{
	return
	'<tr>
		<td class="form_text_gray" align="right" >'.$_name.': </td>
		<td width="10"></td>
		<td><b>'.$_value.'</b></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td width="10"></td>
		<td>&nbsp;</td>
	</tr>
	';
}

// ПРОВЕРЯЕМ НЕ ПУСТЫЕ ЛИ ПОЛЯ
if($email == '')
{
	echo '
	<!DOCTYPE html>
	<html>
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="./style.css" type="text/css" />
	</head>
		<body>
		<div class="form" style="padding:10px;">
		<h1 style="margin:5px 0px 0px 0px;">Поле Email было пропущено. Заполните его!</div>
		</body>
	</html>';
}
else
{
	$form_body = "";
	$form_body .= addField("Тема", $theme);
	$form_body .= addField("Email", $email);
	$form_body .= addField("Вопрос", $question);

	$mail_message = '
		<div class="title">
			<div class="title-1"></div>
			<div class="title-2"><h1 style="text-align:center;">Ваше сообщение отправлено!</h1></div>
			<div class="title-3"></div>
		</div>
		<br/>
		<table border="0" width="100%" style="border-collapse:collapse;"  align="center" cellpadding="0">
		'.$form_body.'
		</table>
	';

	// === Отправка на почту ==================================================

	classMail::send(Settings::instance()->getValue('email'),
			'no-replay@'.$_SERVER['SERVER_NAME'],
			"Вопрос с сайта www.".$_SERVER['SERVER_NAME'],
			$mail_message,
			null);


	// === Запись в Лиды ==================================================

	$text = '<div>
				<table border="0" width="100%" style="border-collapse:collapse;"  align="center" cellpadding="0">
					'.$form_body.'
				</table>
			</div>';

	$stmt_leads = $db->prepare("INSERT INTO com_leads SET title = :title, text = :text, type = 'Вопрос с сайта', date = :date, status = '0'");
	$stmt_leads->execute(array('title' => $email, 'text' => $text, 'date' => date("Y-m-d H:i:s")));


	echo '
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="./style.css" type="text/css" />
<meta http-equiv="refresh" content="2;URL='.$_SERVER['HTTP_REFERER'].'" />
</head>
	<body>
	<div class="form" style="padding:10px;">
	'.$mail_message.'
	</div>
	</body>
</html>';

}
?>
