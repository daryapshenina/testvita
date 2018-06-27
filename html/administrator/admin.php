<?php
defined('AUTH') or die('Restricted access');

// Проверка логина
include("administrator/login.php");

if(isset($d[1])){$admin_d1 = $d[1];} else{$admin_d1 = '';}
if(isset($d[2])){$admin_d2 = $d[2];} else{$admin_d2 = '';}
if(isset($d[3])){$admin_d3 = $d[3];} else{$admin_d3 = '';}
if(isset($d[4])){$admin_d4 = $d[4];} else{$admin_d4 = '';}
if(isset($d[5])){$admin_d5 = $d[5];} else{$admin_d5 = '';}
if(isset($d[6])){$admin_d6 = $d[6];} else{$admin_d6 = '';}
if(isset($d[7])){$admin_d7 = $d[7];} else{$admin_d7 = '';}

// перебрасываем на фронтенд редактирование
if($admin_d1 == 'wysiwyg')
{	
	$ses = '5za_frontend_edit';
	$sess = md5($ses);

	$_SESSION['s5za_e'] = $sess; // сессия frontend редактирования
	header("Location: http://".$_SERVER['HTTP_HOST']);
}

// просмотр сайта - удаляем сессию визуального редактирования
if($admin_d1 == 'viewsite')
{	
	$_SESSION['s5za_e'] = ''; // сессия frontend редактирования
	header("Location: http://".$domain);
}

// Подключаем функцию меню
include("administrator/menu.php");

// === Определяем действие ===============================
if ($admin_d1 == '' || $admin_d1 == 'home' )
{
	include("administrator/homepage.php");
}

// === Компонент ===================================================================
elseif ($admin_d1 == "com") // если действие = "com" - выбрать компонент
{
	// вывод компонентов
	$stmt_com = $db->query("SELECT components FROM components WHERE components = '".$admin_d2."' AND (enabled = '1' OR enabled = '3')");
	
	$components_com = $stmt_com->fetchColumn();	

	if ($admin_d2 == $components_com)
	{
		include($root."/components/".$components_com."/admin/main.php");
		$acom = 777;	
	}		

	// если компонент не определён
	if ($admin_d2 == "" || $acom != "777" ) // если компонент не определён
	{
		include("administrator/homepage.php");
	}		
}

elseif ($admin_d1 == "modules"){include("administrator/modules/main.php");} // модули
elseif ($admin_d1 == "settings"){include("administrator/settings/main.php");} // настройки
elseif ($admin_d1 == "users"){include("administrator/users/main.php");} // пользователи
elseif ($admin_d1 == "help"){include("administrator/help/main.php");} // помощь
elseif ($admin_d1 == "chat"){include("administrator/chat/main.php");} // чат
elseif ($admin_d1 == "upgrade"){include("administrator/upgrade/main.php");} // обновление
else {include("administrator/homepage.php");}
// ===================================================================================

include("administrator/modules/module.php"); // Подключаем модули
include("upgrade/module.php"); // Подключаем проверку обновления
include("administrator/tmp/tmpl.php"); // Подключаем шаблон

?>
