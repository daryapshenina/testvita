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

// Чьи сообщения получаем?
$ip = $_POST['ip'];

$stmt_user = $db->exec("UPDATE chat_user SET data = '".date("Y-m-d H:i:s")."' WHERE ip = 'admin'"); // Записываем дату админа в бд

$stmt_ip = $db->prepare("UPDATE chat_user SET new = '0' WHERE ip = :ip"); // Записываем для этого id что все сообщения прочитаны 
$stmt_ip->execute(array('ip' => $ip));

$stmt_chat_user = $db->query("SELECT * FROM chat_user WHERE ip != 'admin' ORDER BY data DESC");

$chat_out = '';

$i = 0;
while ($m = $stmt_chat_user->fetch())
{
	if ($ip == $m['ip'])
	{
		// Если это активный айпи то
		$chat_out .= '<div id="chat_class_ip_active" class="chat_class_ip_but" title="Происходит переписка">'.$m['ip'].'</div>';
	}
	elseif ($m['new'] == 1)
	{
		// Если у этого пользователя есть новые сообщения то
		$chat_out .= '<div class="chat_class_ip_new chat_class_ip_but" id="chat_id_ip_'.$i.'" title="Есть новые сообщения">'.$m['ip'].'</div>';
	}
	elseif ((time() - strtotime($m['data'])) < 30)
	{
		// Если еще не прошло 30 секунд, значит этот пользователь на связи
		$chat_out .= '<div class="chat_class_ip chat_class_ip_but" id="chat_id_ip_'.$i.'" title="Пользователь онлайн">'.$m['ip'].'</div>';
	}
	else
	{
		// Если этот пользователь давно не появлялся то
		$chat_out .= '<div class="chat_class_ip_lost chat_class_ip_but" id="chat_id_ip_'.$i.'" title="Пользователь вышел">'.$m['ip'].'</div>';
	}

	// Смотрим какие звуки нужны и записываем что они были уже воспроизведены
	if ($m['newuser'] == 1)
	{
		$smt_chat_user_update = $db->prepare("UPDATE chat_user SET newuser = '0' WHERE ip = :ip");
		$smt_chat_user_update->execute(array('ip' => $m[ip]));

		$newuser = 1;
	}
	if ($m['newmess'] == 1)
	{
		$smt_chat_mess_update = $db->prepare("UPDATE chat_user SET newmess = '0' WHERE ip = :ip");
		$smt_chat_mess_update->execute(array('ip' => $m[ip]));
		$newmess = 1;
	}
	$i++;
}

// Включаем нужные звуки и записываем что они были уже воспроизведены
if ($newuser == 1)
{
	$chat_out .= '<audio autoplay="autoplay" src="/administrator/chat/admin/sound/newuser.mp3" type="audio/mp3"></audio>';
}

if ($newmess == 1 && $newuser != 1)
{
	$chat_out .= '<audio autoplay="autoplay" src="/administrator/chat/admin/sound/newmess.mp3" type="audio/mp3"></audio>';
}

// === Подключаемся к бд и берем сообщения ===============
$stmt_mess = $db->prepare("SELECT * FROM chat_mess WHERE ip = :ip ORDER BY data DESC");
$stmt_mess->execute(array('ip' => $ip));


$mess_out = '';
// Выводим сообщения
while ($m = $stmt_mess->fetch())
{
	if ($m['user'] == 0)
	{
		// Если пользователь
		$mess_out .= '<b>Посетитель:</b> '.$m['mess'].'<br />';
	}
	else
	{
		// Если администратор
		$mess_out .= '<b>Вы:</b> '.$m['mess'].'<br />';
	}
}

if ($ip == 'undefined' || $ip == ''){$mess_out .= 'Начните чат выбрав пользователя в списке слева';}
else{$mess_out .= 'Вы: Добрый день чем могу помочь?';}




echo '
<div id="get_info_listuser">'.$chat_out.'</div>
<div id="get_info_mess">'.$mess_out.'</div>
';

?>