<?php
// вывод содержимого страницы
defined('AUTH') or die('Restricted access');

if($frontend_edit == 1){$head->addFile('/components/page/frontend/edit.js');}

$page_id = $d[1];

if($d[1] == ''){$page_id = 1;} else{$page_id = $d[1];}
if(isset($_POST["pass_in"])){$page_psw_in = htmlspecialchars($_POST["pass_in"]);} else {$page_psw_in = '';}

// ID активного меню
$active_menu = $page_id;

$page_stmt = $db->prepare("
	SELECT m.id as menu_id, m.id_com, p.id, p.title, p.text, p.tag_title, p.tag_description, p.access, p.psw
	FROM menu m
	JOIN com_page p ON p.id = m.id_com
	WHERE m.component = 'page' AND m.id_com = :page_id AND m.pub = '1' LIMIT 1");
$page_stmt->execute(array('page_id' => $page_id));


if($page_stmt->rowCount() == 0 && $page_id != 1)
{
	header("HTTP/1.0 404 Not Found");
	include($root."/404.php");
	exit;	
}

$page_item = $page_stmt->fetch();

// Метатеги
if($page_item['tag_title'] == '')
{
	$tag_title = $page_item['title'].' - '. Settings::instance()->getValue('Наименование сайта');
}
else{
	$tag_title = $page_item['tag_title'];	
}

if($page_item['tag_description'] == '')
{
	$tag_description = Settings::instance()->getValue('Описание сайта');
}
else{
	$tag_description = $page_item['tag_description'];	
}


// Если доступ не закрыт - выводим обычную страницу
if ($page_item['access'] != 1)
{
	function component()
	{
		global $domain, $page_item, $frontend_edit;

		// Подключаем шаблон страницы
		include("components/page/frontend/tmp/page.php");
	}
}
else // Если доступ закрыт
{
	// пароль - если не пустой - проверяем на символы
	if ($page_psw_in != '' && (!preg_match("/^[a-z0-9]{4,20}$/is",$page_psw_in)))
	{
		$head->addFile('<link rel="stylesheet" href="/components/page/frontend/tmp/login.css');

		function component()
		{
			global $domain, $page_item, $frontend_edit;
			include("components/page/frontend/tmp/login.php");
		}
	}
	else // Пароль есть и он валидный
	{
		// Captcha
		if(!isset($_SESSION)){session_start();}

		if(isset($_POST['cod']) && !empty($_SESSION['code']) && $_SESSION['code'] == intval($_POST['cod']))
		{
			$frontend_captcha = 1;
		}

		// если пароль подходит
		if($page_item['psw'] == $page_psw_in && $frontend_captcha == 1)
		{
			function component()
			{
				global $domain, $page_item, $frontend_edit;

				// Подключаем шаблон страницы
				include("components/page/frontend/tmp/page.php");
			}
		}
		else
		{
			$head->addFile('/components/page/frontend/tmp/login.css');

			function component()
			{
				global $domain, $page_item, $page_id, $url_arr, $frontend_edit;

				// если есть в массиве ЧПУ - заменяем
				if(isset($url_arr['page/'.$page_id]) && $url_arr['page/'.$page_id] != '')
				{
					$url = '/'.$url_arr['page/'.$page_id];
				}
				else
				{
					$url = '/page/'.$page_id;
				}

				include("components/page/frontend/tmp/login.php");
			}
		}
	}
}




?>