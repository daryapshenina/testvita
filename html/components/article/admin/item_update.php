<?php
// DAN обновление - январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4);
$section = intval($_POST["section"]);
$pub = intval($_POST["pub"]);
$ordering = intval($_POST["ordering"]);
$title = htmlspecialchars($_POST["title"]);
$introtext = $_POST["editor1"];
$fulltext = $_POST["editor2"];
$views = intval($_POST["views"]);
$vote_plus = intval($_POST["vote_plus"]);
$vote_minus = intval($_POST["vote_minus"]);
$cdate = $_POST["cdate"];
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$sef = checkingeditor($_POST["sef"]);

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else{$bt_save = '';} // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else{$bt_prim = '';} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';} // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/article/section/".$section."/".$menu_t); exit;}

if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";} // Условие публикации

// если существуют голоса, только тогда назначаем рейтинг
if ($vote_plus > 0 || $vote_minus > 0)
{
	$vote_sum = $vote_plus + $vote_minus;
	$rating = intval(100*$vote_plus/$vote_sum);
}
else
{
	$rating = 50;
}

if ($title == "" || $title == " ")
{
	$err = '<div id="main-top">Поле "Наименование товара" не заплонено!</div>';
}
else
{
	// Обновляем данные в таблице "com_article_item"
	$query_update_article_item = "UPDATE `com_article_item` SET `section` = '$section', pub = '$pub', `ordering` = '$ordering', `title` = '$title', `introtext` = '$introtext', `fulltext` = '$fulltext', `views` = '$views', `rating` = '$rating', `vote_plus` = '$vote_plus', `vote_minus` = '$vote_minus', `cdate` = '$cdate', `tag_title` = '$tag_title', `tag_description` = '$tag_description' WHERE `id` = '$item_id' LIMIT 1 ;";

	$sql_article_item = mysql_query($query_update_article_item) or die ("Невозможно обновить данные");



	// --- ЧПУ URL ----------------------------------------------------------------------------------------
	if(isset($sef))
	{
		if(classvalidation::checkReservedWord($sef)) // зарезервированно
		{
			$sef_err = 1;
		}
		else
		{
			// проверяем на символы
			if (!preg_match("/^[a-z0-9-\/]{1,255}$/is",$sef))
			{
				$sef_err = 1;
			}
			else
			{
				$sef = strtolower($sef); // в нижний регистр

				// проверяем - есть ли уже запись для `sef`
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef' AND `url` <> 'article/item/$item_id'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef` (наш не в счёт)
				{
					// проверяем - есть ли уже запись
					$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'article/item/$item_id'") or die ("Ошибка - 1");
					$url_query_result = mysql_num_rows($url_query);

					if($url_query_result > 0) // запись есть
					{
						// Обновляем данные в таблице "url"
						$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'article/item/$item_id'";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
					}
					else // запись отсутствует
					{
						// Вставляем в таблицу
						$url_sql = "INSERT INTO `url` (id, url, sef) VALUES(NULL, 'article/item/$item_id', '$sef')";
						$url_query = mysql_query($url_sql) or die ("Ошибка - 3") ;
					}
				}
			}
		}

		if($sef == '')
		{
			// Обновляем данные в таблице "url"
			$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'article/item/$item_id'";
			$url_query = mysql_query($url_sql) or die ("Ошибка - 4");
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------


	if($bt_save == 'Сохранить'){Header ("Location: /admin/com/article/section/".$section); exit;}
	else {Header ("Location: /admin/com/article/itemedit/".$item_id); exit;}

} // конец условия заполненного пункта меню

// ==================================================================================

function a_com()
{
	global $err;
}

?>
