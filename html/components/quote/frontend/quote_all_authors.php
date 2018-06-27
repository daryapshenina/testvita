<?php
// DAN 2012
// выводит раздел архива статей
defined('AUTH') or die('Restricted access');


// ####### Функция вывода ##########################################################
function component()
{ 
	global $root, $site, $quote_title;
		
// Подключаем шаблон вывода заголовка архива цитат
include($root."/components/quote/frontend/tmp/all_sections_title_tmp.php");

// Находим авторов
$authors_query = mysql_query("SELECT * FROM `com_quote_authors` ORDER BY `author` ASC") or die ("Невозможно сделать выборку из таблицы - 1");	

$result = mysql_num_rows($authors_query);

if ($result > 0) 
{	
	while($m = mysql_fetch_array($authors_query)):
		$id = $m['id'];			
		$author = $m['author'];
			
		
		// --- НАХОДИМ ДЛЯ АВТОРА КОЛИЧЕСТВО ЦИТАТ ---
		$quote_sql = "SELECT * FROM `com_quote_item` WHERE `author_id` = '$id'";
		$quote_query = mysql_query($quote_sql) or die ("Невозможно сделать выборку из таблицы - 1");	
		
		$quote_result = mysql_num_rows($quote_query);				
		// --- / находим для автора количество цитат / ---
		
		
		// берём первую букву
		$bukva = mb_substr($author, 0, 1, 'utf-8'); 
		
		// переводим в верхний регистр
		$bukva = mb_strtoupper($bukva);
		
		if (!isset($alphabet[$bukva]))
		{
			$alphabet[$bukva] = $bukva;
			echo '
				<div>&nbsp;</div><div>
				<font color="#00aa00" size="5">'.$alphabet[$bukva].'</font></div>
			';
		}
				
		echo'
			<div><a href="http://'.$site.'/quote/author/'.$id.'">'.$author.'</a> <font color="#999999">('.$quote_result.')</font></div>
		';					
	endwhile;
}



} // конец функции components

?>