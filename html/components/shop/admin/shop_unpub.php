<?php
defined('AUTH') or die('Restricted access');

$stmt = $db->prepare("UPDATE menu SET pub = '0' WHERE id_com = :id_com AND component = 'shop' AND main = '1'");
$stmt->execute(array('id_com' => $SITE->d[4]));

Header ("Location: /admin"); exit;
?>