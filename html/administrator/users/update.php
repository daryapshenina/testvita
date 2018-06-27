<?php
// Обновить пароль
defined('AUTH') or die('Restricted access');

$psw = $_POST["psw1"];
$psw = 'dan'.$psw;

$pass = md5($psw);

$stmt_user = $db->prepare("UPDATE users SET psw = :psw WHERE id = '2';");
$stmt_user->execute(array('psw' => $pass));

Header ("Location: /admin/"); exit;
?>