<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/leads/admin/tmp/style.css');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_leads";
		var cm_leds = [
			["admin/com/leads/view", "contextmenu_add", "Посмотреть"],
			["admin/com/leads/old", "contextmenu_copy", "Просмотренный"],			
			["admin/com/leads/mark", "contextmenu_unblock", "Пометить"],
			["admin/com/leads/remove_mark", "contextmenu_edit", "Снять пометку"],			
			["admin/com/leads/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, cm_leds);
	});
</script>
');

function a_com()
{
	global $root, $db, $domain;
	
	$stmt_leads = $db->query("SELECT id, title, type, date, utm_source, utm_medium, utm_campaign, utm_content, utm_term, utm_date, TIMEDIFF(date,utm_date) as dif_date, utm_counter, status FROM com_leads ORDER BY id desc");
	
	$out = '';
	$utm = '';
	while($m = $stmt_leads->fetch())
	{
		if($m['status'] == '0'){$class = 'class="new"';}
		elseif($m['status'] == '2'){$class = 'class="mark"';}
		else{$class = '';}

		$utm = '';		
		if($m['utm_source'] != '')$utm .= '<div class="utm_str"><span class="utm_name">Источник:</span>'.$m['utm_source'].'</div>';
		if($m['utm_medium'] != '')$utm .= '<div class="utm_str"><span class="utm_name">Тип трафика:</span>'.$m['utm_medium'].'</div>';
		if($m['utm_campaign'] != '')$utm .= '<div class="utm_str"><span class="utm_name">Рекламная кампания:</span>'.$m['utm_campaign'].'</div>';
		if($m['utm_content'] != '')$utm .= '<div class="utm_str"><span class="utm_name">Информация:</span>'.$m['utm_content'].'</div>';
		if($m['utm_term'] != '')$utm .= '<div class="utm_str"><span class="utm_name">Ключевая фраза:</span>'.$m['utm_term'].'</div>';
		if($m['utm_date'] != '0000-00-00 00:00:00' && $m['dif_date'] != '')
		{
			$minutes = $m['dif_date'];
			$utm .= '<div class="utm_str"><span class="utm_name">Время на раздумывание:</span>'.$minutes.'</div>';
		}
		if($m['utm_counter'] != '')$utm .= '<div class="utm_str"><span class="utm_name">Просмотров страниц:</span>'.$m['utm_counter'].'</div>';
		
		$out .= '
			<tr '.$class.' >
				<td>'.$m['id'].'</td>
				<td class="contextmenu_leads" data-id="'.$m['id'].'"><a href="/admin/com/leads/view/'.$m['id'].'">'.$m['title'].'</a>'.$utm.'</td>
				<td>'.$m['type'].'</td>
				<td>'.$m['date'].'</td>
			</tr>		
		';
	}
	
	if($stmt_leads->rowCount() > 0)
	{
		echo'
		<h1 class="leads_title_mp">Лиды:</h1>
		<a class="delete_all" href="/admin/com/leads/delete/all">Удалить всё</a>
		<table class="utm_table">
			<tr>
				<th style="width:50px">№</th>
				<th>Лиды</th>
				<th style="width:150px">Тип</th>
				<th style="width:150px">Дата</th>
			</tr>
			'.$out.'
		</table>
		';			
	}
	else
	{
		echo'<h1>Нет новых лидов</h1>';			
	}
	

}
?>