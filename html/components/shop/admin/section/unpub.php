<?php
defined('AUTH') or die('Restricted access');

// Обновляем данные в таблице "menu"
$stmt = $db->prepare("UPDATE menu SET pub = '0' WHERE id_com = :id_com AND component = 'shop' AND p1 = 'section'");
$stmt->execute(array('id_com' => $SITE->d[5]));

// Обновляем данные в таблице "com_shop_section"
$stmt_update = $db->prepare("UPDATE com_shop_section SET pub = '0' WHERE id = :id");
$stmt_update->execute(array('id' => $SITE->d[5]));

Header ("Location: /admin"); exit;

?>