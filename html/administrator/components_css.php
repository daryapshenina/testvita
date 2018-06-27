<?php
// DAN 2010
// подключает стили для компонентов.

defined('AUTH') or die('Restricted access');

function a_com_css()
{ 
	global $site;

	// подключаем компоненты
	$compsql = mysql_query("SELECT * FROM `components` WHERE `enabled` = '1' OR `enabled` = '3' ") or die ("Невозможно сделать выборку из таблицы - 1");	
	
	while($m = mysql_fetch_array($compsql)):
		$components_com = $m['components'];
		echo '<link rel="stylesheet" href="http://'.$site.'/components/'.$components_com.'/admin/tmp/admin_com.css" type="text/css" />';
	endwhile;
}

?>