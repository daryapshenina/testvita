<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/account/frontend/restore/check.js');
$psw = 'DAN_restore';

$d_r = explode('/', $str); // Нам нужны данные без перевода в нижний регистр ( х $d >>> $d_r)
$id_c = urldecode($d_r[4]);
$vector = $d_r[5];

$user_id = openssl_decrypt($id_c, 'AES-256-CTR', $psw, 0, $vector);
$code = $d_r[6];
$err = '';


if (!preg_match('/^[a-z0-9]{64}$/iu', $code)){$err .= 'Не верный код активации';}
else
{
	// Находим - зарегистрирован ли пользователь
	$stmt_users = $db->prepare('SELECT `time` FROM com_account_activation_code WHERE user_id = :user_id AND code = :code LIMIT 1');
	$stmt_users->execute(array('user_id' => $user_id, 'code' => $code));

	if ($stmt_users->rowCount() == 0){$err .= 'Не верный код подтверждения<br>Возможно код активирован уже ранее<br>';}
	else
	{
		$row = $stmt_users->fetch();

		if(time() > $row['time'] + 86400){$err .= 'Код устарел<br>';}
		else
		{
			$rnd = rand(0, 9999999999);
			$cid = hash('sha256', 'DAN_account_cid'.$rnd);
			
			$stmt_update = $db->prepare('UPDATE com_account_users SET cid = :cid, status = 1 WHERE id = :user_id LIMIT 1');
			$stmt_update->execute(array('cid' => $cid, 'user_id' => $user_id));
			
			$stmt_delete = $db->prepare('DELETE FROM com_account_activation_code WHERE user_id = :user_id');
			$stmt_delete->execute(array('user_id' => $user_id));
			
			Auth::setSuccess($user_id, $cid);
		}	
	}
}


function component()
{
	global $domain, $url_return, $err;

	if($err == '')
	{
		echo '<h1>Восстановление пароля</h1>';
		echo '<form method="post" action="/account/restore/new">';
		echo '<div class="auth_form_container">';		
		echo '<h3 class="auth_form_title">Придумайте новый пароль</h3>';
		echo '<div class="auth_form_div_pass"><input id="password" class="input auth_form_pass" type="password" name="password" placeholder="Пароль" autocomplete="off" maxlength="30" required="" pattern="[a-zA-Z0-9]{6,20}" title="Только английские буквы, и цифры без пробелов, от 6 до 20 символов"></div>';
		echo '<div class="auth_form_div_pass"><input id="password_2" class="input auth_form_pass" type="password" name="password_2" placeholder="Повторите пароль" autocomplete="off" maxlength="30" required="" pattern="[a-zA-Z0-9]{6,20}" title="Только английские буквы, и цифры без пробелов, от 6 до 20 символов"></div>';
		echo '<div class="auth_form_div_but"><input id="submit" class="auth_form_but" type="submit" value="Отправить" name="send"></div>';
		echo '</div>';		
		echo '</form>';
	}
	else
	{
		echo '
			<h1 class="title">Ошибка</h1><div>'.$err.'</div>
			<div>&nbsp</div>
			<div>&nbsp</div>
			<div style="text-align:center;"><a class="button_gray button_big" href="/'.$url_return.'">Далее</a></div>		
		';
	}
}


?>