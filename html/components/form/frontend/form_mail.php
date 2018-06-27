<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST['lastname']) && !empty($_POST['lastname'])){exit;}
include_once $root.'/classes/Auth.php';
$time_decode = Auth::decode($_POST['m']);
$time_delta = time() - $time_decode;
if($time_delta < 10 || $time_delta > 10000){exit;}// Проверка на спам - интервал между загрузкой и отправкой - в пределах 10 - 10 000 секунд.

include_once $root.'/classes/Settings.php';

$page_id = intval($d[2]);

if(isset($_POST["theme"])){$form_theme = trim(strip_tags($_POST["theme"]));}else{$form_theme = '';}
if(isset($_POST["message"])){$form_message = trim(strip_tags($_POST["message"]));}else{$form_message = '';}
if(isset($_POST["fio"])){$form_fio = trim(strip_tags($_POST["fio"]));}else{$form_fio = '';}
if(isset($_POST["contact"])){$form_contact = trim(strip_tags($_POST["contact"]));}else{$form_contact = '';}
if(isset($_POST["email"])){$form_email = trim(strip_tags($_POST["email"]));}else{$form_email = '';}
if(isset($_POST["tel"])){$form_tel = trim(strip_tags($_POST["tel"]));}else{$form_tel = '';}
if(isset($_FILES['file'])){$form_file = $_FILES['file'];}else{$form_file = '';}

// ID активного меню
$active_menu = $page_id;

$stmt = $db->query("SELECT * FROM com_form");

while($form = $stmt->fetch())
{
	if ($form['id'] == "1"){$page_title = $form['name']; $form_description_content = $form['content'];}
	if ($form['name'] == "theme"){$form_theme_content = $form['content']; $form_theme_pub = $form['pub'];}
	if ($form['name'] == "message"){$form_message_content = $form['content']; $form_message_pub = $form['pub'];}
	if ($form['name'] == "fio"){$form_fio_content = $form['content']; $form_fio_pub = $form['pub'];}
	if ($form['name'] == "contact"){$form_contact_content = $form['content']; $form_contact_pub = $form['pub'];}
	if ($form['name'] == "email"){$form_email_content = $form['content']; $form_email_pub = $form['pub'];}
	if ($form['name'] == "tel"){$form_tel_content = $form['content']; $form_tel_pub = $form['pub'];}
	if ($form['name'] == "file"){$form_file_content = $form['content']; $form_file_pub = $form['pub'];}
	if ($form['name'] == "captcha"){$form_captcha_pub = $form['pub'];}
}

// Проверить введенные данные из капчи
if($form_captcha_pub == 1)
{
	// = Проверка кода на картинке ====================================================================
	$cpt = 0;

	if(!empty($_SESSION['code']) && isset($_POST['code']) && intval($_SESSION['code']) == intval($_POST['code']))
	{
		$cpt = 1;
	}
}

// Проверяем файл если он передан
if(!isset($form_file) && $form_file != "")
{
	if($form_file['size'] > 1048576)
	{
		die ('<div align="center"><font color="#FF0000">'.LANG_FORM_NOT_SUBMITTED_FILES_WEIGHT.'</font></div>');
	}
	if(!classValidation::checkTypeFileAll($file['name']))
	{
		die ('<div align="center"><font color="#FF0000">'.LANG_FORM_NOT_SUBMITTED_FILES_TYPE.'</font></div>');
	}
}

// ПРОВЕРЯЕМ НЕ ПУСТЫЕ ЛИ ПОЛЯ
if($pub_captcha == 1 && $cpt == 0)
{
	die ('<div align="center"><font color="#FF0000">'.LANG_FORM_CODE_FROM_IMAGE_ERROR.'</font></div>');
}
else
{
	$utm_arr = $utm->get();
	$utm->delete();

	$head->addCode('<meta name="robots" content="noindex">');


	// ####### Функция вывода компонента ############################################################
	function component()
	{
		global $db, $domain, $site, $page_id, $page_sql,  $ip, $utm_arr, $form_theme, $form_message, $form_fio, $form_contact, $form_email, $form_tel, $page_title, $form_description_content, $form_theme_content, $form_theme_pub, $form_message_content, $form_message_pub, $form_fio_content, $form_fio_pub, $form_contact_content, $form_contact_pub, $form_email_content, $form_email_pub, $form_tel_content, $form_tel_pub, $form_file;

		// ======= ПРОВЕРКА ВВОДИМЫХ ЗНАЧЕНИЙ ========================================================
		if(
			($form_email_pub && !classValidation::checkEmail($form_email)) ||
			($form_tel_pub && !classValidation::checkPhone($form_tel)) ||
			($form_theme_pub && !classValidation::checkText($form_theme)) ||
			($form_message_pub && !classValidation::checkText($form_message)) ||
			($form_fio_pub && !classValidation::checkText($form_fio)) ||
			($form_contact_pub && !classValidation::checkText($form_contact))
		)
		{
			$err = 1;
			die ('<div align="center"><font color="#FF0000">'.LANG_FORM_NOT_SUBMITTED_TEXT_ERROR.'</font></div>');
		}
		// ======= / проверка вводимых значений / ====================================================

		// Тема
		if ($form_theme_pub == "1")
		{
			$form_theme_f =
			'<tr>
				<td class="form_text_gray" width="200" align="right">'.$form_theme_content.'</td>
				<td width="10"></td>
				<td><b>'.$form_theme.'</b></td>
			</tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td width="10"></td>
				<td>&nbsp;</td>
			</tr>
			';
		}
		else{$form_theme_f = '';}

		// Сообщение
		if ($form_message_pub == "1")
		{
			$form_message_f =
			'<tr>
				<td class="form_text_gray" width="200" align="right">'.$form_message_content.'</td>
				<td width="10"></td>
				<td><b>'.$form_message.'</b></td>
			</tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td width="10"></td>
				<td>&nbsp;</td>
			</tr>
			';
		}
		else {$form_message_f = '';}

		// ФИО
		if ($form_fio_pub == "1")
		{
			$form_fio_f =
			'<tr>
				<td class="form_text_gray" width="200" align="right" >'.$form_fio_content.'</td>
				<td width="10"></td>
				<td><b>'.$form_fio.'</b></td>
			</tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td width="10"></td>
				<td>&nbsp;</td>
			</tr>
			';
		}
	else {$form_fio_f = '';}

		// Контактные данные
		if ($form_contact_pub == "1")
		{
			$form_contact_f =
			'<tr>
				<td class="form_text_gray" width="200" align="right">'.$form_contact_content.'</td>
				<td width="10"></td>
				<td><b>'.$form_contact.'</b></td>
			</tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td width="10"></td>
				<td>&nbsp;</td>
			</tr>
			';
		}
		else {$form_contact_f = '';}

		// Email
		if ($form_email_pub == "1")
		{
			$form_email_f =
			'<tr>
				<td class="form_text_gray" width="200" align="right">'.$form_email_content.'</td>
				<td width="10"></td>
				<td><b>'.$form_email.'</b></td>
			</tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td width="10"></td>
				<td>&nbsp;</td>
			</tr>
			';
		}
		else {$form_email_f = '';}

		// Телефон контакта
		if ($form_tel_pub == "1")
		{
			$form_tel_f =
			'<tr>
				<td class="form_text_gray" width="200" align="right">'.$form_tel_content.'</td>
				<td width="10"></td>
				<td><b>'.$form_tel.'</b></td>
			</tr>
			<tr>
				<td width="200">&nbsp;</td>
				<td width="10"></td>
				<td>&nbsp;</td>
			</tr>
			';
		}
		else {$form_tel_f = '';}

		$mail_message = '
			<div class="title">
				<div class="title-1"></div>
				<div class="title-2"><h1>'.LANG_FORM_YOUR_MESSAGE_SEND.'</h1></div>
				<div class="title-3"></div>
			</div>
			<br/>
			<table border="0" width="100%" style="border-collapse: collapse" cellpadding="0">
			'.$form_theme_f
			 .$form_message_f
			 .$form_fio_f
			 .$form_contact_f
			 .$form_email_f
			 .$form_tel_f.
			'
			</table>
			<br/>
			<br/>
			'.LANG_FORM_YOUR_MESSAGE_SEND.'
		';


		if($form_email == ''){$form_email = 'no-replay';}

		// === Вносим данные в компонент Leads ====================================
		$leads_t = '';
		if($form_theme != ''){$leads_t .= $form_theme.'. ';}
		if($form_message != ''){$leads_t .= $form_message.'. ';}
		if($form_fio != ''){$leads_t .= $form_fio.'. ';}
		if($form_contact != ''){$leads_t .= $form_contact.'. ';}
		if($form_email != ''){$leads_t .= $form_email.'. ';}
		if($form_tel != ''){$leads_t .= $form_tel.'. ';}

		$leads_title = mb_substr($leads_t, 0, 50).'...';
		$leads_message =
		'<table border="0" width="100%" style="border-collapse: collapse" cellpadding="0">
			'.$form_theme_f
			 .$form_message_f
			 .$form_fio_f
			 .$form_contact_f
			 .$form_email_f
			 .$form_tel_f.
			'
		</table>';

		$leads_message .= '<div>&nbsp;</div>';
		$leads_message .= '<div>Отправлено со страницы: <a href="'.$_SERVER['HTTP_REFERER'].'" target="_blank">'.$_SERVER['HTTP_REFERER'].'</a></div>';		

		$utm_source = $utm_arr['utm_source'];
		$utm_medium = $utm_arr['utm_medium'];
		$utm_campaign = $utm_arr['utm_campaign'];
		$utm_content = $utm_arr['utm_content'];
		$utm_term = $utm_arr['utm_term'];
		$utm_date = $utm_arr['utm_date'];
		if(empty($utm_counter) || $utm_counter < 1){$utm_counter == 1;}
		$utm_counter = $utm_arr['utm_counter'];

		$stmt_leads = $db->prepare("
		INSERT INTO com_leads
		SET title = :title,
		text = :text,
		type = '".LANG_FORM_MESSAGE_FROM_SITE."',
		date = :date,
		utm_source = :utm_source,
		utm_medium = :utm_medium,
		utm_campaign = :utm_campaign,
		utm_content = :utm_content,
		utm_term = :utm_term,
		utm_date = :utm_date,
		utm_counter = :utm_counter,
		status = '0'
		");

		$stmt_leads->execute(array(
		'title' => $leads_title,
		'text' => $leads_message,
		'date' => date("Y-m-d H:i:s"),
		'utm_source' => $utm_source,
		'utm_medium' => $utm_medium,
		'utm_campaign' => $utm_campaign,
		'utm_content' => $utm_content,
		'utm_term' => $utm_term,
		'utm_date' => $utm_date,
		'utm_counter' => $utm_counter
		));

		// === Отправка на почту ==================================================
		$email = Settings::instance()->getValue('email');
		$from = 'no-replay@'.$site;
		$subject = LANG_FORM_MESSAGE_FROM_SITE." www.".$domain;

		classMail::send($email, $from, $subject, $mail_message, $form_file);

		echo $mail_message;


	} // конец функции component
}
?>