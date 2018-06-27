<?php
// DAN 2012
// Профиль пользователя
defined('AUTH') or die('Restricted access');

// получаем извне
// $sns_users_login
// $sns_ip_lk

// Авторизирован ли пользователь
if ($sns_auth_check == 'true')
{
	// ===== Последнее посещение =======================================================================
	// Получаем время сейчас
	$tday = getdate();
	$today = $tday[year].'-'.$tday[mon].'-'.$tday[mday].' '.$tday[hours].':'.$tday[minutes].':'.$tday[seconds];


	// Смотрим существуют ли куки с последним посещением
	if (isset($_COOKIE['snslv']))
	{
		// Находим разницу в секундах между попытками захода
		$td = strtotime($today)-strtotime($_COOKIE['snslv']);

		// Если больше 600 секунд то обновляем бд и куки
		if ($td > '600')
		{
			// устанавливаем новое значение куки и имя
			$sns_cookie_name_snslv = 'snslv';
			$sns_cookie_value_snslv = $today;
			SetCookie(sns_cookie_name_snslv,$sns_cookie_value_snslv,time()+3600*24*30,'/');

			// Записываем в БД
			$sns_last_ip_query = "UPDATE `sns_users_psw` SET `lastip` = '$sns_ip_lk', `ldate` = '$today' WHERE `login` = '$sns_users_login' LIMIT 1 ;";

			$sns_last_ip_sql = mysql_query($sns_last_ip_query) or die ("Невозможно обновить данные 1");
		}
	}
	else
	{
		// устанавливаем Cookie на один месяц для всех папок
		$sns_cookie_name_snslv = 'snslv';
		$sns_cookie_value_snslv = $today;
		SetCookie($sns_cookie_name_snslv,$sns_cookie_value_snslv,time()+3600*24*30,'/');

		// Записываем в БД дату последнего посещения и айпи для логина вытащеного из бд
		$sns_last_ip_query = "UPDATE `sns_users_psw` SET `lastip` = '$sns_ip_lk', `ldate` = '$today' WHERE `login` = '$sns_users_login' LIMIT 1 ;";

		$sns_last_ip_sql = mysql_query($sns_last_ip_query) or die ("Невозможно обновить данные 1");
	}
	
	// ======= / последнее посещение / ===========================================================
	
		
}
				



?>