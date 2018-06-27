<?php
// DAN обновление - июль 2014
// отсылает администратору магазина запрос по товару
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';
include_once $root.'/classes/Settings.php';

$utm_arr = $utm->get();
$utm->delete();



// ####### Вывод товара ###############################################################
function component()
{
	global $db, $root, $domain, $SITE, $item_id, $utm_arr;

	$item_title = strip_tags($_POST['item_title']);
	$item_id = intval($_POST['item_id']);
	$question = strip_tags($_POST['question']);
	$user_email = strip_tags($_POST['email']);
	$captcha = strip_tags($_POST['captcha']);

	$err = '';

	if (!preg_match("/^[^@]+@[^@]+\.[a-zа-я]{2,10}$/ui",$user_email)){$err .= '<div class="mes_red">'.LANG_INVALID_EMAIL_2.'</div>';}

	// captcha
	if(isset($_SESSION['code']) && isset($captcha))
	{
		if($_SESSION['code'] != $captcha || $captcha == 0){$err .= '<div class="mes_red">'.LANG_CAPTHA_CODE_ERROR.'</div>';}
	}
	else
	{
		$err .= '<div class="mes_red">'.LANG_CAPTHA_CODE_ERROR.'</div>';
	}

	if($err == '')
	{
		$message = '
		<table class="shop_question_tab">
		<tr>
			<td class="shop_question_tab_1">'.LANG_ITEM.':</td>
			<td class="shop_question_tab_2"><a target="_blank" href="http://'.$domain.'/shop/item/'.$item_id.'">'.$item_title.'</a></td>
		</tr>
		<tr>
			<td class="shop_question_tab_1">'.LANG_YOUR_QUASTION.':</td>
			<td class="shop_question_tab_2">'.$question.'</td>
		</tr>
		<tr>
			<td class="shop_question_tab_1">'.LANG_YOUR_EMAIL.':</td>
			<td class="shop_question_tab_2">'.$user_email.'</td>
		</tr>
		</table>';

		echo '<h1 class="shop-item-title-2">'.LANG_YOUR_QUASTION_SENT.'</h1>';
		echo $message;



		// === Отправка на почту ==================================================
		$data = date("Y-m-d H:i:s");

		// SUBJECT тема
		$subject = "Вопрос с сайта www.".$SITE->domainIdn." ";

		// сообщение
		$message .=	'
		<style type="text/css">
		.shop_question_tab  {
			width				:100%;
			border-collapse		:collapse;
			border-spacing		:0px;
		}

		.shop_question_tab  td {
			height				:30px;
			vertical-align		:middle;
			border-style		:solid;
			border-width		:1px;
			border-color		:#cccccc;
		}

		.shop_question_tab_1 {
			padding-left		:10px;
			padding-right		:10px;
			font-weight			:bold;
			width				:100px;
		}

		.shop_question_tab_2 {
			padding-left		:10px;
			padding-right		:10px;
		}
		</style>
		';

		// === Лиды ======================================================================================

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
		type = '".LANG_QUASTION_ABOUT_PRODUCT."',
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
		'title' => $item_title,
		'text' => $message,
		'date' => date("Y-m-d H:i:s"),
		'utm_source' => $utm_source,
		'utm_medium' => $utm_medium,
		'utm_campaign' => $utm_campaign,
		'utm_content' => $utm_content,
		'utm_term' => $utm_term,
		'utm_date' => $utm_date,
		'utm_counter' => $utm_counter
		));

		classMail::send(Settings::instance()->getValue('email'),
			'no-replay@'.$SITE->domainIdn,
			"Вопрос с сайта www.".$SITE->domainIdn,
			$message);
	}
	else
	{
		echo $err;
	}

} // конец функции component

?>
