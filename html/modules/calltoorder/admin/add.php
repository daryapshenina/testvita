<?php
defined('AUTH') or die('Restricted access');

// Проверяем, есть ли уже этот модуль
$query_module = $db->query("SELECT id FROM modules WHERE module = 'calltoorder'");

if($query_module->rowCount() > 0)
{
	function a_com()
	{ 
		global $domain, $query_module;	
		echo '
			<div class="container">
				<h1>Модуль уже подключен:</h1>
				<div><a href="/admin/modules/calltoorder/'.$query_module->fetchColumn().'">Заказать звонок</a></div>
			</div>
		';
	}
}
else
{
	$stmt_insert = $db->query("
		INSERT INTO modules SET
		title = 'Заказать звонок',
		module = 'calltoorder',
		module_csssuf = '',
		pub = '1',
		titlepub = '1',
		enabled = '1',
		description = 'Модуль выводит поле для ввода номера телефона и имени',
		content = '',
		content_2 = '',
		p1 = '1',
		p2 = '60',
		p3 = '50',
		p4 = '50',
		p5 = '#00df2d',
		p6 = '',
		p7 = '',
		p8 = '',
		p9 = '',
		p10 = '',
		block = '',
		ordering = '1'
	");

	$id = $db->lastInsertId();

	Header ("Location: /admin/modules/calltoorder/".$id); 
	exit;	
}

?>