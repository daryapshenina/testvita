<?php
// DAN 2012
// Модуль авторизации
defined('AUTH') or die('Restricted access');

$modules_title_newnotes = $modules_title;
$modules_titlepub_newnotes = $modules_titlepub;
$suf_newnotes = $modules_module_csssuf;

$quantity = $modules_p1;
$length = $modules_p2;

if ($modules_pub == "1")
{
	// верх модуля
	echo'<div class="mod-main'.$suf_newnotes.'">
	<div class="mod-top'.$suf_newnotes.'">';

	// Заголовок модуля
	if ($modules_titlepub_newnotes == "1")
	{
		echo '<div class="mod-title'.$suf_newnotes.'">'.$modules_title_newnotes.'</div>';
	}

	echo'</div>';

	// Записи
	$newnotes_query = mysql_query("SELECT * FROM `sns_notes` WHERE `pub` = '1' ORDER BY `id` desc LIMIT $quantity") or die ("Невозможно сделать выборку из таблицы - 1");

	// количество записей
	$count = mysql_num_rows($newnotes_query);

	// средняя часть
	echo'
		<div class="mod-mid'.$suf_newnotes.'">
			<div class="mod-padding'.$suf_newnotes.'">
				';


	if($count >0)
	{
		while($n = mysql_fetch_array($newnotes_query)):
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

			// длина записи
			if (strlen($newnotes_content) > $length)
			{
				$newnotes_content = substr($newnotes_content, 0, $length);
				$newnotes_content = $newnotes_content.'...>>>';
			}

			echo '
			<div class="mod-newnotes-date">'.$cdate[2].' '.$cd[$cdate[1]].' '.$cdate[0].'</div>

			<div><a target="_blank" class="mod-newnotes-title" href="http://'.$site.'/notes/'.$newnotes_psw_id.'#note_'.$newnotes_id.'" >'.$newnotes_title.'</a></div>

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