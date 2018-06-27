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
	$sectionsql = mysql_query("SELECT * FROM `com_shop_section` WHERE `id` = '$section_id'") or die ("Невозможно сделать выборку из таблицы - 1");	

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
	$('a.sitemenuitem').contextMenu('myMenu2', {
    	bindings: {
          'itemadd': function(t) {
          top.location.href='http://$site/admin/com/shop/itemadd/'+t.name; 
          },			
          'itemedit': function(t) {
          top.location.href='http://$site/admin/com/shop/itemedit/'+t.id; 
          },
          'itemup': function(t) {
          top.location.href='http://$site/admin/com/shop/itemup/'+t.id; 
          },
          'itemdown': function(t) {
          top.location.href='http://$site/admin/com/shop/itemdown/'+t.id; 
          },		  
          'itempub': function(t) {
          top.location.href='http://$site/admin/com/shop/itempub/'+t.id; 
          },
          'itemunpub': function(t) { 
          top.location.href='http://$site/admin/com/shop/itemunpub/'+t.id; 
          }, 
          'itemdelete': function(t) {
          top.location.href='http://$site/admin/com/shop/itemdelete/'+t.id;  
          } 
        }
	}); 
});
</script>
";

echo '	
<table id="main-top-tab">
	<tr>
		<td class="imshop"><span class="desctitle">Интернет-магазин / раздел: </span><span class="sectiontitle">'.$section_title.'</span></td>
		<td class="addsection"><a href="http://'.$site.'/admin/com/shop/sectionadd/'.$section_id.'/'.$menu_t.'">Добавить раздел</a></td>
		<td class="additem"><a href="http://'.$site.'/admin/com/shop/itemadd/'.$section_id.'/'.$menu_t.'">Добавить товар</a></td>
	</tr>
</table>	
';	
	
tovar($section_id);


// Контекстное меню

echo "
  <div class=\"contextMenu\" id=\"myMenu2\">
    <ul>
	  <li id=\"itemadd\"><img src=\"http://".$site."/administrator/tmp/images/addpage.png\" /> Добавить товар</li>	 
      <li id=\"itemedit\"><img src=\"http://".$site."/administrator/tmp/images/edit.png\" /> Редактировать товар</li>	  
      <li id=\"itemup\"><img src=\"http://".$site."/administrator/tmp/images/up.png\" /> Вверх</li>
      <li id=\"itemdown\"><img src=\"http://".$site."/administrator/tmp/images/down.png\" /> Вниз</li>	  
      <li id=\"itempub\"><img src=\"http://".$site."/administrator/tmp/images/p-pub.gif\" /> Опубликовать</li>
      <li id=\"itemunpub\"><img src=\"http://".$site."/administrator/tmp/images/p-unpub.gif\" /> Скрыть</li>
      <li id=\"itemdelete\"><img src=\"http://".$site."/administrator/tmp/images/delete.png\" /> Удалить</li>
    </ul>
  </div>  
";


} // конец функции a_com


// ####### ФУНКЦИИ ##########################################################################################
// ======= ФУНКЦИЯ ВЫВОДА ТОВАРОВ ===========================================================================

function tovar($section_id) // $i = 0 начальный уровень меню, $lvl - уровень меню
	{ 
	global $site, $menu_t, $page_nav;
		
	// вывод настроек
	$settings_num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 11");
	while($m = mysql_fetch_array($settings_num)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parameter = $m['parametr'];
		
		// количество товаров на странице раздела 
		if ($setting_name == "quantity")
		{
			$quantity = $setting_parameter; // сколько страниц выводить
		} 
	endwhile;
	
	$pq = ($page_nav-1)*$quantity;
	if ($pq < 0){$pq = 0;}

	echo'<div class="menu_body">';
	$tovarsql = mysql_query("SELECT * FROM `com_shop_item` WHERE `section` = '$section_id' ORDER BY `ordering` ASC LIMIT $pq,$quantity") or die ("Невозможно сделать выборку из таблицы - 3");	

	$resulttov = mysql_num_rows($tovarsql);

	if ($resulttov > 0) 
		{
		// выводит заголовок	
		echo'
			<table class="w100_bs1 menuheader">
				<tr>
					<td class="cell-v" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
					<td  class="cell-title" >Наименование раздела / товара</td>
					<td  class="cell-price" >Тип</td>			
					<td  class="cell-pub" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">
					Пб.</td>
				</tr>
			</table>
		';	
		
		echo '<table class="w100_bs1">';
	
		while($m = mysql_fetch_array($tovarsql)):
			$tovar_id = $m['id'];	
			$tovar_pub = $m['pub'];	
			$tovar_parent = $m['parent'];
			$tovar_ordering = $m['ordering'];
			$tovar_title = $m['title'];
			$tovar_price = $m['price'];		
			$tovar_description = $m['description'];
			$tovar_photo = $m['photo'];	
			$tovar_photobig = $m['photobig'];						
		
			// --- условия публикации ---
			if ($tovar_pub == "1") {
				$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$classmenu = "menu_pub";
				}
				else {
				$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$classmenu = "menu_unpub";
				}
			
			echo'
				<tr>
					<td class="cell-v">'.$tovar_ordering.'</td>
					<td class="cell-title"><a class="sitemenuitem '.$classmenu.'" id="'.$tovar_id.'" name="'.$section_id.'/'.$menu_t.'" href="http://'.$site.'/admin/com/shop/itemedit/'.$tovar_id.'"  title = "выводит товар" >'.$tovar_title.'</a></td>	
					<td class="cell-price"><a class="sitemenuitem '.$classmenu.'" href="http://'.$site.'/admin/com/shop/itemedit/'.$tovar_id.'" >'.$tovar_price.' руб.</a></td>						
					<td class="cell-pub">'.$pub_x.'</td>
				</tr>			
			';			
		endwhile;
		
		echo'</table></div>';		
		
		
		// ----- НАВИГАЦИЯ -----		
		// определяем общее количество товаров
		$tovar_num_sql = "SELECT * FROM `com_shop_item` WHERE `section` = '$section_id'";	
		
		$tovar_num_query = mysql_query($tovar_num_sql) or die ("Невозможно сделать выборку из таблицы - 12");	

		$result_tov_num = mysql_num_rows($tovar_num_query);
		
		$kol_page_nav = ceil($result_tov_num/$quantity); // количество страниц навигации = количество товаров / товаров на страницу - округляем в большую сторону

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
					echo '<div class="navpage"><a href="http://'.$site.'/admin/com/shop/section/'.$section_id.'/'.$menu_t.'/'.$i.'">'.$i.'</a></div>';
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
	else {echo '<div style="padding: 10px">Раздел пустой, товары отсутствуют</div>';}

	
} // конец функции вывода товара


?>