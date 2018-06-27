<?php
// DAN 2013
// Яндекс-Деньги - уведомление о входящем переводе
defined('AUTH') or die('Restricted access');

$notification_type = $_POST["notification_type"]; // Тип уведомления. Фиксированное значение p2p-incoming.
$operation_id = intval($_POST["operation_id"]);	// Идентификатор операции в истории счета получателя.
$amount = $_POST["amount"];	// Сумма операции.
$currency = $_POST["currency"]; // Код валюты счета пользователя. Всегда 643 (рубль РФ согласно ISO 4217).
$datetime = $_POST["datetime"]; // Дата и время совершения перевода.
$sender = $_POST["sender"];	// Номер счета отправителя перевода.
$codepro = $_POST["codepro"];	// Перевод защищен кодом протекции.
$label = $_POST["label"];	// Метка платежа. Если метки у платежа нет, параметр содержит пустую строку
$sha1_hash = $_POST["sha1_hash"];	// SHA-1 hash параметров уведомления.
$test_notification = $_POST["test_notification"]; // Флаг означает, что уведомление тестовое. По умолчанию параметр отсутствует.

// ####### Вывод содержимого #######################################################
function component()
{
	global $root, $site,  $yandex_secret, $notification_type, $to2, $operation_id, $amount, $currency, $datetime, $sender, $codepro, $label, $sha1_hash, $test_notification;

	// секрет
	$notification_secret = $yandex_secret;

	// вычисляем SHA1
	$sha1 = sha1($notification_type.'&'.$operation_id.'&'.$amount.'&'.$currency.'&'.$datetime.'&'.$sender.'&'.$codepro.'&'.$notification_secret.'&'.$label);

	if($sha1 == $sha1_hash)
	{
		$status = 1;
	}
	else
	{
		$status = 2;

		$err .= '<div><font color="#FF0000">Для вашего заказ <b>'.$label.'</b> указан неверный ключ. Позвоните менеджеру магазина для уточнения ситуации.</font></div>';
		$err_email .= '<div><font color="#FF0000">Для заказа № <b>'.$label.'</b> указан неверный ключ. Это означает, что либо вы не верно указали СЕКРЕТ в настройках интернет-магазина или возможно это фишинговая атака, цель которой получить товар бесплатно или по заниженной цене. Обязательно проверьте поступление денег на Яндекс-Кошелёк. Обязательно проверьте СЕКРЕТ выданный Яндексом и СЕКРЕТ, указанный в настройках магазина</font></div>';
	}


	// ********************
	// $test = $notification_type.' === '.$operation_id.' === '.$amount.' === '.$currency.' === '.$datetime.' === '.$sender.' === '.$codepro.' === '.$label.' === '.$sha1_hash.' === '.$test_notification.' ####### '.$sha1;
	// echo $test;
	// создаем файл в котором записываем значение init
	// $dir = "/components/shop/frontend/";
	// $file = $root.$dir.'testyandex.txt';

	// записываем файл
	// file_put_contents($file, $test);
	// ********************



	// -------- ЗАПРАШИВАЕМ ЗАКАЗ ИЗ БД --------
	// Находим количество товаров
	$orderssql = mysql_query("SELECT * FROM `com_shop_orders` WHERE `id` = '$label' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

	$resultorders = mysql_num_rows($orderssql);

	if($resultorders == 1)
	{
		while($m = mysql_fetch_array($orderssql)):
			$order_id = $m['id'];
			$order = $m['order'];
			$order_items = $m['items'];
			$order_summa = $m['sum'];
			$order_fio = $m['fio'];
			$order_tel = $m['tel'];
			$order_email_client = $m['email'];
			$order_address = $m['address'];
			$order_comments = $m['comments'];
			$order_payer = $m['payer'];
		endwhile;
	}
	else
	{
		$err .= '<div><font color="#FF0000">Ваш заказ № <b>'.$label.'</b> по неизвестной причине не найден в базе данных. Позвоните менеджеру магазина для уточнения ситуации.</font></div>';
		$err_email .= '<div><font color="#FF0000">Заказ № <b>'.$label.'</b> не найден в базе данных. Возможно это фишинговая атака, цель которой получить товар бесплатно или по заниженной цене. Обязательно проверьте поступление денег на Яндекс-Кошелёк.</font></div>';
	}
	// ------- /Запрашиваем заказ из БД / -------



	// ------- РАЗБИРАЕМ ЗАКАЗ НА ТОВАРЫ -------
	$order_items = trim($order_items);
	$items_arr = preg_split('[;]', $order_items);

	$items_sum = count($items_arr);

	for ($i=0; $i<$items_sum; $i++)
	{
		// Находим товары
		$item_sql = mysql_query("SELECT * FROM `com_shop_item` WHERE  `id` = '$items_arr[$i]' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");

		while($m = mysql_fetch_array($item_sql)):
			$item_id = $m['id'];
			$item_title = $m['title'];
			$item_etext_enabled = $m['etext_enabled'];
			$item_etext = $m['etext'];

			$order_key = sha1('dan'.$order_id.$order_date_payment);

			if($item_etext_enabled == 1 && $status == 1) // признак того, что товар электронный
			{
				$eitems_tmp .= '<hr/>';
				$eitems_tmp .= '<div>&nbsp;</div>';
				$eitems_tmp .= '<div><b>'.$item_title.'</b></div>';
				$eitems_tmp .= '<div>Ссылка на страницу получения товара: <a href="/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'">/shop/eitem/'.$item_id.'/'.$order_id.'/'.$order_key.'</a></div>';
				$eitems_tmp .= '<div>&nbsp;</div>';
			}
		endwhile;
	}
	// -----------------------------------------



	// Подключаем шаблон корзины
	include($root."/components/shop/frontend/tmp/shop_basket_yandexsuccess_tmp.php");



	// ======= ОТПРАВКА НА EMAIL ==================================================

	$data = date( d.'.'.m.'.'.Y );

	$ip=GetUserIP();

	$to1 = $order_email_client;

	// SUBJECT тема
	$subject = "Заявка с сайта www.".$site." ";

	$site_code = '=?UTF-8?B?'.base64_encode($site).'?=';

	/* Для отправки HTML-почты Content-type. */
	$headers  = "MIME-Version: 1.0 \r\n";
	$headers .= "Content-type: text/html; charset=UTF-8 \r\n";
	$headers .= "From: www.".$site_code." <".$to2."> \r\n";

	/* сообщение */
	$message = $err.$basket_mail_tmp;

	// = MAIL =
	mail($to1, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);

	// делаем паузу 3 секунды и отправляем ещё одно письмо

	$message = $err_email.$message.'<p>IP '.$ip.'</p>';

	sleep (1);
	mail($to2, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);

	// ======= /отправка на email / ===============================================



	// ======= ОБНОВЛЯЕМ ДАННЫЕ ЗАКАЗА ============================================
	// Обновляем данные в таблице "com_shop_orders"
	$query_update_shop = "UPDATE `com_shop_orders` SET  `status` =  '$status', `date_payment` = NOW() WHERE  `id` = '$label' LIMIT 1 ;";
	$sql_page = mysql_query($query_update_shop);

	// Чистим корзину и удаляем номер заказа
	unset($_SESSION['basket']);
	unset($_SESSION['shop_basket_nm']);

} // конец функции component

?>
