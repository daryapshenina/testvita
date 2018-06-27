<?php
// DAN 2012
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');

$section_id = intval($admin_d4);
$menu_t = intval($admin_d5);
$page_nav = intval($admin_d6);


function a_com()
{ 
	global $site, $section_id, $menu_t, $page_nav;

// Находим наименование раздела
	$sectionsql = mysql_query("SELECT * FROM com_quote_section WHERE id = $section_id") or die ("Невозможно сделать выборку из таблицы - 1");	

	$resulttov = mysql_num_rows($sectionsql);

	if ($resulttov > 0) 
		{	
			while($m = mysql_fetch_array($sectionsql)):
				$section_title = $m['title'];
			endwhile;
		}

// Контекстное меню
echo " 
<script type=\"text/javascript\">
$(document).ready(function() {
	$('a.sitemenuitem').contextMenu('menuquote3', {
    	bindings: {
          'additem': function(t) {
          top.location.href='http://$site/admin/com/quote/itemadd/'+t.name; 
          },			
          'edititem': function(t) {
          top.location.href='http://$site/admin/com/quote/itemedit/'+t.name; 
          },
          'upitem': function(t) {
          top.location.href='http://$site/admin/com/quote/itemup/'+t.name; 
          },
          'downitem': function(t) {
          top.location.href='http://$site/admin/com/quote/itemdown/'+t.name; 
          },		  
          'deleteitem': function(t) {
          top.location.href='http://$site/admin/com/quote/itemdelete/'+t.name;  
          } 
        }
	}); 
});
</script>
";

echo '	
<table id="main-top-tab">
	<tr>
		<td class="quote_all_title"><span class="quote-desctitle">ЦИТАТЫ / раздел: </span><span class="quote-sectiontitle">'.$section_title.'</span></td>
		<td class="quote_addsection"><a href="http://'.$site.'/admin/com/quote/sectionadd/'.$section_id.'/'.$menu_t.'">Добавить раздел</a></td>
		<td class="quote_additem"><a href="http://'.$site.'/admin/com/quote/itemadd/'.$section_id.'/'.$menu_t.'">Добавить цитату</a></td>
	</tr>
</table>	
';	
	
item($section_id);


// Контекстное меню

echo "
  <div class=\"contextMenu\" id=\"menuquote3\">
    <ul>
	  <li id=\"additem\"><img src=\"http://".$site."/administrator/tmp/images/addpage.png\" /> Добавить цитату</li>	 
      <li id=\"edititem\"><img src=\"http://".$site."/administrator/tmp/images/edit.png\" /> Редактировать цитату</li>	  
      <li id=\"upitem\"><img src=\"http://".$site."/administrator/tmp/images/up.png\" /> Вверх</li>
      <li id=\"downitem\"><img src=\"http://".$site."/administrator/tmp/images/down.png\" /> Вниз</li>	  
      <li id=\"deleteitem\"><img src=\"http://".$site."/administrator/tmp/images/delete.png\" /> Удалить</li>
    </ul>
  </div>  
";


} // конец функции a_com


// ####### ФУНКЦИИ ##########################################################################################
// ======= ФУНКЦИЯ ВЫВОДА ЦИТАТ ============================================================================

function item($section_id) // $i = 0 начальный уровень меню, $lvl - уровень меню
	{ 
	global $site, $menu_t, $page_nav;
		
	// вывод настроек
	$settings_num = mysql_query("SELECT * FROM `com_quote_settings`") or die ("Невозможно сделать выборку из таблицы - 11");
	while($m = mysql_fetch_array($settings_num)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parameter = $m['parametr'];
		
		// количество цитат на странице раздела 
		if ($setting_name == "quantity")
		{
			$quantity = $setting_parameter; // сколько страниц выводить
		} 
	endwhile;
	
	echo'<div class="menu_body">';
	
	$item_sql = "SELECT * FROM `com_quote_item` WHERE `section_id` = '$section_id' ORDER BY `ordering` ASC LIMIT $page_nav,$quantity";		
	
	// echo $item_sql;
	
	$item_query = mysql_query($item_sql) or die ("Невозможно сделать выборку из таблицы - 3");	

	$resulttov = mysql_num_rows($item_query);

	if ($resulttov > 0) 
		{
		// выводит заголовок	
		echo'
			<table class="w100_bs1 menuheader">
				<tr>
					<td class="cell-v" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
					<td  class="cell-title" >Цитаты</td>			
					<td  class="quote-cell-author" align="center">Автор цитаты</td>
				</tr>
			</table>
		';	
	
		while($m = mysql_fetch_array($item_query)):
			$item_id = $m['id'];	
			$item_ordering = $m['ordering'];
			$item_quote = $m['quote'];	
			$item_author_id = $m['author_id'];	
			
			// --- НАХОДИМ АВТОРА ЦИТАТЫ ---
			$author_sql = "SELECT * FROM `com_quote_authors` WHERE `id` = '$item_author_id'";
			$author_query = mysql_query($author_sql) or die ("Невозможно сделать выборку из таблицы - 4");		
			
			while($m = mysql_fetch_array($author_query)):
				$id = $m['id'];
				$author = $m['author'];
				$description = $m['description'];		
			endwhile;
			// --- / находим автора цитаты ---
			
			echo'
				<table class="w100_bs1">
					<tr>
						<td class="quote-cell-v">'.$item_ordering.'</td>
						<td class="quote-cell-title"><a class="sitemenuitem"  name="'.$item_id.'" href="http://'.$site.'/admin/com/quote/itemedit/'.$item_id.'"  title = "редактировать цитату" >'.$item_quote.'</a></td>							
						<td class="quote-cell-author"><a href="http://'.$site.'/admin/com/quote/authoredit/'.$id.'"  title = "редактировать автора" >'.$author.'</a></td>
					</tr>
				</table>				
			';			
		endwhile;
		
		echo'</div>';		
		
		
		// ----- НАВИГАЦИЯ -----		
		// определяем общее количество цитат
		$page_num_sql = mysql_query("SELECT * FROM `com_quote_item` WHERE `section_id` = '$section_id'") or die ("Невозможно сделать выборку из таблицы - 12");	

		$result_tov_num = mysql_num_rows($page_num_sql);
		
		$kol_page_nav = ceil($result_tov_num/$quantity); // количество страниц навигации = количество цитат/ цитат на страницу - округляем в большую сторону
		$pn = intval($page_nav/$quantity); // текущая страница - округляем в меньшую сторону

		if ($kol_page_nav > 1) // если колитчество страниц > 1 - выводим навигацию
		{		
			echo '<br/>
			<div align="center">
			<table border="0" cellpadding="0" style="border-collapse: collapse">
				<tr>
					<td>
					<div class="navbg"><div class="navpage-str">Страницы:</div>
			';							
			
			for ($i = 1; $i <= $kol_page_nav; $i++) 
			{
				if (($i-1) == $pn)
				{
					echo '<div class="navpage-active">'.$i.'</div>';
				}
				else 
				{
					echo '<div class="navpage"><a href="http://'.$site.'/admin/com/quote/section/'.$section_id.'/'.$menu_t.'/'.($i-1)*$quantity.'">'.$i.'</a></div>';
				}
			}
				echo '</div>
					  </td>
				</tr>
			</table>
			</div>';
			// ----- / навигация -----
		}
		
	} // $resulttov > 0
	else {echo '<div style="padding: 10px">Раздел пустой, цитаты отсутствуют</div>';}

	
} // конец функции вывода цитат


?>