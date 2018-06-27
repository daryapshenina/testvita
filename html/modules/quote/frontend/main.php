<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

$modules_title_quote = $modules_title;
$modules_titlepub_quote = $modules_titlepub;
$suf_quote = $modules_module_csssuf;

if ($modules_pub == "1")
{
	// верх модуля
	echo'<div class="mod-main'.$suf_quote.'">
	<div class="mod-top'.$suf_quote.'">';
	
	// Заголовок модуля
	if ($modules_titlepub_quote == "1")
	{		
		echo '<div class="mod-title'.$suf_quote.'">'.$modules_title_quote.'</div>';
	}
	
	echo'</div>';		
	
	// средняя часть
	echo'
		<div class="mod-mid'.$suf_quote.'">
			<div class="mod-padding'.$suf_quote.'">
		';	
	
		// находим количество цитат
		$quote_sql_r = "SELECT COUNT(*) FROM `com_quote_item`";
		$quote_query_r = mysql_query($quote_sql_r) or die ("Невозможно сделать выборку из таблицы - 1");	
		
		$quote_result_r = mysql_fetch_row($quote_query_r);
		
		// количество цитат
		$quote_result = $quote_result_r[0] - 1;
		
		$row_rand = round(rand(0,$quote_result));		
		
		$quote_sql = "SELECT * FROM `com_quote_item` LIMIT $row_rand,1;";
		$quote_query = mysql_query($quote_sql) or die ("Невозможно сделать выборку из таблицы - 2");	
		
		while($m = mysql_fetch_array($quote_query)):
			$quote = $m['quote'];
			$author_id = $m['author_id'];	
			
			$author_sql = "SELECT * FROM `com_quote_authors` WHERE `id` = '$author_id' LIMIT 1";		
			$author_query = mysql_query($author_sql) or die ("Невозможно сделать выборку из таблицы - 6");
			
			while($a = mysql_fetch_array($author_query)):
				$author_id = $a['id'];				
				$author = $a['author'];			
			endwhile;			
			
			echo '<div class="quote_r" align="center">'.$quote.'</div>';
			echo '<div class="quote_author_r" align="center"><a href="http://'.$site.'/quote/author/'.$author_id.'">'.$author.'</a></div>';			
		endwhile;		
		
	echo'
            </div>
     	</div>            
		<div class="mod-bot'.$suf_quote.'"></div></div>
	';			
	
}

else 
{
	function mod_quote(){}	
}


?>