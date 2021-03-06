<?php
// DAN 2012
// выводит раздел архива статей
defined('AUTH') or die('Restricted access');

$section_id = intval($d[2]);
$page_nav = intval($d[3]);

if (!isset($page_nav) || $page_nav == ""){$page_nav = 0;}

// ID активного меню
$active_menu = $section_id;

// ======= ПРОВЕРКА СУЩЕСТВОВАНИЯ РАЗДЕЛА ========================================
$section_sql = mysql_query("SELECT * FROM `com_quote_section` WHERE `id` = '$section_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");	
	
$resultsecitem = mysql_num_rows($section_sql); // количество разделов
	
// если разделов нет 
if ($resultsecitem == "0") 
{	
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found"); 
	include("404.php");
	exit;	
}
// ======= / проверка существования раздела ========================================

while($m = mysql_fetch_array($section_sql)):
	$section_id = $m['id'];	
	$section_title = $m['title'];
	$section_description = $m['description'];	
	$section_pub = $m['pub'];	
	$section_parent = $m['parent'];
	$section_ordering = $m['ordering'];
	$tag_title = $m['tag_title'];	
	$tag_description = $m['tag_description'];	
endwhile;

// ####### Функция вывода ##########################################################
function component()
{ 
	global $root, $site, $sorting, $page_nav, $quantity, $section_sql, $section_id, $section_title, $section_description, $section_pub, 
	$section_parent, $section_ordering;
	
	// Подключаем шаблон вывода заголовка раздела
	include($root."/components/quote/frontend/tmp/section_top_tmp.php");

	
	
	// ======= Вывод цитат =======================================================
	
	$quote_sql = "SELECT * FROM `com_quote_item` WHERE `section_id` = '$section_id' ORDER BY `rating`
	DESC LIMIT $page_nav,$quantity";
	
	$quote_query = mysql_query($quote_sql) or die ("Невозможно сделать выборку из таблицы - 5");
	
	$resulttov = mysql_num_rows($quote_query); // количество цитат
	
	if ($resulttov > 0) 
	{
		while($m = mysql_fetch_array($quote_query)):
			$section_quote_id = $m['id'];	
			$section_quote_pub = $m['pub'];
			$section_quote_ordering = $m['ordering'];
			$id = $m['id'];				
			$quote = $m['quote'];	
			$author_id = $m['author_id'];	
			$rating = $m['rating'];	
			$vote_plus = $m['vote_plus'];	
			$vote_minus = $m['vote_minus'];				
			$section_quote_lastip = $m['lastip'];
			
			$author_sql = "SELECT * FROM `com_quote_authors` WHERE `id` = '$author_id' LIMIT 1";		
			$author_query = mysql_query($author_sql) or die ("Невозможно сделать выборку из таблицы - 6");
			
			while($a = mysql_fetch_array($author_query)):
				$author_id = $a['id'];				
				$author = $a['author'];			
			endwhile;
			
			// если существуют голоса, только тогда назначаем рейтинг
			if ($vote_plus > 0 || $vote_minus > 0)
			{
				$toolbar_plus = $rating;
				$toolbar_minus = 100 - $rating;
				$avp = '<div class="quote_vb_plus"></div>'; // если есть голоса - отображаем
				$avm = '<div class="quote_vb_minus"></div>'; // если есть голоса - отображаем			
			}
			else 
			{
				$toolbar_plus = 50;
				$toolbar_minus = 50;
				$avp = "";
				$avm = "";			
			}			
			
			// Подключаем шаблон вывода содержимого раздела
			include($root."/components/quote/frontend/tmp/section_middle_tmp.php");
			
		endwhile;
	} // $resulttov > 0
		
	// ----- НАВИГАЦИЯ -----		
	// определяем общее количество статей
	$nav_num_sql = mysql_query("SELECT * FROM `com_quote_item` WHERE `section_id` = '$section_id'") or die ("Невозможно сделать выборку из таблицы - 7");	
	$result_nav_num = mysql_num_rows($nav_num_sql);
	
	$kol_page_nav = ceil($result_nav_num/$quantity); // количество страниц навигации = количество статей / статей на страницу - округляем в большую сторону
	$pn = intval($page_nav/$quantity); // текущая страница - округляем в меньшую сторону	

	if ($kol_page_nav > 1) // если колитчество страниц > 1 - выводим навигацию
	{
		$quote_nav = '<br/>
		<div align="center">
		<table border="0" cellpadding="0" style="border-collapse: collapse">
			<tr>
				<td>
				<div class="navbg"><div class="navpage-str">Страницы:</div>
		';							
		
		for ($i = 1; $i <= $kol_page_nav; $i++) 
		{
			if (($i-1) == $pn)
			{
				$quote_nav .= '<div class="navpage-active">'.$i.'</div>';
			}
			else 
			{
				// для первой страницы убираем параметр навигации = 0
				$nav_link = ($i-1)*$quantity;	
				
				if ($nav_link == 0 ){$nav_link = "";}
				else {$nav_link = '/'.$section_sorting.'/'.$nav_link;}	
				
				$quote_nav .= '<div class="navpage"><a href="http://'.$site.'/quote/section/'.$section_id.$nav_link.'">'.$i.'</a></div>';
			}
					
		}
			$quote_nav .= '</div>
				  </td>
			</tr>
		</table>
		</div>
		<br/>'
		;
		
	}
	
	// ----- / навигация -----
	
	// Подключаем шаблон вывода подвала
	include($root."/components/quote/frontend/tmp/section_bottom_tmp.php");		
	
} // конец функции component
	
?>