<?php
// DAN 2012
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$section_id = $_POST["section"];
$author_id = $_POST["author"];
$ordering = intval($_POST["ordering"]);
$quote = $_POST["quote"];

$none = $_POST["none"]; // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/quote/authors"); exit;} 

if (!isset($pub) || $pub == ""){$pub = "0";} // Условие публикации

// условия заполнения полей

if ($quote == "" || $quote == " ") { $err = '<div id="main-top">Поле "Цитата" не заплонено!</div>';}
else 
{
	$i = 1;
	// Находим все цитаты, следующие за этой	
	$quote_sql = "SELECT * FROM `com_quote_item` WHERE `section_id` = '$section_id' AND `ordering`>='$ordering' ORDER BY `ordering`";  
	$quote_query = mysql_query($quote_sql); 
	while($n = mysql_fetch_array($quote_query)):
		$quote_id = $n['id'];	
		$quote_ordering = $ordering + $i;
		$i++;

		// Обновляем данные в таблице "com_quote_item" для цитат с порядком на единицу большим нашего
		$query_update_quote = "UPDATE `com_quote_item` SET ordering = '$quote_ordering' WHERE `id` = '$quote_id';";	
		$quote_update = mysql_query($query_update_quote) or die ("Невозможно обновить данные 1");		
	endwhile;	

	// Вставляем в таблицу "com_quote_item"	
	$query_insert_item = "INSERT INTO `com_quote_item` (`id`, `quote`, `ordering`, `section_id`, `author_id`, `rating`, `vote_plus`, `vote_minus`, `lastip`) VALUES (NULL, '$quote', '$ordering', '$section_id', '$author_id', '50', '0', '0', '')";	
	
	$sql_item = mysql_query($query_insert_item) or die ("Невозможно сделать вставку в таблицу - 2");

	Header ("Location: http://".$site."/admin/com/quote/section/".$section_id.""); exit;
}

// ==================================================================================
	
function a_com()
{ 
	global $err; 
	echo $err;
	
} // конец функции

?>