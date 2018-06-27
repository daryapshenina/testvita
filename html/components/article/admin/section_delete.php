<?php
// DAN обновление - январь 2014
// Удаление раздела

defined('AUTH') or die('Restricted access');

$id_com = intval($admin_d4);


// находим id_menu по id_com
$id_com_sql = "SELECT * FROM `menu` WHERE `component` = 'article' AND `p1` <> 'all' AND `id_com` = '$id_com' LIMIT 1";

$id_com_query = mysql_query($id_com_sql) or die ("Невозможно сделать выборку из таблицы - 1");

while($m = mysql_fetch_array($id_com_query)):
	$menu_id = $m['id'];
	$menu_type = $m['menu_type'];
endwhile;


// проверяем - есть ли подразделы внутри раздела
$sections_sql = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'article' AND `p1` = 'section' AND `parent` = '$menu_id'") or die ("Невозможно сделать выборку из таблицы - 2");

$result_sections = mysql_num_rows($sections_sql);

// проверяем - есть ли статьи внутри раздела
$articles_sql = mysql_query("SELECT * FROM `com_article_item` WHERE `section` = '$id_com'") or die ("Невозможно сделать выборку из таблицы - 3");

$result_articles = mysql_num_rows($articles_sql);

$result = $result_sections + $result_articles;

if ($result > 0)
{
	function a_com()
	{
		global $sections_sql, $articles_sql, $result, $result_sections, $result_articles;
		echo '
			<div id="main-top">РАЗДЕЛ НЕ ПУСТОЙ!</div>
			<div style="padding: 10px">
			';

		if ($result_sections > 0) // существуют подпункты
		{
			echo'
				<div>Прежде чем удалить раздел - необходимо удалить (или переместить в другой	 раздел) вложенные подразделы!</div>
				<div>Раздел содержит подразделы:</div>
				<div>
					<ul>
			';
					while($m = mysql_fetch_array($sections_sql)):
						$section_name = $m['name'];
						echo '<li class="red" >'.$section_name.'</li>';
					endwhile;
			echo'
					</ul>
				</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
			';
		}

		if ($result_articles > 0) // существуют статьи
		{
			echo'
					<div>Прежде чем удалить раздел - необходимо удалить (или переместить в другой	 раздел) вложенные статьи!</div>
					<div>Раздел содержит статьи:</div>
					<div>
						<ul>
				';
						while($m = mysql_fetch_array($articles_sql)):
							$article_title = $m['title'];
							echo '<li class="red" >'.$article_title.'</li>';
						endwhile;
			echo'
						</ul>
					</div>
				</div>
			';
		}
	}
}

else {
	// удаляем пункт меню
	mysql_query("DELETE FROM `menu` WHERE `id_com`='$id_com' AND `component` = 'article' AND `main` <> '1' ");

	// удаляем раздел
	mysql_query("DELETE FROM `com_article_section` WHERE `id`='$id_com'") or die ("Невозможно сделать выборку из таблицы - 5");

	// удаляем sef
	mysql_query("DELETE FROM `url` WHERE `url`='article/section/$id_com'") or die ("Удаление не возможно - 5");

	Header ("Location: /admin"); exit;
}

?>
