<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

if ($modules_pub == "1")
{

	$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'tel' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 3");			
	while($m = mysql_fetch_array($num)):
		$module_content = $m['content'];
		echo $module_content;		
	endwhile;

}

?>