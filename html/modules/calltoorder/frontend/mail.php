<?php
define("AUTH", TRUE);

$root = $_SERVER['DOCUMENT_ROOT'];

include_once $root.'/config.php';
include_once $root.'/db.php';
include_once $root.'/classes/UTM.php';
include_once __DIR__.'/lang/'.LANG.'.php';
include_once $root.'/classes/Settings.php';
include_once $root.'/lib/lib.php';

$domain = $site;

$cto_name = strip_tags($_POST["cto_name"]);
$cto_phone = strip_tags($_POST["cto_phone"]);

if($cto_name != '' and $cto_phone != '')
{
	/* Сообщение */

	$mail_message = '
		<p><b>'.LANG_M_CALLTOORDER_NAME.':</b> '.$cto_name.'</p>
		<p><b>'.LANG_M_CALLTOORDER_PHONE.':</b> '.$cto_phone.'</p>
	';


	/* Запись в Лиды */

	$text = '<div><b>'.LANG_M_CALLTOORDER_NAME.': </b>'.$cto_name.'</div><div><b>'.LANG_M_CALLTOORDER_PHONE.':</b> '.$cto_phone.'</div>';

	
	$utm = new UTM;
	$utm_arr = $utm->get();
	$utm->delete();

	$utm_source = $utm_arr['utm_source'];
	$utm_medium = $utm_arr['utm_medium'];
	$utm_campaign = $utm_arr['utm_campaign'];
	$utm_content = $utm_arr['utm_content'];
	$utm_term = $utm_arr['utm_term'];
	$utm_date = $utm_arr['utm_date'];
	$utm_counter = $utm_arr['utm_counter'];

	$stmt_leads = $db->prepare("
	INSERT INTO com_leads 
	SET title = :title, 
	text = :text, 
	type = '".LANG_M_CALLTOORDER_CALLBACK."', 
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
	'title' => $cto_phone, 
	'text' => $text, 
	'date' => date("Y-m-d H:i:s"),
	'utm_source' => $utm_source,
	'utm_medium' => $utm_medium,
	'utm_campaign' => $utm_campaign,
	'utm_content' => $utm_content,
	'utm_term' => $utm_term,
	'utm_date' => $utm_date,
	'utm_counter' => $utm_counter
	));


	/* Отправка */
	$email = Settings::instance()->getValue('email');
	$from = 'no-replay@'.$domain;
	$subject = LANG_M_CALLTOORDER_ORDERING_CALL." ".$domain;

	classMail::send($email, $from, $subject, $mail_message, null);
}

// После всех действий перекидываем
Header ("Location: /"); exit;
?>
