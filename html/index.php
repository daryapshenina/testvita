<?php
define("AUTH", TRUE);

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

include_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core.php';

$SITE = new Core;

$SITE->domain = $domain = $site; // в новой версии используем только $domain. $site - оставляем пока для совместимости;
if(!isset($SITE->domainIdn)) $SITE->domain;
if(!isset($domain_idn)){$domain_idn = $domain;}


session_start();

//date_default_timezone_set('Europe/Moscow');
//$str = $_SERVER["QUERY_STRING"];
$str = $_SERVER['REQUEST_URI'];
$root = $_SERVER['DOCUMENT_ROOT'];

include_once $SITE->root.'/lib/lib.php';
include_once $SITE->root.'/classes/UTM.php';
include_once $SITE->root.'/classes/classHead.php';
include_once $SITE->root.'/classes/Auth.php';
include_once $SITE->root.'/classes/Settings.php';

// === MySQL ======================================================
@$conn = mysql_connect ($host, $user, $passwd) or die ("Соединение с MySQL не установлено!");
mysql_select_db($dbname) OR die ("Соединение с базой данных не установлено");
mysql_query('SET CHARACTER SET utf8');

$head = new classHead; // подключаем header
$utm = new UTM;

// === ЧПУ URL ====================================================

$qs = zapros($str); // фильтрация входных данных
$qs_arr = explode('?', $qs); // отделяем адрес от GET переменных
$qs_arr[0] = preg_replace("/\/$/", "", $qs_arr[0]);

// $GET
if(isset($qs_arr[1]))
{
	parse_str($qs_arr[1], $GET);
	$utm->set_cookie($GET); // ловушка UTM-меток
}

if($qs_arr[0] == 'page/1')
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /");
	exit();
}

// Получаем массив ЧПУ
$stmt_url = $db->query("SELECT * FROM url");
if($stmt_url->rowCount() > 0)
{
	while($u = $stmt_url->fetch())
	{
		if($u['sef'] != '') $url_arr[$u['url']] = $u['sef'];// если ЧПУ URL не пустой -> массив ЧПУ

		if($qs_arr[0] != '' && $qs_arr[0] == $u['sef'] &&  $u['sef'] != '') $qs = $u['url'];	// заменяем ЧПУ на нормальное представление для внутреннего использования
	}
}

$qs_arr = explode('?', $qs);
$d = explode('/', $qs_arr[0]);

if(!isset($d[0])){$d[0] = '';}
if(!isset($d[1])){$d[1] = '';}
if(!isset($d[2])){$d[2] = '';}
if(!isset($d[3])){$d[3] = '';}
if(!isset($d[4])){$d[4] = '';}
if(!isset($d[5])){$d[5] = '';}
if(!isset($d[6])){$d[6] = '';}

if(!($d[0] == 'shop' && $d[1] == 'basket'))$utm->counter();

if ($d[0] != "admin")
{
	// подключаем класс меню
	include("classes/Menu.php");
	$menu = new classMenu;

	$frontend_edit = 0;	// frontend редактирование

	if (isset($_SESSION['s5za_e']) && $_SESSION['s5za_e'] != '' && $d[0] != 'admin')
	{
		$frontend_edit = 1;
		include("administrator/frontend_edit/edit.php");
	}
	else
	{
		function frontend_edit(){return;}
	}
}

// === Устанавливаем флаг $allowed ================================
// какое значение ещё может показываться (устанавливаем флаг "$allowed = 1") в url без вызова ошибки 404
if(strpos($qs, $allowed_words)===false) {$allowed = 0;} else {$allowed = 1;}


// Проверяем подключен ли чат
$stmt_chat_settings = $db->query("SELECT parametr FROM chat_settings WHERE name = 'included'");
$cs = $stmt_chat_settings->fetch();

if ($cs['parametr'] == 1)
{
	// Проверяем онлайн ли администратор, тоесть еще не прошло 10 секунд
	$stmt_chat = $db->query("SELECT data FROM chat_user WHERE ip = 'admin'");

	$ca = $stmt_chat->fetch();
	if (20 > (time() - strtotime($ca['data'])))
	{
		// Если да то смотрим какие нужны стили
		$chat_theme = $db->query("SELECT `parametr` FROM `chat_settings` WHERE `name` = 'theme'");

		$ct = $chat_theme->fetch();

		if ($ct['parametr'] == 1) {$chat_theme = "green";}
		elseif ($ct['parametr'] == 2) {$chat_theme = "azure";}
		elseif ($ct['parametr'] == 3) {$chat_theme = "black";}
		elseif ($ct['parametr'] == 4) {$chat_theme = "blue";}
		elseif ($ct['parametr'] == 5) {$chat_theme = "orange";}
		elseif ($ct['parametr'] == 6) {$chat_theme = "purple";}
		elseif ($ct['parametr'] == 7) {$chat_theme = "red";}
		elseif ($ct['parametr'] == 8) {$chat_theme = "turquoise";}
		elseif ($ct['parametr'] == 9) {$chat_theme = "yellow";}
		else {$chat_theme = "";}

		$head->addFile('http://'.$domain.'/administrator/chat/frontend/chat.js');
		$head->addFile('http://'.$domain.'/administrator/chat/frontend/chat_style.css');
		$head->addFile('http://'.$domain.'/administrator/chat/frontend/img/'.$chat_theme.'/chat_theme_style.css');
	}
}

if(Settings::instance()->getValue('meta_tag') != 'ERROR SETTINGS NAME') $head->addCode(Settings::instance()->getValue('meta_tag')." \n");

// === Определяем компонент =======================================
// Если никакого компонента нет - подключаем компаонент страницы
$include_components = false;
if ($d[0] == "" )
{
	// $d[1] = 1; // id_com = 1
	include($root."/components/page/frontend/main.php"); // подключаем компонент страницы
	$include_components = true; // признак того, что компонент уже подключён (не ошибка 404)
}
elseif ($d[0] == "admin") // компонент ПАНЕЛЬ АДМИНИСТРИРОВАНИЯ
{
	$d[0] = 'admin'; // определяем компонент администратора
	$include_components = true;
}
else // Подключаем компоненты frontend если '$d[0]' или не 'admin'
{
	if($d[0] == 'sitemap.xml'){$d[0] = 'sitemap';}

	$components = $d[0];
	$stmt_com = $db->prepare("SELECT * FROM components WHERE components = :components AND enabled > 0 LIMIT 1");
	$stmt_com->execute(array('components' => $components));
	if($stmt_com-> rowCount() > 0)
	{
		include($root."/components/".$components."/frontend/main.php");
		$include_components = true;
	}
}


// если компонент не подключён (!=1) и хвост не является разрешённым - ошибка 404
if (!$include_components && $allowed == "0")
{
	header("HTTP/1.0 404 Not Found");
	include($root."/404.php");
	exit;
}


// ################################################################
// === Подключаем шаблон ==========================================
if ($d[0] != "admin")
{
	include("settings/settings.php"); // Подключаем настройки сайта
	include("modules/main.php"); // Подключаем модули
	include("tmp/tmpl.php"); // Подключаем шаблон
}
else {include("administrator/admin.php");}

// Сообщения об ошибках
function err_m ($err, $msg, $file, $line)
{
	global $admin_email;

	$err = $err.' '.$msg.' '.$file.' '.$line;

	$subj = '=?utf-8?B?'.base64_encode('Ошибка CMS').'?=';

	$headers  = "MIME-Version: 1.0 \r\n";
	$headers .= "Date: ".date("D, d M Y h:i:s O")."\r\n";
	$headers .= "Content-type: text/html; charset=utf-8 \r\n";
	$headers .= "From: info@5za.ru <info@5za.ru> \r\n";
	$headers .= "To: info@5za.ru \r\n";

	mail('info@5za.ru', $subj, $err, $headers);
}

?>