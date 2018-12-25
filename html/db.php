<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT']."/config.php";

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

/*if(!isset($db))
	$db = new PDO($db_dsn, $db_user, $db_password, $db_opt);*/
if(!isset($db))
    try{
        $db = new PDO($db_dsn, $db_user, $db_password, $db_opt);
    }
    catch(Exception $e){
        echo $e->getMessage();
        die;
    }