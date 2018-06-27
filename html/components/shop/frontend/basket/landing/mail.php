<?php
defined('AUTH') or die('Restricted access');

include_once $root.'/classes/Settings.php';

if($_SERVER["REQUEST_METHOD"] == 'POST')
{

	if(!isset($_POST['lastname']) || $_POST['lastname'] != ''){exit;}
	if(!isset($_POST['dt']) || $_POST['dt'] == ''){exit;}
	if(!isset($_POST['name'])){exit;}
	if(!isset($_POST['phone'])){exit;}

	$time_decode = Auth::decode($_POST['dt']);
	$time_delta = time() - $time_decode;
	if($time_delta < 3 || $time_delta > 600){exit;}

	$phone = $_POST['phone'];
	$name = $_POST['name'];
	$item_title = strip_tags($_POST['item_title']);
	$item_id = intval($_POST['item_id']);

	if(strlen($phone) < 3){echo "<h1>Телефон указан не верно!</h1>";exit;}

	$message = '
	<table class="shop_question_tab">
	<tr>
		<td class="shop_question_tab_1">Заказ:</td>
		<td class="shop_question_tab_2"><a target="_blank" href="http://'.$domain.'/shop/item/'.$item_id.'">'.$item_title.'</a></td>
	</tr>
	</table>';

	// === Лиды ======================================================================================

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
	type = 'Заявка',
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


	// === Отправка на почту ==================================================
	$data = date("Y-m-d H:i:s");

	// SUBJECT тема
	$subject = "Вопрос с сайта www.".$domain." ";

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


	classMail::send(Settings::instance()->getValue('email'),
		'no-replay@'.$domain,
		"Заказ с сайта www.".$domain,
		$message);
		
	Header ('Location: /shop/landing_order'); exit;		
}
else
{
	
}

	

// ####### Вывод товара ###############################################################
function component()
{
	global $root, $domain, $message;
	echo '<h1 class="shop-item-title-2">Ваш заказ отправлен</h1>';
}

?>