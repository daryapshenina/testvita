<?php
defined('AUTH') or die('Restricted access');

$stmt = $db->prepare("UPDATE com_account_users SET status = '1' WHERE id = :id");
$stmt->execute(array('id' => $d[5]));


Header("Location: /admin/com/account/users/all");
exit;

?>