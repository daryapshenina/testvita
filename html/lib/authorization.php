<?php
// DAN 2012
// Профиль пользователя
defined('AUTH') or die('Restricted access');

session_start();

// ------- Проверка по сессии -------
$sns_user_id = intval($_SESSION['dansnsid']);
$sns_ses_value_key = htmlspecialchars($_SESSION['dansns']);
$sns_ses_ip_key = htmlspecialchars($_SESSION['dansnsip']);



// запрос по id
$sns_logsql = mysql_query("SELECT * FROM `sns_users_psw` WHERE `id` = '$sns_user_id' AND `active` = '1'") or die ("Err - 1");

$sns_logsql_result = mysql_num_rows($sns_logsql);	

if ($sns_logsql_result > 0)
{			
	while($m = mysql_fetch_array($sns_logsql)):
		$sns_users_id = $m['id'];
		$sns_users_login = $m['login'];			
		$sns_users_psw = $m['psw'];
	endwhile;
}

// Значения из БД
$sns_ses_value_lk = 'dan'.$sns_users_login.$sns_users_psw;
$sns_ses_value_lock = md5($sns_ses_value_lk);

// ip входящего
$sns_ip_lk = GetUserIP();

// шифруем ip
$sns_ses_ip_lk = 'dan'.$sns_ip_lk ;
$sns_ses_ip_lock = md5($sns_ses_ip_lk);	

// признак входа по сессии
if ($sns_ses_value_key == $sns_ses_value_lock && $sns_ses_ip_key == $sns_ses_ip_lock){$sns_login_ses = 1;} else{$sns_login_ses = 0;}	
// ------- / проверка по сессии / -------



// ------- Проверяем значение cookies -------
$sns_user_id = intval($_COOKIE['snsid']);

// запрос по id
$sns_logsql = mysql_query("SELECT * FROM `sns_users_psw` WHERE `id` = '$sns_user_id' AND `active` = '1'") or die ("Err - 2");
			
$sns_logsql_result = mysql_num_rows($sns_logsql);	

if ($sns_logsql_result > 0)
{			
	while($m = mysql_fetch_array($sns_logsql)):
		$sns_users_id = $m['id'];
		$sns_users_login = $m['login'];			
		$sns_users_psw = $m['psw'];
	endwhile;
}		

// получаем cookies
$sns_cookies_value_key = htmlspecialchars($_COOKIE['snslg']);

// вычисляем значение куки
$sns_cookie_value_n = 'psw'.$sns_users_login.$sns_users_psw;
$sns_cookies_value_lock = md5($sns_cookie_value_n);

// признак входа по cookies
if ($sns_cookies_value_key == $sns_cookies_value_lock){$sns_login_cook = 1;} else{$sns_login_cook = 0;}		
	// ------- / проверяем значение cookies / -------	
		
		
// Авторизирован ли пользователь
if ($sns_login_ses == 1 || $sns_login_cook == 1)
{
	$sns_auth_check = 'true';		
}
else
{
	$sns_auth_check = 'false';		
}



// ------- Проверяем, заполнен ли профиль -------
// Авторизирован ли пользователь
if ($sns_auth_check == 'true')
{
	// запрос по id
	$sns_profile_fil_query = mysql_query("SELECT * FROM `sns_users_profile` WHERE `psw_id` = '$sns_user_id'") or die ("Err - 3");
				
	$sns_profile_fil_result = mysql_num_rows($sns_profile_fil_query);	

	if ($sns_profile_fil_result > 0)
	{			
		while($m = mysql_fetch_array($sns_profile_fil_query)):
			$sns_profile_name = $m['name'];
			$sns_profile_family = $m['family'];			
			$sns_profile_sex = $m['sex'];
		endwhile;
		
		if (($sns_profile_name == '' || $sns_profile_family = '' || $sns_profile_sex = '') && ($d[0] != 'login' && $d[0] != 'registration' && $d[0] != 'profile'))
		{
			$sns_profile_filled_check == 'false';
			
			// Выкидываем на страницу с редактированием профиля с признаком незаполненного профиля
			Header ("Location: http://".$site."/profile/".$sns_user_id.'/edit/empty'); exit;	
		}
		else 
		{
			$sns_profile_filled_check == 'true';
		}
	}		
}


?>