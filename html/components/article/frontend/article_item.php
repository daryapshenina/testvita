<?php
// выводит содержимое статьи
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

if($frontend_edit == 1){$head->addFile('/components/article/frontend/item_edit.js');}

// Получаем с верхних уровней
// $sns_auth_check
// $sns_users_id

$article_item_id = intval($d[2]);

// увеличиваем просмотры на единицу
$query_update_views = "UPDATE `com_article_item` SET  `views` = `views` + 1 WHERE `id` = '$article_item_id'" ;

$sql_views = mysql_query($query_update_views) or die ("Невозможно обновить данные 1");

// ======= Проверка существования статьи =======================================================
$itemsql = mysql_query("SELECT * FROM `com_article_item` WHERE `id` = '$article_item_id' AND `pub` = '1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");

$result_item = mysql_num_rows($itemsql); // количество статей

// если статей нет
if ($result_item == "0")
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}

while($m = mysql_fetch_array($itemsql)):
	$article_item_id = $m['id'];
	$article_item_section = $m['section'];
	$article_item_pub = $m['pub'];
	$article_item_ordering = $m['ordering'];
	$article_item_title = $m['title'];
	$article_item_introtext = $m['introtext'];
	$article_item_fulltext = $m['fulltext'];
	$article_item_views = $m['views'];
	$article_item_rating = $m['rating'];
	$article_item_vote_plus = $m['vote_plus'];
	$article_item_vote_minus = $m['vote_minus'];
	$article_item_cdate = $m['cdate'];
	$article_item_lastip = $m['lastip'];
	$tag_title = $m['tag_title'];
	$tag_description = $m['tag_description'];
endwhile;

// Если тег тайтл не заполнен то $page_title + $site_title;
$page_title = $article_item_title;


// --- Вывод настроек раздела ---

$section_sql = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$article_item_section' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

$resultsecitem = mysql_num_rows($section_sql); // количество разделов

while($s = mysql_fetch_array($section_sql)):
	$section_display_date = $s['display_date'];
	$section_display_vote = $s['display_vote'];
	$section_display_views = $s['display_views'];
	$section_text_output = $s['text_output'];
	$section_comments = $s['comments'];
endwhile;

// --- / Вывод настроек раздела ---




// ####### Вывод статей ###############################################################
function component()
{
	global $site, $sns_auth_check, $sns_users_id, $article_item_id, $article_item_section, $article_item_pub,
	$article_item_ordering, $article_item_title, $article_item_introtext, $article_item_fulltext, $article_item_views,
	$article_item_rating, $article_item_vote_plus, $article_item_vote_minus, $article_item_cdate, $article_item_lastip,
	$resultsecitem, $section_display_date, $section_display_vote, $section_display_views, $section_text_output,
	$section_comments, $comments_out, $frontend_edit;

		$article_item_cdate_d = substr($article_item_cdate, 0, 10);

		$cdate = explode("-",$article_item_cdate_d);
		$cd['01'] = LANG_ARTICLE_JANUARY;
		$cd['02'] = LANG_ARTICLE_FEBRUARY;
		$cd['03'] = LANG_ARTICLE_MARCH;
		$cd['04'] = LANG_ARTICLE_APRIL;
		$cd['05'] = LANG_ARTICLE_MAY;
		$cd['06'] = LANG_ARTICLE_JUNE;
		$cd['07'] = LANG_ARTICLE_JULY;
		$cd['08'] = LANG_ARTICLE_AUGUST;
		$cd['09'] = LANG_ARTICLE_SEPTEMBER;
		$cd['10'] = LANG_ARTICLE_OCTOBER;
		$cd['11'] = LANG_ARTICLE_NOVEMBER;
		$cd['12'] = LANG_ARTICLE_DECEMBER;

		// вывод вводного текста и основного
		if ($section_text_output == "1"){$text_output = $article_item_introtext.$article_item_fulltext;} else {$text_output = $article_item_fulltext;}

		// если существуют голоса, только тогда назначаем рейтинг
		if ($article_item_vote_plus > 0 || $article_item_vote_minus > 0)
		{
			$article_item_toolbar_plus = $article_item_rating;
			$article_item_toolbar_minus = 100 - $article_item_rating;
			$avp = '<div class="article_vb_plus"></div>'; // если есть голоса - отображаем
			$avm = '<div class="article_vb_minus"></div>'; // если есть голоса - отображаем
		}
		else
		{
			$article_item_toolbar_plus = 50;
			$article_item_toolbar_minus = 50;
			$avp = "";
			$avm = "";
		}

		// свойства - отображение сортировки по рейтингу (по количеством голосов за и против)
		if ($section_display_vote == 1)
		{
			$prop_rating = '
			<div class="article_prop_2">
				<div class="article_prop_2">'.LANG_ARTICLE_VOTE.':</div>
				<a href="#" onclick="vote(\''.$article_item_id.'\',\'plus\')" class="zp_bt" title="'.LANG_ARTICLE_VOTE_FOR.'" >
					<img border="0" src="/components/article/frontend/tmp/images/za_bt.png" />
				</a>
				<div id="votestatus" class="article_prop_vb">
					<div class="article_prop_2_left" style="width: '.$article_item_toolbar_plus.'%">
						<div class="rt_vb" title="'.LANG_ARTICLE_RATING_PERCENT_VORE_FOR.'" >'.$article_item_rating.'%</div>
						<div class="article_prop_votingbar" title="'.LANG_ARTICLE_TOTAL_VOTE_FOR.'" >'.$avp.'</div>
						<div class="rt_vb" title="'.LANG_ARTICLE_TOTAL_VOTE_FOR.'" >'.$article_item_vote_plus.'</div>
					</div>
					<div class="article_prop_2_right" style="width: '.$article_item_toolbar_minus.'%">
						<div class="rt_vb" title="'.LANG_ARTICLE_TOTAL_VOTE_AGAINST.'"></div>
						<div class="article_prop_votingbar" title="'.LANG_ARTICLE_TOTAL_VOTE_AGAINST.'" >'.$avm.'</div>
						<div class="rt_vb" title="'.LANG_ARTICLE_TOTAL_VOTE_AGAINST.'" >'.$article_item_vote_minus.'</div>
					</div>
				</div>
				<a href="#" onclick="vote(\''.$article_item_id.'\',\'minus\')"  class="zp_bt" title="'.LANG_ARTICLE_VOTE_AGAINST.'" >
					<img border="0" src="/components/article/frontend/tmp/images/protiv_bt.png" />
				</a>
			</div>';
		}

		// свойства - отображение сортировки по количеству просмотров
		if ($section_display_views == 1)
		{
			$prop_views = '<div class="article_prop_2 article_prop_views" title="'.LANG_ARTICLE_VIEWS_2.'"><img border="0" src="/components/article/frontend/tmp/images/view_small.png" /></div><div class="article_prop" title="'.LANG_ARTICLE_VIEWS_2.'">'.$article_item_views.'</div>';
		}

		$article_item_cdate_d = substr($article_item_cdate, 0, 10);

		// свойства - отображение сортировки по дате
		if ($section_display_date == 1)
		{
			$prop_date = '<div class="article_prop article_prop_date" title="'.LANG_ARTICLE_DATE_OF_CREATION.'">'.intval($cdate[2]).' '.$cd[$cdate[1]].' '.$cdate[0].'</div>';
		}

		//------- отображение свойств и голосования -------
		$toolbar = $prop_rating.$prop_views.$prop_date;
		//------- / отображение свойств и голосования -------

		// ------- Голосование на AJAX -------
		echo '
		<script type="text/javascript">

		function getXmlHttp(){
		  var xmlhttp;
		  try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		  } catch (e) {
			try {
			  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
			  xmlhttp = false;
			}
		  }
		  if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
			xmlhttp = new XMLHttpRequest();
		  }
		  return xmlhttp;
		}

		function vote(id,vote)
		{
			var req = getXmlHttp()
			req.onreadystatechange = function()
			{
				if (req.readyState == 4)
				{
					if(req.status == 200)
					{
						document.getElementById("votestatus").innerHTML = req.responseText;
					}
				}

			}

			req.open(\'GET\', \'/components/article/frontend/article_vote.php?id=\' + id + \'&vote=\' + vote, true);
			req.send(null);
			document.getElementById("votestatus").innerHTML = "<div align=\"center\"><img class=\"loading_img\" src=\"/components/article/frontend/tmp/images/loading.gif\" /></div>";
		}
		</script>';
		// ------- / Голосование на AJAX -------



		// ======= КОММЕНТАРИИ ==============================================================
		// Если комментарии включены
		if($section_comments == 1)
		{
			$comments_out .= '<hr class="article_comments_hr" />';
			$comments_out .= '<div>&nbsp;</div>';

			// ------- Проверка авторизации пользователя ------------------------------------
			if ($sns_auth_check == 'true')
			{
				// Профиль
				$query_profile = mysql_query("SELECT * FROM `sns_users_profile` WHERE `psw_id` = '$sns_users_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
				while($n = mysql_fetch_array($query_profile)):
					$name = $n['name'];
					$family = $n['family'];
					$sex = $n['sex'];
					$country = $n['country'];
					$directory = $n['directory'];
					$photo = $n['photo'];
				endwhile;

				if(!isset($photo) || $photo == '')
				{
					if ($sex == 1)
					{
						$photo_out = '<a target="_blank" href="/profile/'.$sns_users_id.'"><img src="/profile/frontend/tmp/images/man.png" border="0" alt="'.$name.' '.$family.'"></a>';
					}
					elseif ($sex == 2)
					{
						$photo_out = '<a target="_blank" href="/profile/'.$sns_users_id.'"><img src="/profile/frontend/tmp/images/woman.png" border="0" alt="'.$name.' '.$family.'"></a>';
					}
					else
					{
						$photo_out = '<a target="_blank" href="/profile/'.$sns_users_id.'"><img src="/profile/frontend/tmp/images/nophoto.png" border="0" alt="'.$name.' '.$family.'"></a>';
					}
				}
				else
				{
					$photo_out = '<a target="_blank" href="/profile/'.$sns_users_id.'"><img src="/components/photo/users/'.$directory.'/my_photo_preview.jpg" border="0" alt="'.$name.' '.$family.'"></a>';
				}

				$comments_out .= '
				<div class="article_comments_title">'.LANG_ARTICLE_WRITE_COMMENT.'</div>
				<table border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
					<tr>
						<td width="130" height="120">
							<div>'.$photo_out.'</div>
						</td>
						<td valign="top">
							<form method="post" action="/comments/add/aticle/'.$article_item_id.'">
								<div><b><a target="_blank" href="/profile/'.$sns_users_id.'">'.$name.' '.$family.'</a></b></div>
								<textarea rows="4" name="sns_comments" cols="50"></textarea><br/>
								<input type="submit" value="'.LANG_ARTICLE_SEND.'" name="send">
							</form>
						</td>
					</tr>
				</table>
				';
			}
			else
			{
				$comments_out .= '<div><b><a href="/login/auth">'.LANG_ARTICLE_LOG_IN_1.'</a></b>, '.LANG_ARTICLE_LOG_IN_2.'</div>';
			}
			// ------- / проверка авторизации пользователя / --------------------------------

			$comments_out .= '
			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<div class="article_comments_title">'.LANG_ARTICLE_ALL_COMMENTS.'</div>
			';

			$sns_article_parent = 0;
			// начальная зависимость комментариев и начальный уровень
			comments_out($sns_article_parent, 0);

		}

		// ======= / коммегнтарии / ==========================================================




		// ======= Модальное окно =========================================================================

		if ($sns_auth_check == 'true')
		{
			echo '
			<div id="sns_popup" class="sns_overlay">
				<form method="post" action="/comments/add/aticle/'.$article_item_id.'">
					<div><b>'.LANG_ARTICLE_YOUR_COMMENT.':</b></div>
					<div><textarea rows="4" name="sns_comments" cols="50"></textarea></div>
					<div id="sns_comments_id"></div>
					<input type="submit" value="'.LANG_ARTICLE_SEND.'" name="send">
				</form>
			</div>
			';
		}
		else
		{
			echo '
			<div id="sns_popup" class="sns_overlay">
				<div>&nbsp;</div>
				<div><b><a href="/login/auth">'.LANG_ARTICLE_LOG_IN_1.'</a></b>, '.LANG_ARTICLE_LOG_IN_2.'</div>
				<div id="sns_comments_id"></div>
				<div>&nbsp;</div>
			</div>

			';
		}



		echo '
			<script type="text/javascript">
			$(document).ready(function(){

				//When you click on a link with class of sns_poplight and the href starts with a #
				$(\'a.sns_poplight[href^=#]\').click(function() {
					var popID = $(this).attr(\'rel\'); //Get Popup Name
					var popURL = $(this).attr(\'href\'); //Get Popup href to define size

					//Pull Query & Variables from href URL
					var query= popURL.split(\'?\');
					var dim= query[1].split(\'&\');
					var popWidth = dim[0].split(\'=\')[1]; //Gets the first query string value
					var article_id = dim[1].split(\'=\')[1]; // получаем занчение второй переменной

					document.getElementById("sns_comments_id").innerHTML = \'<input type="hidden" name="sns_comments_id"  value="\' + article_id + \'"/>\';

					//Добавить кнопку "Закрыть" в наше окно, прописываете прямой путь к картинке кнопки
					$(\'#\' + popID).fadeIn().css({ \'width\': Number( popWidth ) }).prepend(\'<a href="#" class="close"><span class="sns_btn_close" title="Закрыть окно" ></span></a>\');

					//Определяет запас на выравнивание по центру (по вертикали по горизонтали)мы добавим 80px к высоте / ширине, значение полей вокруг содержимого (padding) и ширину границы устанавливаем в CSS
					var popMargTop = ($(\'#\' + popID).height() + 80) / 2;
					var popMargLeft = ($(\'#\' + popID).width() + 80) / 2;

					//Применяем отступы в всплывающем окне
					$(\'#\' + popID).css({
						\'margin-top\' : -popMargTop,
						\'margin-left\' : -popMargLeft
					});

					//Фон слоя затемнения
					$(\'body\').append(\'<div id="fade"></div>\'); //Добавляем слой затемнения.
					$(\'#fade\').css({\'filter\' : \'alpha(opacity=80)\'}).fadeIn(); //Постепенное исчезание слоя - .css({\'filter\' : \'alpha(opacity=80)\'}) используется для фиксации в IE, фильтр для устранения бага тупого IE

					return false;
				});


				//Закрыть всплывающее окно и слой затемнения
				$(\'a.close, #fade\').live(\'click\', function() { //При нажатии рядом, окно и слой затемнения закрываются
					$(\'#fade , .sns_overlay\').fadeOut(function() {
						$(\'#fade, a.close\').remove();
				}); //fade them both out

					return false;
				});
			});
			</script>
		';
		// ======= / модальное окно / =========================================================================

		// Подключаем шаблон
		include("components/article/frontend/tmp/item_tmp.php");

} // конец функции component





// ####### Рекурсия уровней комментариев ###################################################
function comments_out($sns_article_parent, $lvl)
{
	global $site, $article_item_id, $comments_out;
	$lvl++;

	// находим комментарии для этой статьи
	$sns_comments_sql = mysql_query("SELECT * FROM `sns_comments` WHERE `item_id` = '$article_item_id' AND `item_type` = 'article' AND `parent` = '$sns_article_parent' ") or die ("Невозможно сделать выборку из таблицы - 2");

	$sns_comments_result = mysql_num_rows($sns_comments_sql); // количество количество комментарие

	if ($sns_comments_result > 0)
	{
		while($n = mysql_fetch_array($sns_comments_sql)):
			$sns_comment_id = $n['id'];
			$sns_comment_article_id = $n['article_id'];
			$sns_comment_parent = $n['parent'];
			$sns_comment_comments = $n['comments'];
			$sns_comment_active = $n['active'];
			$sns_comment_date = $n['date'];
			$sns_comment_user_id = $n['user_id'];

			$sns_comment_date_arr = split('-',$sns_comment_date);
			$sns_comment_date_year = $sns_comment_date_arr[0];
			$sns_comment_date_month = intval($sns_comment_date_arr[1]);
			$sns_comment_date_day = intval($sns_comment_date_arr[2]);

			$sns_comment_date_month_arr[1] = LANG_ARTICLE_JANUARY;
			$sns_comment_date_month_arr[2] = LANG_ARTICLE_FEBRUARY;
			$sns_comment_date_month_arr[3] = LANG_ARTICLE_MARCH;
			$sns_comment_date_month_arr[4] = LANG_ARTICLE_APRIL;
			$sns_comment_date_month_arr[5] = LANG_ARTICLE_MAY;
			$sns_comment_date_month_arr[6] = LANG_ARTICLE_JUNE;
			$sns_comment_date_month_arr[7] = LANG_ARTICLE_JULY;
			$sns_comment_date_month_arr[8] = LANG_ARTICLE_AUGUST;
			$sns_comment_date_month_arr[9] = LANG_ARTICLE_SEPTEMBER;
			$sns_comment_date_month_arr[10] = LANG_ARTICLE_OCTOBER;
			$sns_comment_date_month_arr[11] = LANG_ARTICLE_NOVEMBER;
			$sns_comment_date_month_arr[12] = LANG_ARTICLE_DECEMBER;

			$sns_comment_date_month = $sns_comment_date_month_arr[$sns_comment_date_month];

			// находим пользователя
			$sns_profile_sql = mysql_query("SELECT * FROM `sns_users_profile` WHERE `psw_id` = '$sns_comment_user_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 3");

			while($n = mysql_fetch_array($sns_profile_sql)):
				$sns_user_name = $n['name'];
				$sns_user_family = $n['family'];
				$sns_user_sex = $n['sex'];
				$sns_user_country = $n['country'];
				$sns_user_directory = $n['directory'];
				$sns_user_photo = $n['photo'];
			endwhile;

			if(!isset($sns_user_photo) || $sns_user_photo == '')
			{
				if ($sns_user_sex == 1)
				{
					$sns_photo_out = '<a target="_blank" href="/profile/'.$sns_comment_user_id.'"><img src="/components/profile/frontend/tmp/images/man.png" class="sns_comments_users_photo" border="0" width="60" height="60" alt="'.$sns_user_name.' '.$sns_user_family.'"></a>';
				}
				elseif ($sns_user_sex == 2)
				{
					$sns_photo_out = '<a target="_blank" href="/profile/'.$sns_comment_user_id.'"><img src="/components/profile/frontend/tmp/images/woman.png" class="sns_comments_users_photo" border="0" width="60" height="60" alt="'.$sns_user_name.' '.$sns_user_family.'"></a>';
				}
				else
				{
					$sns_photo_out = '<a target="_blank" href="/profile/'.$sns_comment_user_id.'"><img src="/components/profile/frontend/tmp/images/nophoto.png" class="sns_comments_users_photo" border="0" width="60" height="60" alt="'.$sns_user_name.' '.$sns_user_family.'"></a>';
				}
			}
			else
			{
				$sns_photo_out = '<a target="_blank" href="/profile/'.$sns_comment_user_id.'"><img src="/components/photo/users/'.$sns_user_directory.'/my_photo_preview.jpg" class="sns_comments_users_photo" border="0" width="60" height="60" alt="'.$sns_user_name.' '.$sns_user_family.'"></a>';
			}
			// отступ слева
			$sns_comments_margin_left = 40*($lvl - 1);

			if ($sns_comment_active == 1)
			{
				$comments_out .= '
				<div class="sns_comments_item" style="margin-left:'.$sns_comments_margin_left.'px;">
					'.$sns_photo_out.'
					<a target="_blank" href="/profile/'.$sns_comment_user_id.'">'.$sns_user_name.' '.$sns_user_family.'</a><span class="sns_comments_date">'.$sns_comment_date_day.' '.$sns_comment_date_month.' '.$sns_comment_date_year.'</span><br/>
					'.$sns_comment_comments.'<br/>
					<b><a href="#?w=500&cid='.$sns_comment_id.'" rel="sns_popup" class="sns_poplight">'.LANG_ARTICLE_REPLY.'</a></b>
				</div>
				';
			}
			else
			{
				$comments_out .= '<div class="sns_comments_item_block" style="margin-left:'.$sns_comments_margin_left.'px;">'.LANG_ARTICLE_COMMENT_IS_CLOSED.'</div>';
			}

			comments_out($sns_comment_id, $lvl);

		endwhile;
	}
}

?>