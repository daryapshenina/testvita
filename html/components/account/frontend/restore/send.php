<?php
defined('AUTH') or die('Restricted access');
$psw = 'DAN_restore';

$title = 'Восстановление пароля '.$domain;
$description = '';

$err = '';

// Заносим в базу данных
if($_SERVER["REQUEST_METHOD"] == 'POST')
{
	$hash = $_POST['h'];
	$vector = $_POST['v'];	
	$email = $_POST['email'];

	$time = intval(openssl_decrypt($hash, 'AES-256-CTR', $psw, 0, $vector));
	$delta = time() - $time;

	// Интервал ожидания отправки из формы
	if($delta < 2 || $delta > 3600)
	{
		$err .= 'Что-то пошло не так';
	}

	// Проверяем, есть ли пользователь с подобным email в системе
	$stmt_user = $db->prepare("SELECT id FROM com_account_users WHERE email = :email LIMIT 1");
	$stmt_user->execute(array('email' => $email));

	if($stmt_user->rowCount() == 0) $err = 'Пользователь не найден';

	if ($err == '') // нет ошибки
	{
		$user_id = $stmt_user->fetchColumn();

		$stmt_del = $db->prepare("DELETE FROM com_account_activation_code WHERE user_id = :user_id");
		$stmt_del->execute(array('user_id' => $user_id));

		$rand = rand(0, 999999);
		$vector = substr(md5($rand), 0, 16);		
		$code_user_id = urlencode(openssl_encrypt($user_id, 'AES-256-CTR', $psw, 0, $vector));

		$time = time();
		$code = hash("sha256", $psw.$time.rand());

		$stmt_insert_code = $db->prepare('INSERT INTO com_account_activation_code SET user_id = :user_id, code = :code, time = :time');
		$stmt_insert_code->execute(array('user_id' => $user_id, 'code' => $code, 'time' => $time));

		$message = 'Для восстановления Вашего аккаунта пожалуйста перейдите по ссылке. Ссылка действительна в течении 24 часов.<br><br>';
		$message .= '<a href="http://'.$domain.'/account/restore/check/'.$code_user_id.'/'.$vector.'/'.$code.'">http://'.$domain.'/account/restore/check/'.$code_user_id.'/'.$vector.'/'.$code.'</a><br><br>';
		$message .= 'Если не получается перейти по ссылке скопируйте ее в адресную строку Вашего браузера.<br><br>';
		$subject = "Восстановление пароля на сайте ".$domain_idn;

		// Отправка на почту
		classMail::send($email,'no-replay',$subject,$message,'');
	}	
}



function component()
{
	global $domain, $err;

	if($err == '')
	{
		$title = 'Восстановление пароля';
		$content = '
			<div>Ссылка на восстановление пароля отправлена на Ваш email</div>
			<div>Перейдите по ссылке, указанной в письме. Ссылка действительна в течении 24 часов.</div>
		';
	}
	else
	{
		$title = 'Ошибка';
		$content = $err;
	}

	echo '
	<div class="registration_form">
		<h1 class="registration_h1">'.$title.'</h1>
		<div class="registration_cont">'.$content.'</div>	
	</div>
	';
}


?>