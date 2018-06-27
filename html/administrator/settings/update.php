<?php
// DAN 2010
// Настройки сайта
defined('AUTH') or die('Restricted access');

$publish = $_POST["publish"];
$title = htmlspecialchars($_POST["title"]);
$description = htmlspecialchars($_POST["description"]);
$statistics = $_POST["statistics"];
$email = mb_strtolower(trim(htmlspecialchars($_POST["email"])));
$meta_tag = strip_tags($_POST["meta_tag"], '<meta>');
$personal_information = intval($_POST["personal_information"]);

if (!isset($publish) || $publish == ""){$publish = "0";}

// Обновляем данные в таблице "settings"
// сайт включен
$stmt_u1 = $db->prepare("UPDATE settings SET parameter = :parameter WHERE id = '1' ");
$stmt_u1->execute(array('parameter' => $publish));

// название сайта
$stmt_u2 = $db->prepare("UPDATE settings SET parameter = :parameter WHERE id = '2' ");
$stmt_u2->execute(array('parameter' => $title));

// описание сайта
$stmt_u3 = $db->prepare("UPDATE settings SET parameter = :parameter WHERE id = '3' ");
$stmt_u3->execute(array('parameter' => $description));

// код счётчика статистики
$stmt_u5 = $db->prepare("UPDATE settings SET parameter = :parameter WHERE id = '5' ");
$stmt_u5->execute(array('parameter' => $statistics));

// email
$stmt_u6 = $db->prepare("UPDATE settings SET parameter = :parameter WHERE id = '6' ");
$stmt_u6->execute(array('parameter' => $email));

// метатеги
$stmt_u7 = $db->prepare("UPDATE settings SET parameter = :parameter WHERE id = '7' ");
$stmt_u7->execute(array('parameter' => $meta_tag));

// метатеги
$stmt_pi = $db->prepare("UPDATE settings SET parameter = :parameter WHERE name = 'personal_information' ");
$stmt_pi->execute(array('parameter' => $personal_information));


// Перезагружаем иконку
$favicon_name = checkingeditor_2(@$_FILES['favicon']['name']); // Оригинальное имя файла на компьютере клиента.
$favicon_size = checkingeditor_2(@$_FILES['favicon']['size']); // Размер в байтах принятого файла.
$favicon_tmp_name = @$_FILES['favicon']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.

if (isset($favicon_size)&& $favicon_size > 0) // если есть иконка
{
	if(strtolower($favicon_name) == 'favicon.ico' && $favicon_size < 10000)
	{
		if(file_exists($favicon_tmp_name)){Copy($favicon_tmp_name, 'favicon.ico');}

		Header ("Location: /admin/"); exit;
	}
	else
	{
		function a_com()
		{
			echo '<div id="main-top">Не верный формат иконки сайта</div>';
			if ($favicon_size > 10000){echo 'Размер иконки - свыше 10КБайт - это неадекватность какая-то!';}
		}
	}
}
else
{
	Header ("Location: /admin/"); exit;
}
?>