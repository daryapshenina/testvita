<?php
defined('AUTH') or die('Restricted access');

function component()
{
	global $db;
	$err = '';
	if(!preg_match("/^[a-z0-9]{6,20}$/i",$_POST['password'])) $err .= 'Не правильно заполнено поле "Пароль".<br>';
	if($_POST['password'] != $_POST['password_2']) $err .= 'Пароли не совпадают.<br>';
	if(!Auth::check()) $err .= 'Пользователь не авторизирован<br>';

	if($err == '')
	{
		$user = Auth::getUser();

		$psw_hash = hash("sha256", 'DAN_psw'.$user['email'].$_POST['password']); // генерируем хеш пароля		
		$time = time();

		$stmt_update = $db->prepare('UPDATE com_account_users SET psw = :psw, date_visit = :date_visit, status = 1 WHERE id = :id');
		$stmt_update->execute(array('id' => $user['id'], 'psw' => $psw_hash, 'date_visit' => date("Y-m-d H:i:s")));

		echo '
			<h1 class="title">Ваши данные для входа на сайт</h1>
			<div><b>Логин:</b> '.$user['email'].'</div>
			<div><b>Пароль:</b> '.$_POST['password'].'</div>	
		';
	}
	else
	{
		echo '<h1 class="title">Ошибка</h1><div>'.$err.'</div>';
	}
}

?>