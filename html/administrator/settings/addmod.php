<?php
// Добавить редактируемый модуль
defined('AUTH') or die('Restricted access');

$stmt_mod_insert = $db->query("INSERT INTO modules SET title = 'Редактируемый модуль', module = 'editor', module_csssuf = '', pub = '0', titlepub = '1', enabled = '1', description = 'Редактируемый модуль', content = '', p1 = '', p2 = '', p3 = '', p4 = '', p5 = '', p6 = '', p7 = '', p8 = '', p9 ='', p10 = '', block = '', ordering = '0' ");

function a_com()
{
	global $site;
		
	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/tools.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Добавлени нового редактируемого модуля:</div>
		<div>&nbsp;</div>
		
		<div style="padding:10px;"><h3>Новый редактируемый модуль добавлен</h1></div>
		
	';
} // конец функции компонента
?>