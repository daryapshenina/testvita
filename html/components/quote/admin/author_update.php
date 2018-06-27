<?php
// DAN 2012
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$id = intval($admin_d4);
$author = $_POST["author"];
$description = $_POST["editor1"];

$none = $_POST["none"]; // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/quote/authors"); exit;} 


// Обновляем данные в таблице "com_quote_authors" 
$author_sql = "UPDATE `com_quote_authors` SET `author` = '$author', `description` = '$description' WHERE `id` = '$id' LIMIT 1 ;";
			
$author_query = mysql_query($author_sql) or die ("Невозможно обновить данные");	
	
Header ("Location: http://".$site."/admin/com/quote/authors"); exit;

// ==================================================================================
	
function a_com()
{ 
	global $err; 
}

?>