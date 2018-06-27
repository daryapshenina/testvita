<?php
// DAN 2012
// Публикуем пункт меню $admin_d2 = $d[2];

defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($admin_d4);

// Обновляем данные в таблице "menu"
$stmt = $db->prepare("UPDATE `menu` SET `pub` = '1' WHERE `id_com` = :id_com AND `component` = 'page';");
$stmt->execute(array(
	'id_com' => $id_com
));

Header ("Location: /admin/site/"); exit;

?>