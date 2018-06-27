<?php
// DAN 2012
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$author = htmlspecialchars($_POST["author"]);
$description = $_POST["editor1"];

$none = $_POST["none"]; // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/quote/authors"); exit;} 


// Вставляем в таблицу "com_quote_author"	
$query_insert_author = "INSERT INTO `com_quote_authors` (`id`, `author`, `description`) VALUES (NULL, '$author', '$description')";	

	
$sql_author = mysql_query($query_insert_author) or die ("Невозможно сделать вставку в таблицу - 1");

Header ("Location: http://".$site."/admin/com/quote/authors"); exit;


// ==================================================================================
	
function a_com()
{ 
	global $err; 
	echo $err;
	
} // конец функции

?>