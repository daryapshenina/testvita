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

// Узнаем айпи пользователя
$ip = $_SERVER['REMOTE_ADDR'];

if ($ip != '')
{
	// ==== Записываем пользователя в бд ========================================
	// Смотрим есть ли пользователь
	$stmt_user = $db->prepare("SELECT * FROM chat_user WHERE ip = :ip LIMIT 1");
	$stmt_user->execute(array('ip'=>$ip));

	// Смотрим есть ли новые сообщения для пользователя
	$user_select = $stmt_user->fetch();
	if ($user_select['newmfu'] == 1)
	{
		echo '<audio autoplay="autoplay" src="/administrator/chat/admin/sound/newmess.mp3" type="audio/mp3"></audio>';
	}

	if ($stmt_user->rowCount() == 0)
	{
		// Если пользователя нет, то записываем
		$stmt_user_insert = $db->prepare("INSERT INTO chat_user SET ip = :ip, data = '".date("Y-m-d H:i:s")."', newmess = '0', newuser = '1', newmfu = '0'");
		$stmt_user_insert->execute(array('ip' => $ip));
	}
	else
	{
		// Если есть, то просто обновляем дату и подт. просмотр сообщения
		$stmt_user_update = $db->prepare("UPDATE chat_user SET data = '".date("Y-m-d H:i:s")."', newmfu = '0' WHERE ip = :ip");
		$stmt_user_update->execute(array('ip'=>$ip));
	}
	// ====== зап. пользователя ===============================


	// === Подключаемся к бд и берем сообщения ===============
	$stmt_mess = $db->prepare("SELECT * FROM chat_mess WHERE ip = :ip ORDER BY data DESC");
	$stmt_mess->execute(array('ip'=>$ip));

	// Выводим сообщения
	while ($getmess = $stmt_mess->fetch())
	{
		if ($getmess['user'] == 0)
		{
			// Если пользователь
			echo '<div class="chat_mess_user"><div class="chat_mess_p">'.$getmess['mess'].'</div></div>';
		}
		else
		{
			// Если администратор
			echo '<div class="chat_mess_admin"><div class="chat_mess_p">'.$getmess['mess'].'</div></div>';
		}
		
		// разделитель сообщений
		echo '<div class="chat_mess_raz"></div>';
	}

	echo '<div class="chat_mess_admin"><div class="chat_mess_p">Добрый день чем могу помочь?</div></div>';
	// ========= получение сообщений =====================
}
?>