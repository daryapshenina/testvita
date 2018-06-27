<?php
// DAN 2012
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');


function a_com()
{ 
	global $site, $section_id, $menu_t, $page_nav;

// Контекстное меню
echo " 
<script type=\"text/javascript\">
$(document).ready(function() {
	$('a.quoteauthor').contextMenu('menuquote3', {
    	bindings: {
          'addauthor': function(t) {
          top.location.href='http://$site/admin/com/quote/authoradd'; 
          },			
          'editauthor': function(t) {
          top.location.href='http://$site/admin/com/quote/authoredit/'+t.name; 
          },
          'addquote': function(t) {
          top.location.href='http://$site/admin/com/quote/itemadd/'+t.name; 
          },		  
          'deleteauthor': function(t) {
          top.location.href='http://$site/admin/com/quote/authordelete/'+t.name;  
          } 
        }
	}); 
});
</script>
";

echo '	
<table id="main-top-tab">
	<tr>
		<td class="quote_all_title"><span class="quote-desctitle">ЦИТАТЫ / Авторы: </span><span class="quote-sectiontitle">'.$section_title.'</span></td>
		<td class="quote_addsection"><a href="http://'.$site.'/admin/com/quote/authoradd/'.$section_id.'/'.$menu_t.'">Добавить автора</a></td>
	</tr>
</table>	
';	

// Находим авторов
	$authors_query = mysql_query("SELECT * FROM `com_quote_authors` ORDER BY `author` ASC") or die ("Невозможно сделать выборку из таблицы - 1");	

	$result = mysql_num_rows($authors_query);
	
	echo '<div class="margin-left-right-10">';

	if ($result > 0) 
		{	
			while($m = mysql_fetch_array($authors_query)):
				$id = $m['id'];			
				$author = $m['author'];
				
				
				// --- НАХОДИМ ДЛЯ АВТОРА КОЛИЧЕСТВО ЦИТАТ ---
				$quote_sql = "SELECT * FROM `com_quote_item` WHERE `author_id` = '$id'";
				$quote_query = mysql_query($quote_sql) or die ("Невозможно сделать выборку из таблицы - 1");	
				
				$quote_result = mysql_num_rows($quote_query);				
				// --- / находим для автора количество цитат / ---
				
				
				// берём первую букву
				$bukva = mb_substr($author, 0, 1, 'utf-8'); 
				
				// переводим в верхний регистр
				$bukva = mb_strtoupper($bukva);
				
				if (!isset($alphabet[$bukva]))
				{
					$alphabet[$bukva] = $bukva;
					echo '
						<div>&nbsp;</div><div>
						<font color="#00aa00" size="5">'.$alphabet[$bukva].'</font></div>
					';
				}
				
				echo'
					<div><a class="quoteauthor"  name="'.$id .'" href="http://'.$site.'/admin/com/quote/authoredit/'.$id.'">'.$author.'</a> <font color="#999999">('.$quote_result.')</font></div>
				';					
			endwhile;
		}

	echo '</div>';

// Контекстное меню

echo "
  <div class=\"contextMenu\" id=\"menuquote3\">
    <ul>
      <li id=\"editauthor\"><img src=\"http://".$site."/administrator/tmp/images/edit.png\" /> Редактировать</li>
	  <li id=\"addauthor\"><img src=\"http://".$site."/administrator/tmp/images/addpage.png\" /> Добавить автора</li>
	  <li id=\"addquote\"><img src=\"http://".$site."/administrator/tmp/images/addpage.png\" /> Добавить цитату</li>
      <li id=\"deleteauthor\"><img src=\"http://".$site."/administrator/tmp/images/delete.png\" /> Удалить</li>
    </ul>
  </div>  
";


} // конец функции a_com

?>