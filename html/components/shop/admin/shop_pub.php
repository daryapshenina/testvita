<?php
defined('AUTH') or die('Restricted access');

// Обновляем данные в таблице "menu"
$stmt = $db->prepare("UPDATE menu SET pub = '1' WHERE id_com = :id_com AND component = 'shop' AND main = '1'");
$stmt->execute(array('id_com' => $SITE->d[4]));

Header ("Location: /admin/site/"); exit;		
?>