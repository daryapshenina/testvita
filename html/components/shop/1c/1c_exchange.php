<?php
// Импорт данных из 1С
define("AUTH", TRUE);

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// ------- Настройки -------

if(@set_time_limit(90)){$time_limit = 50;} else {$time_limit = 20;}	// устанавливаем время работы скрипта и время завершения 50 секунд (минута - 10 секунд) или 20 секунд.
$login = 'admin';							// логин авторизации
$cookie_name = 'import_1c';					// имя куки
$cookie_value = 'import_1c_value';			// значение куки
$zip = 'zip=no';							// поддержка zip
$file_limit = 'file_limit=8000000';			// максимальный размер файла, закачиваемый на сервер
$dir = "/components/shop/1c/import_1c/";	// директория для файлов 1с

$time_start = time();
$root = $_SERVER['DOCUMENT_ROOT'];

include($root.'/config.php');
include($root.'/lib/lib.php');

// ******* ПРОВЕРКА ********
// $str = $_SERVER["QUERY_STRING"]."\n";
// $file = $root.$dir.'QUERY_STRING.txt';
// $f = fopen($file,"a+");
// fwrite($f,$str);
// fclose($f);
// ******** / проверка ********

if(isset($_COOKIE[$cookie_name])){$cookie_value_input = $_COOKIE[$cookie_name];}
if(isset($_GET["type"])){$type = htmlspecialchars($_GET["type"]);} else{$type = '';}
if(isset($_GET["mode"])){$mode = htmlspecialchars($_GET["mode"]);} else{$mode = '';}
if(isset($_GET["filename"])){$filename = htmlspecialchars($_GET["filename"]);} else{$filename = '';}

$file_post_data = file_get_contents("php://input");



// === PDO ===============================================================================
$db_host = $host;
$db_name = $dbname;
$db_user = $user;
$db_password = $passwd;

$db_dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$db_opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
$db = new PDO($db_dsn, $db_user, $db_password, $db_opt);

// Настройки интернет -магазина
include_once($root."/components/shop/classes/classShopSettings.php");

$s = new classShopSettings;
$settings = unserialize($s->settings);


// Авторизация
include('auth.php');

// Предварительная установка
if ($type == "catalog" && $mode == "checkauth"){include('checkauth.php');}

// Инициализация
if ($type == "catalog" && $mode == "init"){echo $zip."\n".$file_limit;}

// Загрузка файлов
if ($type == "catalog" && $mode == "file"){include('files.php');}

// Обработка файла import.xml
if ($type == "catalog" && $mode == "import" && ($filename == 'import.xml' || $filename == 'import0_1.xml')){include('import_xml.php');}

// Обработка файла offers.xml
if ($type == "catalog" && $mode == "import" && ($filename == 'offers.xml' || $filename == 'offers0_1.xml')){include('offers_xml.php');}

?>