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
$id_com_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'shop' AND `id_com` = '$id_com' AND `p1` <> 'all' LIMIT 1";

$id_com_query = mysql_query($id_com_sql) or die ("Невозможно сделать выборку из таблицы - 1");

while($m = mysql_fetch_array($id_com_query)):
	$menu_id = $m['id'];
endwhile;

// проверяем - есть ли подразделы внутри раздела
$sections_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `component` = 'shop' AND `p1` = 'section' AND `parent` = '$menu_id'";

$sections_query = mysql_query($sections_sql) or die ("Невозможно сделать выборку из таблицы - 2");

$result_sections = mysql_num_rows($sections_query);

// проверяем - есть ли товары внутри раздела
$items_sql = mysql_query("SELECT * FROM `com_shop_item` WHERE `section` = '$id_com'") or die ("Невозможно сделать выборку из таблицы - 3");

$result_items = mysql_num_rows($items_sql);

$result = $result_sections + $result_items;

if ($result > 0)
{
	function a_com()
	{
		global $sections_query, $items_sql, $result, $result_sections, $result_items;
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
					while($m = mysql_fetch_array($sections_query)):
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
		
		if ($result_items > 0) // существуют статьи
		{		
			echo'
					<div>Прежде чем удалить раздел - необходимо удалить (или переместить в другой	 раздел) товары внутри раздела!</div>
					<div>Раздел содержит товары:</div>	
					<div>
						<ul>
				';
						while($m = mysql_fetch_array($items_sql)):
							$items_title = $m['title'];	
							echo '<li class="red" >'.$items_title.'</li>';
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
	mysql_query("DELETE FROM `menu` WHERE `id_com`='$id_com' AND `component` = 'shop' AND `main` <> '1' ");
	
	// удаляем раздел
	mysql_query("DELETE FROM `com_shop_section` WHERE `id`='$id_com'") or die ("Невозможно сделать выборку из таблицы - 5");
	
	// удаляем sef
	mysql_query("DELETE FROM `url` WHERE `url`='shop/section/$id_com'") or die ("Удаление не возможно - 5");	
		
	Header ("Location: http://".$site."/admin"); exit;
}

?>