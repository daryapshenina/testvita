<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST['lastname']) && !empty($_POST['lastname'])){exit;}
include_once $root.'/classes/Auth.php';
$time_decode = Auth::decode($_POST['m']);
$time_delta = time() - $time_decode;
if($time_delta < 10 || $time_delta > 10000){exit;}// Проверка на спам - интервал между загрузкой и отправкой - в пределах 10 - 10 000 секунд.

$utm_arr = $utm->get();
$utm->delete();

function component()
{
	global $root, $db, $domain, $utm_arr;

	$out = '';
	$err = '';

	$title_out = '';
	$text = '';

	if(isset($_POST['id'])){$id = intval($_POST['id']);}else{$id = 0;}
	if(isset($_POST['field_1'])){$f_1 = trim(strip_tags($_POST['field_1']));}else{$f_1 = '';}
	if(isset($_POST['field_2'])){$f_2 = trim(strip_tags($_POST['field_2']));}else{$f_2 = '';}
	if(isset($_POST['field_3'])){$f_3 = trim(strip_tags($_POST['field_3']));}else{$f_3 = '';}
	if(isset($_FILES['file'])){$form_file = $_FILES['file'];}else{$form_file = '';}
	if(isset($_POST['code'])){$code = intval($_POST['code']);}else{$code = 0;}

	// ======= ПРОВЕРКА ВВОДИМЫХ ЗНАЧЕНИЙ ========================================================
	if(
		($id == 0) ||
		(!classValidation::checkPhone($f_1)) ||
		(!classValidation::checkText($f_2)) ||
		(!classValidation::checkText($f_3))
	)
	{
		$err = 1;
		die ('<div align="center"><font color="#FF0000">'.LANG_FORM_NOT_SUBMITTED_TEXT_ERROR.'</font></div>');
	}

	$stmt = $db->prepare("SELECT * FROM modules WHERE id = :id");
	$stmt->execute(array('id' => $id));
	$m = $stmt->fetch();

	if($m['titlepub'] == 1) $title_out = '<div style="font-size:18px">'.$m['title'].'</div>';
	if($m['content'] == 1) $text = '<div style="padding:20px 0px;">'.$m['content_2'].'</div>';

	if ($m['p1'] == 1)
	{
		$field_1 =
		'<tr>
			<td style="width:140px;"><b>'.$m['p2'].':</b> </td><td>'.$f_1.'</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		';
	}

	if ($m['p3'] == 1)
	{
		$field_2 =
		'<tr>
			<td style="width:140px;"><b>'.$m['p4'].':</b> </td><td>'.$f_2.'</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		';
	}

	if ($m['p5'] == 1)
	{
		$field_3 =
		'<tr>
			<td style="width:140px;"><b>'.$m['p6'].':</b> </td><td>'.$f_3.'</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		';
	}

	if ($m['p7'] == 1 && $form_file != '')
	{
		$file =
		'<tr>
			<td colspan="2"><b>Прикреплён файл</b></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		';
	}
	else {$file = '';}

	if ($m['p8'] == 1)
	{
		if(empty($_SESSION['code']) || !isset($_POST['code']) || intval($_SESSION['code']) != intval($_POST['code']))
		{
			die ('<div align="center"><font color="#FF0000">'.LANG_FORM_CODE_FROM_IMAGE_ERROR.'</font></div>');
		}
	}

	// Проверяем файл если он передан
	if($form_file != '' && $form_file['name'] != '')
	{
		if($form_file['size'] > 1048576)
		{
			die ('<div align="center"><font color="#FF0000">'.LANG_FORM_NOT_SUBMITTED_FILES_WEIGHT.'</font></div>');
		}
		if(!classValidation::checkTypeFileAll($form_file['name']))
		{
			die ('<div align="center"><font color="#FF0000">'.LANG_FORM_NOT_SUBMITTED_FILES_TYPE.'</font></div>');
		}
	}

	$out .=
	'<h1>Ваше сообщение отправлено!</h1>
	<div>
		<div style="max-width:500px;margin:0 auto;font-size:13px;background:#f0f0f0;padding:20px;border-radius:2px;box-shadow:0px 0px 1px rgba(0, 0, 0, 0.4);">
			'.$title_out.'
			'.$text.'
			<table style="width:100%;max-width:460px;border-collapse:collapse;border:0px;border-spacing:0px;">
				'.$field_1.'
				'.$field_2.'
				'.$field_3.'
				'.$file.'
			</table>
		</div>
	</div>
	';

	// === Отправка на почту ==================================================
	$email = Settings::instance()->getValue('email');
	$from = 'no-replay@'.$domain;
	$subject = LANG_FORM_MESSAGE_FROM_SITE." www.".$domain;

	classMail::send($email, $from, $subject, $out, $form_file);

	echo $out;


	// === Вносим данные в компонент Leads ====================================

	$out .= '<div>&nbsp;</div>';
	$out .= '<div>Отправлено со страницы: <a href="'.$_SERVER['HTTP_REFERER'].'" target="_blank">'.$_SERVER['HTTP_REFERER'].'</a></div>';

	$utm_source = $utm_arr['utm_source'];
	$utm_medium = $utm_arr['utm_medium'];
	$utm_campaign = $utm_arr['utm_campaign'];
	$utm_content = $utm_arr['utm_content'];
	$utm_term = $utm_arr['utm_term'];
	$utm_date = $utm_arr['utm_date'];
	$utm_counter = $utm_arr['utm_counter'];
	if(empty($utm_counter) || $utm_counter < 1){$utm_counter == 1;}

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
	'title' => LANG_FORM_MESSAGE_FROM_SITE,
	'text' => $out,
	'date' => date("Y-m-d H:i:s"),
	'utm_source' => $utm_source,
	'utm_medium' => $utm_medium,
	'utm_campaign' => $utm_campaign,
	'utm_content' => $utm_content,
	'utm_term' => $utm_term,
	'utm_date' => $utm_date,
	'utm_counter' => $utm_counter
	));


}

?>
