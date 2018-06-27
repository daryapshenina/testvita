<?php
// Проверка и аутентификация пользователя
defined('AUTH') or die('Restricted access');

// Мета - теги
$title = 'Регистрация пользователя';
$description = '';

$head->addFile('/components/profile/frontend/login/tmp/style.css');
	
$err = '';

if(isset($_POST['email'])){$email = mb_strtolower(trim($_POST['email']));} else{$email = '';}
if(isset($_POST['password'])){$psw = $_POST['password'];} else{$psw = '';}
if(isset($_POST['captcha'])){$captcha = intval($_POST['captcha']);} else{$captcha = '';}

if (!preg_match("/^[^@]+@[^@]+\.[a-zа-я]{2,10}$/ui",$email)){$err .= 'Не правильно заполнено поле "Email"<br>';}
if (!preg_match("/^[a-z0-9]{6,20}$/i",$psw)){$err .= 'Не правильно заполнено поле "Пароль"<br>';}

if(isset($_SESSION['code']) && isset($captcha))
{
	if($_SESSION['code'] != $captcha || $captcha == 0){$err .= '<div class="login_err">Не верно указан код с картинки.</div>';}
}
else {$err .= '<div class="login_err">Отсутствует код с картинки</div>';}

	

if ($err == '') // нет ошибки
{
	$psw_hash = hash("sha256", 'DAN_psw'.$email.$psw);	// генерируем хеш пароля

	$stmt_psw = $db->prepare("SELECT id FROM com_profile_users WHERE email = :email AND psw = :psw_hash LIMIT 1");
	$stmt_psw->execute(array('email'=>$email, 'psw_hash'=>$psw_hash));

	if($stmt_psw->rowCount() == 0)
	{
		$err .= '<div class="login_err">Неправильный логин или пароль.<br><br>';
		$err .= 'Возможно у вас выбрана другая раскладка клавиатуры или нажата клавиша "Caps Lock".</div>';
	}
}



if($err == '')
{
	$user_id = $stmt_psw->fetchColumn();

	// Ставим куки авторизации
	$rnd = rand(0, 9999999999);
	$cid = hash('sha256', 'DAN_5za'.$rnd);

	$stmt_update = $db->prepare('UPDATE com_profile_users SET cid = :cid WHERE id = :user_id LIMIT 1');
	$stmt_update->execute(array('cid' => $cid, 'user_id' => $user_id));

	// Если есть куки - перезаписываем, если нет - ставим
	SetCookie('uid', $user_id, (time () + 60*60*24*365), '/', '.'.$domain, False, True); // user ID			
	SetCookie('cid', $cid, (time () + 60*60*24*365), '/', '.'.$domain, False, True); // куки авторизации
	
	if(!isset($_SESSION)){session_start();}
	$_SESSION['uid'] = $user_id;
	
	Header("Location: http://".$domain."/profile/view"); 
	exit;
}





function component()
{
	global $root, $db, $domain, $domain_idn, $err;
	
	echo $err;
}
?>