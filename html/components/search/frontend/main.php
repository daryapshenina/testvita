<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

function component()
{
	global $db, $root, $domain, $url_arr, $d;

	// Настройки
	$com_search_issPage = 30; // Выдач на страницу

	if(isset($_POST['search']) != "" AND $d[1] == "") // главная страница компонента поиск, без пагинации
	{
		// Удаляем все кроме букв и цифр, а так же впереди и взади удаляем пробелы
		$searchString = trim(strip_tags($_POST['search']));
	}
	elseif ($d[1] != "")
	{
		// Удаляем все кроме букв и цифр, а так же впереди и взади удаляем пробелы
		$searchString_get = hex2bin($d[1]);
		$searchString = trim(strip_tags($searchString_get));
	}
	else
	{
		$searchString = '';
	}

	$searchStringInput = htmlspecialchars($searchString);
	$searchString = preg_replace('/[^a-zа-яё0-9\s]/ui', ' ', $searchString);

	echo '
		<form name="com_form_search" method="POST" action="/search">
			<div class="com_search_main">
				<table border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
					<tr>
						<td><input class="com_search_input" type="text" name="search" value="'.$searchStringInput.'" autocomplete="off" placeholder="Введите поисковой запрос и нажмите enter или кнопку поиска" /></td>
						<td width="30"><div id="com_search_submit" class="com_search_submit" title="Найти!"></div></td>
					</tr>
				</table>
			</div>
		</form>
	';

	// Поисковой запрос получен?
	if((isset($_POST['search']) && $_POST['search'] != ""  ) || $d[1] != "")
	{
		// Массив содержащий поисковую выдачу
		$arraySearch = array();
		// Массив таблиц участвующих в поиске
		$arrayTableQuery[0] = array('com_page', 'title, text', 'text');
		$arrayTableQuery[1] = array('com_shop_item', 'title, intro_text, full_text', 'intro_text', 'full_text');
		//$arrayTableQuery[1] = array('com_shop_item i JOIN com_shop_char c ON c.item_id = i.id', 'title, intro_text, full_text, value', 'intro_text', 'full_text');
		$arrayTableQuery[2] = array('com_article_item', 'title, introtext, `fulltext`', 'introtext', 'fulltext');
		// Формируем запрос, Если пришел post то его подставляем, иначе get

		$searchStringQuery = ""; // Окончательная строка запроса
		if(mb_strlen($searchString, 'UTF-8') > 2)
		{
			// Разбиваем строку на массив
			$arrayString = preg_split("/[\s]+/", $searchString);
			// Перебиваем весь массив строк вырезая окончания и добавляя в окончательную строку запроса
			foreach($arrayString as $arrayStringNumber => $arrayStringSingle)
			{
				// Если длина слова меньше 2 то оно не участвует в поиске
				if(mb_strlen($arrayStringSingle, 'UTF-8') > 2)
				{
					if(mb_strlen($arrayStringSingle, 'UTF-8') > 4) // чтобы не кастрировать короткие слова
					{
						$com_search_simgle_edit = preg_replace('/(а|я|о|е|ы|и|ов|ев|у|ам|ям|ой|ою|ей|ый|ий|ею|ом|ем|ю|ами|ями|ах|ях|ые)$/ui', "", $arrayStringSingle); // Обработанное слово
					}
					else
					{
						$com_search_simgle_edit = $arrayStringSingle;
					}

					$arrayString[$arrayStringNumber] = $com_search_simgle_edit;
					$searchStringQuery = $searchStringQuery.'*'.$com_search_simgle_edit;
				}
			}
			$searchStringQuery = $searchStringQuery.'*';

			// Перебираем все таблицы указанные в массиве
			foreach($arrayTableQuery as $arrayTableQuerySingle)
			{
				if($arrayTableQuerySingle[0] == 'com_page' || $arrayTableQuerySingle[0] == 'com_shop_char'){$pub_out = '';} else {$pub_out = "AND pub = '1'";}

				// Достаем из бд
				$stmt = $db->query("
					SELECT *,
						MATCH($arrayTableQuerySingle[1])
						AGAINST ('".$searchStringQuery."' IN BOOLEAN MODE) AS sort
					FROM $arrayTableQuerySingle[0]
					WHERE
						MATCH($arrayTableQuerySingle[1])
						AGAINST ('".$searchStringQuery."' IN BOOLEAN MODE)
					".$pub_out."
					ORDER BY sort DESC LIMIT 0, 1000
				");

				// Перебираем полученные данные и помещаем в общий массив
				$i = count($arraySearch); // Смотрим индекс последнего элемента
				while($m = $stmt->fetch())
				{
					// Подрезаем длину текста и удаляем теги (если текст 2 существует то и его режем и добавляем)
					$page_title = strip_tags($m['title']);
					$page_text = mb_substr($m[''.$arrayTableQuerySingle[2].''], 0, 300, "utf-8");
					$page_text = strip_tags($page_text);

					if(isset($arrayTableQuerySingle[3]) && $arrayTableQuerySingle[3] != '')
					{
						$page_text_2 = mb_substr($m[''.$arrayTableQuerySingle[3].''], 0, 300, "utf-8");
						$page_text_2 = strip_tags($page_text_2);
					}
					else
					{
						$page_text_2 = '';
					}

					// Выделяем жирным найденное
					foreach($arrayString as $arrayStringSingle)
					{
						$arrayStringSingle = htmlspecialchars($arrayStringSingle);
						$page_title = str_replace($arrayStringSingle, '<b>'.$arrayStringSingle.'</b>', $page_title);
						$page_text = str_replace($arrayStringSingle, '<b>'.$arrayStringSingle.'</b>', $page_text);
						$page_text_2 = str_replace($arrayStringSingle, '<b>'.$arrayStringSingle.'</b>', $page_text_2);
					}

					$arraySearch[$i][0] = $m['sort'];
					$arraySearch[$i][1] = $arrayTableQuerySingle[0];
					$arraySearch[$i][2] = $m['id'];
					$arraySearch[$i][3] = $page_title;
					$arraySearch[$i][4] = $page_text;
					$arraySearch[$i][5] = $page_text_2;
					$i++;
				}
				$page_title = '';
				$page_text = '';
				$page_text_2 = '';
			}

			// Выводим все данные из общего массива если внутри что то есть
			if(count($arraySearch) > 0)
			{
				// Сортируем массив поисковой выдачи
				rsort($arraySearch);
				// Проверяем на правильность данных
				if(intval($d[2]) <= 0){$com_search_now = 1;}else{$com_search_now = $d[2];}
				// Считаем с какого номера выводить посты
				$com_search_numpage = $com_search_now * $com_search_issPage - $com_search_issPage;
				// До какого поста выводим
				$com_search_toPrint = $com_search_numpage + $com_search_issPage;
				// Считаем сколько получилось страниц
				$com_search_allPage = ceil(count($arraySearch) / $com_search_issPage);

				echo '<div class="com_search_iss">';

				// Перебираем с требуемой страницы до требуемой
				for($i = $com_search_numpage;$i < $com_search_toPrint;$i++)
				{
					if (isset($arraySearch[$i][1]) && $arraySearch[$i][1] != '')
					{
						// Тело
						echo '<div class="com_search_iss_single">';

							// Заголовок с ссылкой
							if($arraySearch[$i][1] == 'com_page'){$com_searh_link = '/page/';}
							elseif($arraySearch[$i][1] == 'com_shop_item'){$com_searh_link = '/shop/item/';}
							elseif($arraySearch[$i][1] == 'com_article_item'){$com_searh_link = '/article/item/';}

							// Ссылка на материал
							$com_searh_link_url = $com_searh_link.$arraySearch[$i][2];

							// убираем один символ / в начале
							$p_qs = substr($com_searh_link_url, 1);

							// если есть в массиве ЧПУ - заменяем
							if(isset($url_arr[$p_qs]) && $url_arr[$p_qs] != '')
							{
								$com_searh_link_url = '/'.$url_arr[$p_qs];
							}

							// Вырезаем символ \ и заменяем на пробел
							$arraySearch[$i][3] = preg_replace("/\\\/", " ", $arraySearch[$i][3]);

							echo '
								<div class="com_search_number">'.($i+1).'</div>
								<div>
									<a href="'.$com_searh_link_url.'" class="com_search_iss_single_title" target="_blank">'.$arraySearch[$i][3].'</a>
									<div class="com_search_iss_single_text">'.$arraySearch[$i][4].'</div>
									<div class="com_search_iss_single_fulltext">'.$arraySearch[$i][5].'</div>
								</div>
							';

						echo '</div>';
					}
				}

				echo '</div>';

				// Выводим навигацию если страниц больше 1
				if($com_search_allPage > 1)
				{
					echo '<div class="com_search_nav">';
					for($n = 1; $n <= $com_search_allPage;$n++)
					{
						// Если это текущая страница
						if($n == $com_search_now)
						{
							echo '<a class="com_search_navl_act">'.$n.'</a>';
						}
						else
						{
							$searchString_get = bin2hex($searchString);
							echo '<a href="/search/'.$searchString_get.'/'.$n.'" class="com_search_navl">'.$n.'</a>';
						}
					}
					echo '</div>';
				}
			}
			else
			{
				echo '<div class="com_search_notF">Ничего не найдено...</div>';
			}
		}
		else
		{
			echo '<div class="com_search_notF">Запрос должен быть не меньше трех символов</div>';
		}
	}
}


$head->addFile('/components/search/frontend/search.js');
$head->addFile('/components/search/frontend/tmp/style.css');

?>