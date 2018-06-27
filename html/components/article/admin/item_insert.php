<?php
// DAN обновлено - январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$title = htmlspecialchars($_POST["title"]);
$section_id = $_POST["section"];
$pub = intval($_POST["pub"]);
$ordering = intval($_POST["ordering"]);
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$sef = checkingeditor($_POST["sef"]);
$introtext = $_POST["editor1"];
$fulltext = $_POST["editor2"];

$bt_save = $_POST["bt_save"]; // кнопка 'Сохранить'
$bt_prim = $_POST["bt_prim"]; // кнопка 'Применить'
$bt_none = $_POST["bt_none"]; // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com"); exit;}

if (!isset($pub) || $pub == ""){$pub = "0";} // Условие публикации

// условия заполнения полей

if ($title == "" || $title == " ") { $err = '<div id="main-top">Поле "Наименование статьи" не заплонено!</div>';}
else
{
	$i = 1;
	// Находим все статьи, следующие за этой
	$asql = "SELECT * FROM `com_article_item` WHERE `section` = '$section_id' AND `ordering`>='$ordering' ORDER BY `ordering`";
	$aqsql = mysql_query($asql);
	while($n = mysql_fetch_array($aqsql)):
		$article_id = $n['id'];
		$article_ordering = $ordering + $i;
		$i++;

		// Обновляем данные в таблице "com_article_item" для статей с порядком на единицу большим нашего
		$query_update_article = "UPDATE `com_article_item` SET ordering = '$article_ordering' WHERE `id` = '$article_id';";
		$sql_menu = mysql_query($query_update_article) or die ("Невозможно обновить данные 1");
	endwhile;

	$data = date( d.'.'.m.'.'.Y );	// дата


	// Вставляем в таблицу "com_article_item"
	$query_insert_item = "INSERT INTO `com_article_item` (`id`, `section`, `pub`, `ordering`, `title`, `introtext`, `fulltext`, `views`, `rating`, `vote_plus`, `vote_minus`, `cdate`, `lastip`, `tag_title`, `tag_description`) VALUES (NULL, '$section_id', '$pub', '$ordering', '$title', '$introtext', '$fulltext', '0', '50', '0', '0', NOW(), '', '$tag_title', '$tag_description')";

	$sql_item = mysql_query($query_insert_item) or die ("Невозможно сделать вставку в таблицу - 2");
	$id_com = mysql_insert_id();


	// --- ЧПУ URL ----------------------------------------------------------------------------------------
	if(isset($sef) && $sef != '')
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
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef`
				{
					// Вставляем данные в таблице "url"
					$url_sql = "INSERT INTO `url` (url, sef) VALUES('article/item/$id_com', '$sef')";
					$url_query = mysql_query($url_sql) or die ("Невозможно вставить данные 4");
				}
			}
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/article/itemedit/".$id_com); exit;}

}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
