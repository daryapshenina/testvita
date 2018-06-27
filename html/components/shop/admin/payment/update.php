<?php
defined('AUTH') or die('Restricted access');
	
if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop"); exit;}

if(isset($_POST["payment_cash"])) $shopSettings->payment_method_cash = intval($_POST["payment_cash"]);
	else $shopSettings->payment_method_cash = 0;

if(isset($_POST["payment_prepayment"])) $shopSettings->payment_method_prepayment = intval($_POST["payment_prepayment"]);
	else $shopSettings->payment_method_prepayment = 0;

if(isset($_POST["payment_сash_on_delivery"])) $shopSettings->payment_method_сash_on_delivery = intval($_POST["payment_сash_on_delivery"]);
	else $shopSettings->payment_method_сash_on_delivery = 0;

if(isset($_POST["payment_yandex"])) $shopSettings->payment_method_yandex = $_POST["payment_yandex_type"]; 
	else $shopSettings->payment_method_yandex = 0;

if(isset($_POST["yandex_money_id"])) $shopSettings->yandex_money_id = intval($_POST["yandex_money_id"]);
	else $shopSettings->yandex_money_id = '';

if(isset($_POST["yandex_secret"])) $shopSettings->yandex_secret = strip_tags($_POST["yandex_secret"]);
	else $shopSettings->yandex_secret = '';

if(isset($_POST["yandex_cashbox_test"]) && $_POST["yandex_cashbox_test"] == 1) $shopSettings->yandex_cashbox_test = 1;
	else $shopSettings->yandex_cashbox_test = 0;

if(isset($_POST["nds"])) $shopSettings->nds = intval($_POST["nds"]);
	else $shopSettings->nds = 1;

$shopSettings->yandex_cashbox_shop_id = intval($_POST["yandex_cashbox_shop_id"]);

$shopSettings->yandex_cashbox_scid = intval($_POST["yandex_cashbox_scid"]);

$shopSettings->yandex_cashbox_password = strip_tags($_POST["yandex_cashbox_password"]);

if(isset($_POST["payment_sberbank"])) $shopSettings->payment_method_sberbank = intval($_POST["payment_sberbank"]); 
	else $shopSettings->payment_method_sberbank = 0;

if(isset($_POST["sberbank_test"])) $shopSettings->sberbank_test = 1;
	else $shopSettings->sberbank_test = 0;

if(isset($_POST["sberbank_login"])) $shopSettings->sberbank_login = trim(strip_tags($_POST["sberbank_login"]));
	else $shopSettings->sberbank_login = 0;

if(isset($_POST["sberbank_password"])) $shopSettings->sberbank_password = trim(strip_tags($_POST["sberbank_password"]));
	else $shopSettings->sberbank_password = 0;

$s_serialize = serialize($shopSettings);

$stmt = $db->prepare("UPDATE com_shop_settings SET parametr = :s_serialize WHERE name = 'settings'");
$stmt->execute(array('s_serialize' => $s_serialize));


Header ("Location: /admin/com/shop"); exit;

?>