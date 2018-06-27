<?php
// DAN 2012
// вставляет данные голосования в таблицу и возвращает результат

include("../../../config.php");

$id = $_GET["id"];
$vote = $_GET["vote"];

// сбрасываем данные
$vote_plus = 0;
$vote_minu = 0;

if ($vote == "plus"){$set = "`vote_plus` = `vote_plus` + 1"; $vote_plus = 1;}
elseif ($vote == "minus"){$set = "`vote_minus` = `vote_minus` + 1"; $vote_minus = 1;} 
else {
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found"); 
	include("../../../404.php");
	exit;	
}

// определяем IP	
$ip=GetUserIP();

// === MySQL ======================================================

$conn = mysql_connect ($host, $user, $passwd) or die ("Соединение с MySQL не установлено!"); 
mysql_select_db($dbname) OR die ("Соединение с базой данных не установлено");
mysql_query('SET CHARACTER SET utf8');


// ======= Вывод данных до обновлённого рейтинга ==================================================================
$prev_sql = mysql_query("SELECT * FROM `com_quote_item` WHERE `id` = '$id'") or die ("Невозможно сделать выборку из таблицы - 1");	

$prev_item = mysql_num_rows($prev_sql); // количество статей

// если статей нет
if ($prev_item == "0") 
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found"); 
	include("../../../404.php");
	exit;	
}

while($p = mysql_fetch_array($prev_sql)):
	$prev_vote_plus = $p['vote_plus'];
	$prev_vote_minus = $p['vote_minus'];
	$prev_lastip = $p['lastip'];	
endwhile;

// голоса старые из базы + полученный голос
$rating_vote_plus = $prev_vote_plus + $vote_plus;
$rating_vote_minus = $prev_vote_minus + $vote_minus;

// пересчитываем рейтин
$item_rating = intval(100*$rating_vote_plus/($rating_vote_plus + $rating_vote_minus));

// ======= Проверка IP и cookies =======

// cookies
$dan_c = "";
// создание переменной 5za_quote_*** для cookies
$dan_c = $site."_quote_".$id;

if ($ip == $prev_lastip || (isset($_COOKIE["$dan_c"])))
{
	echo '
		<div id="votestatus" style="line-height	:17px; text-align :center;">Вы уже голосовали за эту <br/>цитату раньше.</div>
	';	
}
else // если пользователь ещё не голосовал за эту статью
{
	// устанавливаем куки
	SetCookie($dan_c,$id,time()+36000000); 
	
	// Обновляем данные в таблице "com_quote_item" 
	$query_update_quote_item = "UPDATE `com_quote_item` SET ".$set.", `rating` = '$item_rating', `lastip` = '$ip' WHERE `id` = '$id' LIMIT 1;";
	
	$sql_quote_item = mysql_query($query_update_quote_item) or die ("Невозможно обновить данные");	
	
	
	// ======= Пересчитываем рейтинг =======================================================
	$itemsql = mysql_query("SELECT * FROM `com_quote_item` WHERE `id` = '$id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");	
	
	$result_item = mysql_num_rows($itemsql); // количество статей
	
	// если статей нет
	if ($result_item == "0") 
	{
		// выдаём страницу ошибки 404.html
		header("HTTP/1.0 404 Not Found"); 
		include("../../../404.php");
		exit;	
	}
	
	while($m = mysql_fetch_array($itemsql)):
		$item_vote_plus = $m['vote_plus'];
		$item_vote_minus = $m['vote_minus'];
		$item_lastip = $m['lastip'];	
	endwhile;
	
	$item_toolbar_plus = $item_rating;
	$item_toolbar_minus = (100 - $item_rating);
	
	echo '
		<div id="votestatus_'.$id.'" class="quote_prop_vb">
			<div class="quote_prop_2_left" style="width: '.$item_toolbar_plus.'%">
				<div class="rt_vb" title="Рейтинг (% голосов за)" >'.$item_rating.'%</div>						
				<div class="quote_prop_votingbar" title="голосов за" >						
					<div class="quote_vb_plus"></div>
				</div>
				<div class="rt_vb" title="Голосов за" >'.$item_vote_plus.'</div>							
			</div>
			<div class="quote_prop_2_right" style="width: '.$item_toolbar_minus.'%">
				<div class="rt_vb" title="голосов против"></div>					
				<div class="quote_prop_votingbar" title="голосов против" >				
					<div class="quote_vb_minus"></div>
				</div>
				<div class="rt_vb" title="Голосов за" >'.$item_vote_minus.'</div>						
			</div>
		</div>
	';
}


// ####### Определение IP- адреса ##########################################################

function GetUserIP()
{    
	if (isset($_SERVER['HTTP_CLIENT_IP']))    
	{      
		$ip = $_SERVER['HTTP_CLIENT_IP'];     
	}    
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))    
	{       
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];     
	}    
	else 
	{       
		$ip = $_SERVER['REMOTE_ADDR'];     
	}     
return($ip);
} 

?>