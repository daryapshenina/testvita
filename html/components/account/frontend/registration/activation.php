<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';
include_once $root."/classes/Auth.php";

$account_settings = accountSettings::getInstance();
if(!$account_settings->registration_allow){Header ("Location: /"); exit;}

// Мета - теги
$title = 'Активация аккаунта на сайте.';
$description = '';

$uri = explode('/', $_SERVER['REQUEST_URI']);

$err = '';
$user_id = @Auth::decode($uri[3]);
$code = @$uri[4];
$url_return = @Auth::decode($uri[5]);

if (!preg_match('/^[a-z0-9]{64}$/iu',$code))
{
	$err .= 'Не верный код активации<br>';
}
else
{
	// Находим - зарегистрирован ли пользователь
	$stmt_users = $db->prepare('SELECT * FROM com_account_activation_code WHERE user_id = :user_id LIMIT 1');
	$stmt_users->execute(array('user_id' => $user_id));

	if ($stmt_users->rowCount() == 0){$err .= 'Не верный код активации<br>Возможно код активирован уже ранее<br>';}
	else
	{
		while($row = $stmt_users->fetch())
		{
			if ($row['code'] != $code){$err .= 'Не верный код активации<br>';}
			else
			{
				if(time() > $row['time'] + 86400){$err .= 'Код устарел<br>';}				
			}
		}			
	}
	
	// Если проверка прошла успешно
	if ($err == '')
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


function component()
{
	global $domain, $url_return, $err;

	if($err == '')
	{
		echo '
			<h1 class="title">Ваш email подтверждён!</h1>
			<div>&nbsp</div>
			<div>&nbsp</div>
			<div style="text-align:center;"><a class="button_green button_big" href="'.$url_return.'">Далее</a></div>
		';		
	}
	else
	{
		echo '
			<h1 class="title">Ошибка</h1><div>'.$err.'</div>
			<div>&nbsp</div>
			<div>&nbsp</div>
			<div style="text-align:center;"><a class="button_gray button_big" href="'.$url_return.'">Далее</a></div>		
		';
	}
}

?>
