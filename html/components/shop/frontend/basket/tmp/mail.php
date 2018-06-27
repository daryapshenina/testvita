<?php
defined('AUTH') or die('Restricted access');
$data = date("Y-m-d H:i:s");

// --- Mетод оплаты ---
if($paymethod == 'cash') $paymethod_type = LANG_PAY_CASH; // Наличными при получении
if($paymethod == 'prepayment') $paymethod_type = LANG_PREPAYMENT; // Предоплата
if($paymethod == 'сash_on_delivery') $paymethod_type = LANG_PAY_IMPOSED; // Наложенным платежом
if($paymethod == 'yandex_money') $paymethod_type = LANG_YANDEX_MONEY; // Яндекс мани
if($paymethod == 'yandex_cashbox') $paymethod_type = LANG_YANDEX_CASHBOX;// Яндекс касса
if($paymethod == 'sberbank') $paymethod_type = 'Сбербанк'; // Сбербанк


if(isset($domain_idn)) $domain = $domain_idn;

$email_content = '
<h1>Заказ из интернет-магазина с сайта <a target="_blank" href="">'.$domain.'</a></h1>
<div>'.$data.'</div>
<div>&nbsp;</div>
<div>'.$items_email_out.'</div>
<div>&nbsp;</div>
<div>Сумма: <b>'.$summa_format.' '.$shopSettings->currency.'</b></div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>ФИО: &nbsp; <b>'.$fio.'</b></div>
<div>Телефон контакта: &nbsp; <b>'.$tel.'</b></div>
<div>Email: &nbsp; <b>'.$email.'</b></div>
<div>Адрес доставки: &nbsp; <b>'.$address.'</b></div>
<div>Комментарии: &nbsp; <b>'.$comments.'</b></div>
<div>Метод оплаты: &nbsp; <b>'.$paymethod_type.'</b></div>
';

?>