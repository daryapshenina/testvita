<?php
// DAN 2012
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4);
$section = intval($_POST["section"]);
$author = intval($_POST["author"]);
$ordering = intval($_POST["ordering"]);
$quote = htmlspecialchars($_POST["quote"]);
$vote_plus = intval($_POST["vote_plus"]);
$vote_minus = intval($_POST["vote_minus"]);

$none = $_POST["none"]; // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/quote/section/".$section); exit;} 

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
	
if ($quote == "" || $quote == " ") 
{ 
	$err = '<div id="main-top">Поле "Цитата" не заплонено!</div>';
}
else 
{	
	// Обновляем данные в таблице "com_article_item" 
	$quote_sql = "UPDATE `com_quote_item` SET `quote` = '$quote', `ordering` = '$ordering', `section_id` = '$section', `author_id` = '$author', `rating` = '$rating', `vote_plus` = '$vote_plus', `vote_minus` = '$vote_minus' WHERE `id` = '$item_id' LIMIT 1 ;";
			
	$$quote_query = mysql_query($quote_sql) or die ("Невозможно обновить данные");	
	
	Header ("Location: http://".$site."/admin/com/quote/section/".$section); exit;
} // конец условия заполненного пункта меню

// ==================================================================================
	
function a_com()
{ 
	global $err; 
}

?>