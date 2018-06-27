<?php
// DAN 2010
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $site, $d; 	
	
	echo '
	<div id="main-top">ИНТЕРНЕТ - МАГАЗИН: Создать раздел</div>

	<form method="POST" action="http://'.$site.'/admin/com/shop/insertsection/">	
	
	<table class="main-tab">
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="20">&nbsp;</td>		
			<td width="200" height="25">Название раздела</td>
			<td><input type="text" name="title" size="50"></td>
		</tr>		
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Опубликовать раздел</td>
			<td><input type="checkbox" name="pub" value="1" checked></td>
		</tr>						
		<tr>
			<td width="20">&nbsp;</td>			
			<td width="200" height="25">Описание раздела:</td>
			<td>&nbsp;</td>
		</tr>		
	</table>
	<textarea name="editor1"></textarea>
	
	<script type="text/javascript">	
		CKEDITOR.replace( \'editor1\',
			{	        
				height: \'200px\',
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