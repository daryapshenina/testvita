<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/leads/admin/tmp/style.css');

function a_com()
{
	global $root, $db, $domain, $d;
	
	$id = $d[4];
	
	$stmt_leads = $db->prepare("SELECT *, TIMEDIFF(date,utm_date) as dif_date FROM com_leads WHERE id = :id LIMIT 1");
	$stmt_leads->execute(array('id' => $id));
	
	$lead = $stmt_leads->fetch();
	
	if($lead['status'] == '0')
	{
		$stmt_update = $db->prepare("UPDATE com_leads SET status = '1' WHERE id = :id");
		$stmt_update->execute(array('id' => $id));
	}
	
	if($lead['type'] == 'Заказ из интернет-магазина')$lead['type'] = '<a href="/admin/com/shop/orders">'.$lead['type'].'</a>';
	
	$utm = '';		
	if($lead['utm_source'] != '')$utm .= '<div><b>UTM метки:</b></div><div><span class="utm_name">Источник:</span>'.$lead['utm_source'].'</div>';
	if($lead['utm_medium'] != '')$utm .= '<div><span class="utm_name">Тип трафика:</span>'.$lead['utm_medium'].'</div>';
	if($lead['utm_campaign'] != '')$utm .= '<div><span class="utm_name">Рекламная кампания:</span>'.$lead['utm_campaign'].'</div>';
	if($lead['utm_content'] != '')$utm .= '<div><span class="utm_name">Информация:</span>'.$lead['utm_content'].'</div>';
	if($lead['utm_term'] != '')$utm .= '<div><span class="utm_name">Ключевая фраза:</span>'.$lead['utm_term'].'</div>';
	if($lead['utm_date'] != '0000-00-00 00:00:00' && $lead['dif_date'] != '')
	{
		$minutes = $lead['dif_date'];
		$utm .= '<div><span class="utm_name">Время на раздумывание:</span>'.$minutes.'</div>';
	}
	if($lead['utm_counter'] != '')$utm .= '<div><span class="utm_name">Просмотров страниц:</span>'.$lead['utm_counter'].'</div>';
	
	echo'
	<div id="main">
		<h1>Лид:</h1>
		<div class="m_b_20">'.$lead['date'].'</div>
		<div class="m_b_20">'.$lead['type'].'</div>
		<div class="m_b_20">'.$lead['text'].'</div>
		<div class="m_b_40">'.$utm.'</div>		
		<div><a href="/admin/com/leads" class="greenbutton">Назад</a> <a href="/admin/com/leads/delete/'.$lead['id'].'" class="redbutton">Удалить</a></div>
	</div>
	';	
}
?>