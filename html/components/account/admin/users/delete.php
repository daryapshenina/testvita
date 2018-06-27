<?php
defined("AUTH") or die("Restricted access");
include_once($root."/lib/remove_directory.php");

$floor_id = 1000 * floor($d[5]/1000); // тысячная папка
$dir = $root.'/files/account/'.$floor_id.'/'.$d[5];
remove_directory($dir);

$stmt_profile = $db->prepare("DELETE FROM com_account_profile WHERE user_id = :user_id LIMIT 1");
$stmt_profile->execute(array('user_id' => $d[5]));

$stmt_user = $db->prepare("DELETE FROM com_account_users WHERE id = :id");
$stmt_user->execute(array('id' => $d[5]));

Header("Location: /admin/com/account/users/all");

exit;

?>