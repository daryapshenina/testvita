<?php
// DAN 2012
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');	
$id = intval($admin_d4);

function a_com()
{ 
	global $site, $id;	
	
	$author_sql = "SELECT * FROM `com_quote_authors` WHERE `id` = '$id' LIMIT 1";
	$author_query = mysql_query($author_sql) or die ("Невозможно сделать выборку из таблицы - 1");	
	
	while($m = mysql_fetch_array($author_query)):
		$id = $m['id'];
		$author = $m['author'];
		$description = $m['description'];		
	endwhile;	
	
	echo '
	
	<div id="main-top">ЦИТАТЫ: Добавить автора</div>

	<form enctype="multipart/form-data" method="POST" action="http://'.$site.'/admin/com/quote/authorupdate/'.$id.'">	
	
	<table class="main_tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="70" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="70" height="25">Автор:</td>
			<td><input type="text" name="author" size="30" value="'.$author.'"></td>
		</tr>
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="70" height="25">О авторе:</td>
			<td>&nbsp;</td>
		</tr>			
	</table>
	<textarea name="editor1">'.$description.'</textarea>
	<script type="text/javascript">	
		CKEDITOR.replace( \'editor1\',
			{	        
				height: \'500px\',
				filebrowserBrowseUrl : \'http://'.$site.'/administrator/plugins/browser/dan_browser.php\',
			}); 	
	</script>	
	<br/>
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
	<br/>
	&nbsp;
	</form>	
	';
	
} // конец функции
?>