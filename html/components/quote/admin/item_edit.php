<?php
// DAN 2012
// Редактируем статью

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $item_id, $item_section_id; 
	
	// находим родительский раздел
	$itemsection = mysql_query("SELECT * FROM `com_quote_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
			
	while($n = mysql_fetch_array($itemsection)):
		$item_quote = $n['quote'];	
		$item_section_id = $n['section_id'];
		$item_author_id = $n['author_id'];		
		$item_ordering = $n['ordering']; 	
		$item_rating = $n['rating'];		
		$item_vote_plus = $n['vote_plus'];
		$item_vote_minus = $n['vote_minus'];		
		$item_cdate = $n['cdate'];
		$item_lastip = $n['lastip'];		
	endwhile; 	
	
			// если существуют голоса, только тогда назначаем рейтинг
		if ($item_vote_plus > 0 || $item_vote_minus > 0)
		{
			$item_toolbar_plus = $item_rating;
			$item_toolbar_minus = 100 - $item_rating;
			$avp = '<div class="article_vb_plus"></div>'; // если есть голоса - отображаем
			$avm = '<div class="article_vb_minus"></div>'; // если есть голоса - отображаем			
		}
		else 
		{
			$item_toolbar_plus = 50;
			$item_toolbar_minus = 50;
			$avp = "";
			$avm = "";			
		}
	
	echo '
	<script language="JavaScript">
		function rating() 
		{
			vote_plus = Number(document.getElementById("vote_plus").value);
			vote_minus = Number(document.getElementById("vote_minus").value);
			vote_sum = vote_plus + vote_minus;
			if (vote_plus > 0 || vote_sum > 0)
			{
				vote_percent_plus = 100*vote_plus/vote_sum;
				vote_percent_plus = Math.round(vote_percent_plus); 
				vote_percent_minus = 100 - vote_percent_plus; 
				vote_rating = vote_percent_plus + \'% за\';
			}
			else 
			{
				vote_percent_plus = 100*vote_plus/vote_sum;
				vote_percent_plus = Math.round(vote_percent_plus); 
				vote_percent_minus = 100 - vote_percent_plus; 
				vote_rating = \'Нет голосов\';
			}

			document.getElementById("rating").innerHTML = \'<div class="votingbar"><div class="vote_bar_plus" style="width: \' + vote_percent_plus +\'%"; ></div><div class="vote_bar_minus" style="width: \' + vote_percent_minus +\'%"; ></div></div>\' + vote_plus  +  \' за, \' + vote_minus + \' против (\' + vote_rating + \')\';
		}
	</script>
	
	<div id="main-top">ЦИТАТЫ: Редактировать цитату</div>

	<form enctype="multipart/form-data" method="POST" action="http://'.$site.'/admin/com/quote/itemupdate/'.$item_id.'/">	
		
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Категория</td>
			<td>
			<select size="1" name="section">';
			section($item_section_id);
			echo'
			</select>
			</td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Автор:</td>
			<td>
			<select size="1" name="author">';
			author($item_author_id);
			echo'
			</select>
			</td>
		</tr>				
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Порядок размещения цитаты</td>
			<td><input type="number" min="0" max="1000000" name="ordering" size="3" value="'.$item_ordering.'"></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Цитата:</td>
			<td><textarea rows="5" name="quote" cols="70" required >'.$item_quote.'</textarea></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Рейтинг</td>
			<td><div id="rating"></div></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Голосов за</td>
			<td><input onClick="rating()" type="number" min="0" max="1000000" id="vote_plus" type="text" name="vote_plus" size="10" value="'.$item_vote_plus.'"></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Голосов против</td>
			<td><input onClick="rating()" type="number" min="0" max="1000000" id="vote_minus" type="text" name="vote_minus" size="10" value="'.$item_vote_minus.'"></td>
		</tr>						
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>			
	</table>
	<br/>
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
	<br/>
	&nbsp;
	</form>	
	<script language="JavaScript">
		rating();
	</script>	
	';

} // конец функции

// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======

// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======
function section($item_section_id) 
{ 
	global $site;
	
	$section_query = mysql_query("SELECT * FROM `com_quote_section` ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");
		
	$section_result = mysql_num_rows($section_query);
		
	if ($section_result > 0) 
	{
		while($s = mysql_fetch_array($section_query)):
			$section_id_tree = $s['id'];	
			$section_title_tree = $s['title'];
	
		   // устанавливаем состояние выбрано для родительского раздела
			if ($item_section_id == $section_id_tree){$selected = "selected";} else {$selected = "";}
			
			echo'<option value="'.$section_id_tree.'" '.$selected.' >'.$section_title_tree.'</option>';			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции


function author($item_author_id) 
{ 
	global $site;
	
	$authors_query = mysql_query("SELECT * FROM `com_quote_authors` ORDER BY `author` ASC") or die ("Невозможно сделать выборку из таблицы - 3");
		
	$authors_result = mysql_num_rows($authors_query);
		
	if ($authors_result > 0) 
	{
		while($a = mysql_fetch_array($authors_query)):
			$authors_id = $a['id'];	
			$authors = $a['author'];
			
		   // устанавливаем состояние выбрано для родительского раздела
			if ($authors_id == $item_author_id){$selected = "selected";} else {$selected = "";}			
	
			echo'<option '.$selected.' value="'.$authors_id.'" >'.$authors.'</option>';			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции


?>