<?php
// DAN 2012
// Добавляем новую страницу

defined('AUTH') or die('Restricted access');

// определяем id раздела
$section_id = intval($admin_d4);

// определяем тип меню
$menu_t = intval($admin_d5);	

// Перед тем как добавить цитату - проверяем - есть ли разделы 
$number_sections = mysql_query("SELECT * FROM com_quote_section") or die ("Невозможно сделать выборку из таблицы - 1");
$result_number_sections = mysql_num_rows($number_sections);
if ($result_number_sections < 1)
{
	function a_com()
	{
		echo 
		'
		<div id="main-top">Отсутствуют разделы</div>
		<div style="padding: 10px">Отсутствуют разделы. Необходимо завести хотя бы один раздел.</div>
		';		
	}
}
else 
{	
	function a_com()
	{ 
		global $site, $section_id_parent, $section_id_ordering; 	
		
		echo '
		<div id="main-top">ЦИТАТЫ: Добавить цитату</div>
	
		<form enctype="multipart/form-data" method="POST" action="http://'.$site.'/admin/com/quote/iteminsert/">	
		
		<table class="main_tab">
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Категория</td>
				<td>
				<select size="1" name="section">';
				section();
				echo'
				</select>
				</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Автор:</td>
				<td>
				<select size="1" name="author">';
				author();
				echo'
				</select>
				</td>
			</tr>				
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Порядок размещения цитаты</td>
				<td><input type="text" name="ordering" size="3" value="'.$section_id_ordering.'"></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Цитата:</td>
				<td><textarea rows="5" name="quote" cols="70">«»</textarea></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>			
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;
		</form>	
		';
	
	} // конец функции

} // конец проверки существования разделов

// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА РАЗДЕЛОВ =======
function section() 
{ 
	global $site, $section_id;
	
	$section_query = mysql_query("SELECT * FROM `com_quote_section` ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");
		
	$section_result = mysql_num_rows($section_query);
		
	if ($section_result > 0) 
	{
		while($s = mysql_fetch_array($section_query)):
			$section_id_tree = $s['id'];	
			$section_title_tree = $s['title'];
	
		   // устанавливаем состояние выбрано для родительского раздела
			if ($section_id == $section_id_tree){$selected = "selected";} else {$selected = "";}		
			echo'<option value="'.$section_id_tree.'" '.$selected.' >'.$section_title_tree.'</option>';			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции


function author() 
{ 
	global $site;
	
	$authors_query = mysql_query("SELECT * FROM `com_quote_authors` ORDER BY `author` ASC") or die ("Невозможно сделать выборку из таблицы - 3");
		
	$authors_result = mysql_num_rows($authors_query);
		
	if ($authors_result > 0) 
	{
		while($a = mysql_fetch_array($authors_query)):
			$authors_id = $a['id'];	
			$authors = $a['author'];
	
			echo'<option value="'.$authors_id.'" >'.$authors.'</option>';			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции

?>