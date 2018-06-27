<?php
// Выводит модули сайта в центре (компонентом)
defined('AUTH') or die('Restricted access');

$id = intval($d[3]);

$stmt_update = $db->prepare("UPDATE modules SET pub = '1' WHERE id = :id");
$stmt_update->execute(array('id' => $id));

Header ("Location: /admin/modules"); 
exit;
?>