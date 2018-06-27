<?php
// DAN 2012
// Удаление раздела

defined('AUTH') or die('Restricted access');

$id_com = intval($admin_d4); 

// определяем тип меню
$menu_t = intval($admin_d5);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------
// сбрасываем значения
$menu_top_selected = "";
$menu_left_selected = "";

if (!isset($menu_t) || $menu_t == "0" || $menu_t == "1")
{
	$menu_type = "top";	
}
if ($menu_t == "2")
{
	$menu_type = "left";	
}

// находим id_menu по id_com
$id_com_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'quote' AND `p1` <> 'all' AND `id_com` = '$id_com' LIMIT 1";

$id_com_query = mysql_query($id_com_sql) or die ("Невозможно сделать выборку из таблицы - 1");

while($m = mysql_fetch_array($id_com_query)):
	$menu_id = $m['id'];
endwhile;


// проверяем - есть ли подразделы внутри раздела
$sections_sql = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'quote' AND `p1` = 'section' AND `parent` = '$menu_id'") or die ("Невозможно сделать выборку из таблицы - 2");

$result_sections = mysql_num_rows($sections_sql);

// проверяем - есть ли статьи внутри раздела
$quotes_sql = mysql_query("SELECT * FROM `com_quote_item` WHERE `section_id` = '$id_com'") or die ("Невозможно сделать выборку из таблицы - 3");

$result_quotes = mysql_num_rows($quotes_sql);

$result = $result_sections + $result_quotes;

if ($result > 0)
{
	function a_com()
	{
		global $sections_sql, $quotes_sql, $result, $result_sections, $result_quotes;
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
		
		if ($result_quotes > 0) // существуют статьи
		{		
			echo'
					<div>Прежде чем удалить раздел - необходимо удалить (или переместить в другой	 раздел) вложенные статьи!</div>
					<div>Раздел содержит статьи:</div>	
					<div>
						<ul>
				';
						while($m = mysql_fetch_array($quotes_sql)):
							$quote_title = $m['title'];	
							echo '<li class="red" >'.$quote_title.'</li>';
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
	mysql_query("DELETE FROM `menu` WHERE `id_com`='$id_com' AND `component` = 'quote' AND `main` <> '1' ");
	
	// удаляем раздел
	mysql_query("DELETE FROM `com_quote_section` WHERE `id`='$id_com'") or die ("Невозможно сделать выборку из таблицы - 5");	
		
	Header ("Location: http://".$site."/admin"); exit;
}

?>