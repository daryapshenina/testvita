<?php
defined('AUTH') or die('Restricted access');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/account/classes/accountSettings.php');

if(isset($_POST['registration_allow'])){$registration_allow = intval($_POST['registration_allow']);}else{$registration_allow = 0;}
if(isset($_POST['shop_allow'])){$shop_allow = intval($_POST['shop_allow']);}else{$shop_allow = 0;}
if(isset($_POST['ads_allow'])){$ads_allow = intval($_POST['ads_allow']);}else{$ads_allow = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'


// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/account"); exit;}

$account_settings = new accountSettings;
$account_settings->registration_allow = $registration_allow;
$account_settings->shop_allow = $shop_allow;
$account_settings->ads_allow = $ads_allow;

$a_s_serialize = serialize($account_settings);

$stmt_updfate = $db->prepare("UPDATE com_account_settings SET settings = :settings WHERE id = 1");
$stmt_updfate->execute(array('settings' => $a_s_serialize));


if($bt_save == 'Сохранить'){Header ("Location: /admin/com/account"); exit;}
else {Header ("Location: /admin/com/account/settings"); exit;}

?>