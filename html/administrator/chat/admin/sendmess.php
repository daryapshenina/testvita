<?php
define("AUTH", TRUE);
include("../../../config.php");
include("../../../lib/lib.php");

// === MySQL ======================================================
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

// Получаем из формы данные
$mess = $_POST["mess"];
$ip = $_POST['ip'];

// Удаляем переносы
$arr_replace = array("\r\n", "\n", "\r");
$mess = str_replace($arr_replace, ' ', $mess);

// Удаляем теги
$mess = strip_tags($mess);

$stmt_insert = $db->prepare("INSERT INTO chat_mess SET ip = :ip, user = '1', mess = :mess, data = '".date("Y-m-d H:i:s")."'"); // Вставляем полученные данные в таблицу
$stmt_insert->execute(array('ip' => $ip, 'mess' => $mess));

$stmt_update = $db->prepare("UPDATE chat_user SET newmfu = '1' WHERE ip = :ip"); // Записываем для пользователя что есть новое сообщения
$stmt_update->execute(array('ip' => $ip));

?>
