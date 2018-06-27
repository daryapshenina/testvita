<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/article/admin/section/section.css');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{	
		class_name = "contextmenu_article_section";
		var contextmenu_article_section = [
			["admin/com/article/itemadd", "contextmenu_add", "Добавить статью"],
			["admin/com/article/itemup", "contextmenu_up", "Вверх"],
			["admin/com/article/itemdown", "contextmenu_down", "Вниз"],
			["admin/com/article/itempub", "contextmenu_pub", "Опубликовать"],
			["admin/com/article/itemunpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/article/itemdelete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_article_section);
	});
</script>
');

$section_id = intval($admin_d4);
$page_nav = intval($admin_d5);


function a_com()
{ 
	global $site, $section_id, $page_nav;

// Находим наименование раздела
	$sectionsql = mysql_query("SELECT * FROM com_article_section WHERE id = $section_id") or die ("Невозможно сделать выборку из таблицы - 1");	

	$resulttov = mysql_num_rows($sectionsql);

	if ($resulttov > 0) 
	{	
		while($m = mysql_fetch_array($sectionsql)):
			$section_title = $m['title'];
		endwhile;
	}


echo '
<h1>'.$section_title.'</h1>
<table class="admin_table_2">
	<tr>
		<td style="width:200px;"><a class="section_add" href="/admin/com/article/sectionadd/'.$section_id.'">Добавить раздел</a></td>
		<td style="width:200px;"><a class="item_add" href="/admin/com/article/itemadd/'.$section_id.'">Добавить статью</a></td>
		<td>&nbsp;</td>		
	</tr>
</table>	
';	
	
item($section_id);


} // конец функции a_com


// ####### ФУНКЦИИ ##########################################################################################
// ======= ФУНКЦИЯ ВЫВОДА СТАТЕЙ ============================================================================

function item($section_id) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
	global $site, $page_nav;
		
	// вывод настроек
	$settings_num = mysql_query("SELECT * FROM `com_article_settings`") or die ("Невозможно сделать выборку из таблицы - 11");
	while($m = mysql_fetch_array($settings_num)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parameter = $m['parametr'];
		
		// количество статей на странице раздела 
		if ($setting_name == "quantity")
		{
			$quantity = $setting_parameter; // сколько страниц выводить
		} 
	endwhile;
	
	
	$pq = ($page_nav-1)*$quantity;
	if ($pq < 0){$pq = 0;}	
	
	echo'<div class="menu_body">';
	$itemsql = mysql_query("SELECT * FROM `com_article_item` WHERE `section` = '$section_id' ORDER BY `ordering` ASC LIMIT $pq,$quantity") or die ("Невозможно сделать выборку из таблицы - 3");	

	$resulttov = mysql_num_rows($itemsql);

	if ($resulttov > 0) 
		{
		// выводит заголовок	
		echo'
			<table class="admin_table even_odd">
				<tr>
					<th style="width:50px;"></th>
					<th>Статьи</th>			
					<th style="width:50px;" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</th>
				</tr>
		';
	
		while($m = mysql_fetch_array($itemsql)):
			$item_id = $m['id'];	
			$item_pub = $m['pub'];	
			$item_ordering = $m['ordering'];
			$item_title = $m['title'];					
		
			// --- условия публикации ---
			if ($item_pub == "1") {
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$classmenu = "menu_pub";
				}
				else {
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$classmenu = "menu_unpub";
				}
			
			echo'
				<tr>
					<td class="contextmenu_article_section" data-id="'.$item_id.'">'.$item_ordering.'</td>
					<td class="contextmenu_article_section" data-id="'.$item_id.'"><a class="sitemenuitem '.$classmenu.'" id="'.$item_id.'" name="'.$section_id .'" href="/admin/com/article/itemedit/'.$item_id.'"  title = "редактировать статью" >'.$item_title.'</a></td>							
					<td class="contextmenu_article_section" data-id="'.$item_id.'">'.$pub_x.'</td>
				</tr>				
			';			
		endwhile;
		
		echo'</table></div>';		
		
		
		// ----- НАВИГАЦИЯ -----		
		// определяем общее количество статей
		$page_num_sql = mysql_query("SELECT * FROM `com_article_item` WHERE `section` = '$section_id'") or die ("Невозможно сделать выборку из таблицы - 12");	

		$page_num_result = mysql_num_rows($page_num_sql);
		
		$kol_page_nav = ceil($page_num_result/$quantity); // количество страниц навигации = количество статей / статей на страницу - округляем в большую сторону

		if ($kol_page_nav > 1) // если колитчество страниц > 1 - выводим навигацию
		{		
			echo '<br/>
			<div align="center">
			<table border="0" cellpadding="0" style="border-collapse: collapse">
				<tr>
					<td>
					<div class="navbg"><div class="navpage-str">Страницы:</div>
			';							
			
			if ($page_nav < 1){$page_nav = 1;}
			
			for ($i = 1; $i <= $kol_page_nav; $i++) 
			{
				if ($i == $page_nav)
				{
					echo '<div class="navpage-active">'.$i.'</div>';
				}
				else 
				{
					echo '<div class="navpage"><a href="/admin/com/article/section/'.$section_id.'/'.$i.'">'.$i.'</a></div>';
				}
			}
				echo '</div>
					  </td>
				</tr>
			</table>
			</div>';
		}
		// ----- / навигация -----	
			
	} // $resulttov > 0
	else {echo '<div style="padding: 10px">Раздел пустой, статьи отсутствуют</div>';}

	
} // конец функции вывода статей


?>