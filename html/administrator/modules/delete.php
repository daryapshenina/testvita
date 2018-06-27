<?php
// Выводит модули сайта в центре (компонентом)
defined('AUTH') or die('Restricted access');

$id = intval($d[3]);

$stmt_delete = $db->prepare('DELETE FROM modules WHERE id = :id');
$stmt_delete->execute(array('id' => $id));

if(isset($d[4]) && $d[4] == 'frontend')
{
	Header ("Location: ".$_SERVER['HTTP_REFERER']); 	
}
else
{
	Header ("Location: /admin/modules"); 
}

exit;
?>