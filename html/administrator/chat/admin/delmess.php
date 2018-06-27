<?php
define("AUTH", TRUE);
include("../../../config.php");

// увы, но на время перехода на pdo - будем использовать 2 драйвера... зато весело!!!
$db_host = $host;
$db_name = $dbname;
$db_user = $user;
$db_password = $passwd;

$db_dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$db_opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"	
);
$db = new PDO($db_dsn, $db_user, $db_password, $db_opt);

$ip = $_POST['ip'];

$stmt_delete_user = $db->prepare("DELETE FROM chat_user WHERE ip = :ip");
$stmt_delete_user->execute(array('ip' => $ip));

$stmt_delete_mess = $db->prepare("DELETE FROM chat_mess WHERE ip = :ip");
$stmt_delete_mess->execute(array('ip' => $ip));

?>