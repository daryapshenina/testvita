<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';
include_once $root."/classes/Auth.php";

$account_settings = accountSettings::getInstance();
if(!$account_settings->registration_allow){Header ("Location: /"); exit;}


$title = 'Регистрация на сайте '.$domain;
$description = '';

$err = '';

if(isset($_SESSION['uid'])) unset($_SESSION['uid']);

// Заносим в базу данных
if($_SERVER["REQUEST_METHOD"] == 'POST')
{
	if(isset($_POST['email'])){$email = mb_strtolower(trim($_POST['email']));} else{$email = '';}
	if(isset($_POST['password'])){$psw = $_POST['password'];} else{$psw = '';}
	if(isset($_POST['captcha'])){$captcha = intval($_POST['captcha']);} else{$captcha = '';}
	if(isset($_POST['data'])){$url_return_code = $_POST['data'];} else{$url_return_code = '';}

	if (!preg_match("/^[^@]+@[^@]+\.[a-zа-я]{2,20}$/ui",$email)){$err .= 'Не правильно заполнено поле "Email"<br>';}
	if (!preg_match("/^[a-z0-9]{6,20}$/i",$psw)){$err .= 'Не правильно заполнено поле "Пароль"<br>';}


	// = Проверка кода на картинке ====================================================================
	if(isset($_SESSION['code']) && isset($captcha))
	{
		if($_SESSION['code'] != $captcha || $captcha == 0){$err .= 'Не верно указан код с картинки<br>';}
	}
	else {$err .= 'Отсутствует код с картинки<br>';}


	// Находим - зарегистрирован ли пользователь
	$stmt_users = $db->prepare('SELECT id FROM com_account_users WHERE email = :email LIMIT 0, 1');
	$stmt_users->execute(array('email' => $email));

	// while($row = $stmt_users->fetch()){print_r($row);}

	 // email уже есть
	if($stmt_users->rowCount() > 0){$err .= 'Данный email <b>'.$email.'</b> уже зарегистрирован в системе<br><br><a href="/account">Войти в систему</a><br><a href="/account/restore">Восстановить пароль</a>';}

	if($err == '')
	{
		$last_ip = GetUserIP(); // находим ip
		$psw_hash = hash("sha256", 'DAN_psw'.$email.$psw); // генерируем хеш пароля

		$stmt_users_insert = $db->prepare('INSERT INTO com_account_users SET email = :email, psw = :psw, cid = :cid, date_reg = :date_reg, date_visit = :date_visit, status = :status');
		$stmt_users_insert->execute(array('email' => $email, 'psw' => $psw_hash, 'cid' => '', 'date_reg' => date("Y-m-d H:i:s"), 'date_visit' => date("Y-m-d H:i:s"), 'status' => '0'));

		$user_id = $db->lastInsertId();

		// email
		$time = time();

		$code_user_id = Auth::encode($user_id);
		$code = hash("sha256", 'DAN_check_email'.$time.rand());

		$stmt_insert_code = $db->prepare('INSERT INTO com_account_activation_code SET user_id = :user_id, code = :code, time = :time');
		$stmt_insert_code->execute(array('user_id' => $user_id, 'code' => $code, 'time' => $time));

		$message = 'Спасибо за регистрацию!<br><br>';
		$message .= 'Для активации Вашего аккаунта пожалуйста перейдите по ссылке. Ссылка действительна в течении 24 часов.<br><br>';
		$message .= '<a href="http://'.$site.'/account/activation/'.$code_user_id.'/'.$code.'/'.$url_return_code.'">http://'.$domain.'/account/activation/'.$code_user_id.'/'.$code.'/'.$url_return_code.'</a><br><br>';
		$message .= 'Если не получается перейти по ссылке скопируйте ее в адресную строку Вашего браузера.<br><br>';
		$message .= 'Ваши регистрационные данные:<br> Логин: '.$email.'<br> Пароль: '.$psw.'<br>';

		$subject = "Регистрация на сайте ".$domain_idn;

		// Отправка на почту
		classMail::send($email,'no-replay',$subject,$message,'');
	}

	//	TEST
	// echo $err.'-----'.$message; exit;
}

if($err == '' && $_SERVER["REQUEST_METHOD"] == 'POST'){Header ('Location: /account/reg'); exit;}



function component()
{
	global $domain, $err;

	// --- Обработка ошибки ---
	if($err == '')
	{
		// Выводим предложение дальше
		echo '
			<h1 class="title">Подтверждение электронной почты</h1>
			<div>Сообщение со ссылкой отправлено на Ваш email</div>
			<div>Перейдите по ссылке, указанной в письме. Ссылка действительна в течении 24 часов.</div>
		';
	}
	else
	{
		echo '
		<div class="registration_form">
			<h1 class="registration_h1">Ошибка</h1>
			<div class="registration_cont">'.$err.'</div>
		</div>
		';
	}
}



?>