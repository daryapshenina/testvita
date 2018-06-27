<?php
// DAN 2012
// Модуль авторизации
defined('AUTH') or die('Restricted access');

$modules_title_newcomments = $modules_title;
$modules_titlepub_newcomments = $modules_titlepub;
$suf_newcomments = $modules_module_csssuf;


$quantity = $modules_p1;
$length = $modules_p2;


if ($modules_pub == "1")
{	
	// верх модуля
	echo'<div class="mod-main'.$suf_newcomments.'">
	<div class="mod-top'.$suf_newcomments.'">';
	
	// Заголовок модуля
	if ($modules_titlepub_newcomments == "1")
	{		
		echo '<div class="mod-title'.$suf_newcomments.'">'.$modules_title_newcomments.'</div>';
	}
	
	echo'</div>';
	
	// комментарии
	$newcomments_query = mysql_query("SELECT * FROM `sns_comments` WHERE `active` = '1' ORDER BY `id` desc LIMIT $quantity") or die ("Невозможно сделать выборку из таблицы - 1");	
	
	// количество комментариев
	$count = mysql_num_rows($newcomments_query);		

	// средняя часть
	echo'
		<div class="mod-mid'.$suf_newcomments.'">
			<div class="mod-padding'.$suf_newcomments.'">
				';
				

	if($count >0)
	{
		while($n = mysql_fetch_array($newcomments_query)):
			$newcomments_item_id = $n['item_id'];
			$newcomments_item_type = $n['item_type'];
			$newcomments_comments = $n['comments'];
			$newcomments_date = $n['date'];	
			
			// выводим дату из бд и меняем её вид
			$newcomments_cdate_d = substr($newcomments_date, 0, 10);
			$cdate = explode("-",$newcomments_cdate_d);
			$cd['01'] = 'января';
			$cd['02'] = 'февраля';
			$cd['03'] = 'марта';
			$cd['04'] = 'апреля';
			$cd['05'] = 'мая';
			$cd['06'] = 'июня';
			$cd['07'] = 'июля';
			$cd['08'] = 'августа';
			$cd['09'] = 'сентября';
			$cd['10'] = 'октября';
			$cd['11'] = 'ноября';
			$cd['12'] = 'декабря';
			
			// длина комментария
			if ( strlen($newcomments_comments) > $length)
			{
				$newcomments_comments = substr($newcomments_comments, 0, $length);
				$newcomments_comments = $newcomments_comments.'...>>>';
			}
			
			// тип материала, который комментируем
			if ($newcomments_item_type == 'article')
			{
				$query_article = mysql_query("SELECT `title` FROM `com_article_item` WHERE `id` = '$newcomments_item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");	
				
				while($n = mysql_fetch_array($query_article)):
					$article_title = $n['title'];
				endwhile;	
				
				$newcomments_title = '<div><a target="_blank" class="mod-newcomments-title" href="http://'.$site.'/article/item/'.$newcomments_item_id.'" >'.$article_title.'</a></div>';
			}
			
			
			echo '<div class="mod-newcomments-date">'.$cdate[2].' '.$cd[$cdate[1]].' '.$cdate[0].'</div>';
			echo $newcomments_title;			
			echo '<div>'.$newcomments_comments.'</div><div>&nbsp;</div>';
	
		
		endwhile;
	}
		
		   echo'
			</div>
		</div>            
		<div class="mod-bot'.$suf_newcomments.'"></div></div>
	';					
}

?>