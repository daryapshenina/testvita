<?php
// Выводит список модулей слева

defined('AUTH') or die('Restricted access');

function a_modules_chat ()
{
	global $domain, $section_id;
	
	echo '
		<a id="chat_online" class="mod_left_link" href="/admin/chat">Открыть чат</a>
		<a id="chat_settings" class="mod_left_link" href="/admin/chat/settings">Настройки чата</a>
	';
}

function a_modules_com()
{ 
	global $db, $domain, $section_id;

	// вывод компонентов
	$stmt_com = $db->query("SELECT * FROM components WHERE enabled = '1' OR  enabled = '3'"); // enabled '1' - опубликовать справа и в меню   '2' - нигде не публиковать   '3' - публиковать только справа

	while($c = $stmt_com->fetch())
	{
		$components_id = $c['id'];
		$components_com = $c['components'];		
		$components_title = $c['title'];	
		$components_description = $c['description'];
		$components_enabled = $c['enabled'];
		
		echo '<a class="mod_left_link '.$components_com.'_ico" href="/admin/com/'.$components_com.'/">'.$components_title.'</a>';		
	}
}

function a_modules_mod()
{ 
	global $db, $domain, $section_id;

	echo '<a id="modulesall" class="mod_left_link" href="/admin/modules/">Все модули</a>';

	// вывод модулей
	$stmt_mod = $db->query("SELECT * FROM modules WHERE enabled = '1' ");
		
	while($m = $stmt_mod->fetch())
	{
		$modules_id = $m['id'];
		$modules_title = $m['title'];
		$modules_module = $m['module'];	
		$modules_pub = $m['pub'];	
		$modules_enabled = $m['enabled'];
		$modules_description = $m['description'];
		
		echo '<a class="'.$modules_module.'_ico mod_left_link" href="/admin/modules/'.$modules_module.'/'.$modules_id.'">'.$modules_title.'</a>';		
	}	
}

?>