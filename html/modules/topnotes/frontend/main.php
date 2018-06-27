<?php
// DAN 2012
// Модуль топ записей пользователей
defined('AUTH') or die('Restricted access');

$modules_title_topnotes = $modules_title;
$modules_titlepub_topnotes = $modules_titlepub;
$suf_topnotes = $modules_module_csssuf;

$quantity = $modules_p1;
$length = $modules_p2;

if ($modules_pub == "1")
{
	// верх модуля
	echo'<div class="mod-main'.$suf_topnotes.'">
	<div class="mod-top'.$suf_topnotes.'">';

	// Заголовок модуля
	if ($modules_titlepub_topnotes == "1")
	{
		echo '<div class="mod-title'.$suf_topnotes.'">'.$modules_title_topnotes.'</div>';
	}

	echo'</div>';

	// Достаем лайки и сортируем по дате
	$newnotes_query_likes = mysql_query("SELECT `id`, `note_id` FROM `sns_notes_likes` ORDER BY `date_like` desc LIMIT $quantity") or die ("Невозможно сделать выборку из таблицы - 1");

	// количество лайков
	$count_likes = mysql_num_rows($newnotes_query_likes);

	// средняя часть
	echo'
		<div class="mod-mid'.$suf_newnotes.'">
			<div class="mod-padding'.$suf_newnotes.'">
				';

	// Если лайки есть
	if($count_likes > 0)
	{
		while($l = mysql_fetch_array($newnotes_query_likes)):
			$newnotes_likes_id = $l['id'];
			$newnotes_likes_note_id = $l['note_id'];

			// Достаем записи пользователей по айди лайков
			$newnotes_query = mysql_query("SELECT * FROM `sns_notes` WHERE `pub` = '1' and `id` = '$newnotes_likes_note_id'") or die ("Невозможно сделать выборку из таблицы - 111");

			$n = mysql_fetch_array($newnotes_query);
			
			$newnotes_id = $n['id'];
			$newnotes_psw_id = $n['psw_id'];
			$newnotes_title = $n['title'];
			$newnotes_content = $n['content'];
			$newnotes_date = $n['date'];
			
			// выводим дату из бд и меняем её вид
			$newnotes_cdate_d = substr($newnotes_date, 0, 10);
			$cdate = explode("-",$newnotes_cdate_d);
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
			
			$newnotes_content = strip_tags($newnotes_content);

			// длина комментария
			if (strlen($newnotes_content) > $length)
			{
				$newnotes_content = substr($newnotes_content, 0, $length);
				$newnotes_content = $newnotes_content.'...>>>';
			}

			echo '
			<div class="mod-newcomments-date">'.$cdate[2].' '.$cd[$cdate[1]].' '.$cdate[0].'</div>
			
			<div><a target="_blank" class="mod-newcomments-title" href="http://'.$site.'/notes/'.$newnotes_psw_id.'#note_'.$newnotes_id.'" >'.$newnotes_title.'</a></div>
			
			<div>'.$newnotes_content.'</div><div>&nbsp;</div>
			';

		endwhile;
	}
	else
	{
		echo 'Новых записей нет';
	}

		   echo'
			</div>
		</div>
		<div class="mod-bot'.$suf_newnotes.'"></div></div>
	';
}

?>