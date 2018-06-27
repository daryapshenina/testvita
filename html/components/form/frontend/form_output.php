<?php
defined('AUTH') or die('Restricted access');

$page_id = intval($d[1]);

$stmt = $db->query("SELECT id FROM menu WHERE component = 'form' AND pub = '1' LIMIT 1");

// проверяем, есть ли выборка. если нет, то направляем на страницу ошибки.
if ($stmt->rowCount() == 0)
{
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}


// выводим содержимое страницы
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




// ####### Функция вывода компонента ############################################################
function component()
{
	global $root, $site, $tabmenu, $page_id, $tipmenu, $page_sql, $page_title, $form_description_content, $form_theme_content, $form_theme_pub, $form_message_content, $form_message_pub, $form_fio_content, $form_fio_pub, $form_contact_content, $form_contact_pub, $form_email_content, $form_email_pub, $form_tel_content, $form_tel_pub, $form_file_pub, $form_file_content, $frontend_edit, $form_captcha_pub;

	include_once $root.'/classes/Auth.php';
	$time_encode = Auth::encode(time());

	$form_theme = '';
	$form_message = '';
	$form_fio = '';
	$form_contact = '';
	$form_email = '';
	$form_tel = '';
	$form_file = '';
	$form_captcha = '';


	// Тема
	if ($form_theme_pub == "1")
	{
		$form_theme =
		'<tr>
			<td><input type="text" name="theme" size="40" value="" placeholder="'.$form_theme_content.'" class="input" required></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// Сообщение
	if ($form_message_pub == "1")
	{
		$form_message =
		'<tr>
			<td><textarea id="com_form_message" name="message" rows="7" cols="32" placeholder="'.$form_message_content.'" class="input" required ></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// ФИО
	if ($form_fio_pub == "1")
	{
		$form_fio =
		'<tr>
			<td><input type="text" name="fio" size="40" value="" placeholder="'.$form_fio_content.'" class="input" required></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// Контактные данные
	if ($form_contact_pub == "1")
	{
		$form_contact =
		'<tr>
			<td><input type="text" name="contact" size="40" value="" placeholder="'.$form_contact_content.'" class="input" required></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// Email
	if ($form_email_pub == "1")
	{
		$form_email =
		'<tr>
			<td><input type="email" name="email" size="40" value="" placeholder="'.$form_email_content.'" class="input" required></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// Телефон контакта
	if ($form_tel_pub == "1")
	{
		$form_tel =
		'<tr>
			<td><input type="text" name="tel" size="40" value="" placeholder="'.$form_tel_content.'" class="input" title="Только цифры" required pattern="[0-9 \-\(\)\+]{5,20}" ></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// Файл
	if ($form_file_pub == "1")
	{
		$form_file =
		'<tr>
			<td colspan="2"><div class="input_1">'.$form_file_content.'</div></td>
		</tr>
		<tr>
			<td><input type="file" name="file" size="40" class="input"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	// Капча
	if ($form_captcha_pub == "1")
	{
		$form_captcha = '
		<tr>
			<td>
				<table border="0" width="100%" style="border-collapse: collapse" cellpadding="0">
					<tr>
						<td style="width:100px;"><img class="captcha_img" src="/administrator/captcha/pic.php" /></td>
						<td>&nbsp;</td>
						<td style="vertical-align:middle;"><input type="text" name="code" size="3" value="" placeholder="Введите цифры" class="input" title="4 цифры" required pattern="[0-9]{4}" ></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		';
	}

	if(Settings::instance()->getValue('personal_information') == 1)
	{
		$personal_information = '<tr><td>&nbsp;</td></tr><tr><td><input required checked title="Вы должны дать согласие перед отправкой" type="checkbox">Я согласен на <a href="/personal-information" target="_blank">обработку персональных данных</a></td></tr>';
	}
	else{$personal_information = '';}

	$out = '
		<h1 class="title">'.$page_title.'</h1>
		<div>'.$form_description_content.'</div>
		<form class="com_form" method="POST" action="/form/mail/'.$page_id.'/'.$tipmenu.'" enctype="multipart/form-data">
		<div class="com_form_table_main">
			<table border="0" width="100%" style="border-collapse: collapse" cellpadding="0">
			'.$form_theme
			 .$form_message
			 .$form_fio
			 .$form_contact
			 .$form_email
			 .$form_tel
			 .$form_file
			 .$form_captcha.
			'
			<tr>
				<td><input type="submit" value="'.LANG_FORM_SEND.'" name="button" class="button_green button_small"></td>
			</tr>
			'.$personal_information.'
			</table>
			<input class="form_lastname" type="text" name="lastname" value="">
			<input type="hidden" name="m" value="'.$time_encode.'">
		</div>
		</form>
	';

	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_form" data-id="'.$page_id.'">'.$out.'</div>';}
	else {echo $out;}

} // конец функции



?>