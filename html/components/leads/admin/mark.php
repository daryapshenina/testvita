<?php
defined('AUTH') or die('Restricted access');

$id = intval($d[4]);

$stmt_update = $db->prepare("UPDATE com_leads SET status = '2' WHERE id = :id");
$stmt_update->execute(array('id' => $id));

Header ('Location: /admin/com/leads');
exit;
?>