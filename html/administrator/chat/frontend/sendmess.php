<?php
define("AUTH", TRUE);
include("../../../config.php");

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
if(isset($_POST["mess"])){$mess = $_POST["mess"];} else {$mess = '';}
$ip = $_SERVER['REMOTE_ADDR'];

if ($ip != '' && $mess != '')
{
	// Удаляем переносы
	$arr_replace = array("\r\n", "\n", "\r");
	$mess = str_replace($arr_replace, ' ', $mess);

	// Удаляем теги
	$mess = strip_tags($mess);

	// Вставляем полученные данные в таблицу
	$stmt_mess = $db->prepare("INSERT INTO chat_mess SET ip = :ip, user = '0', mess = :mess, data = '".date("Y-m-d H:i:s")."' ");
	$stmt_mess->execute(array('ip'=>$ip, 'mess'=>$mess));

	// Записываем что отправлено новое сообщение
	$stmt_user = $db->prepare("UPDATE chat_user SET new = '1', newmess = '1' WHERE ip = :ip");
	$stmt_user->execute(array('ip'=>$ip));
}
?>