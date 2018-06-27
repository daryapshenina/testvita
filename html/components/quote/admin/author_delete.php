<?php
// DAN 2012
// Удаление раздела

defined('AUTH') or die('Restricted access');

$id = intval($admin_d4); 

// проверяем - есть ли цитаты у этого автора
$quote_sql = mysql_query("SELECT * FROM `com_quote_item` WHERE `author_id` = '$id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

$quote_result = mysql_num_rows($quote_sql);

if ($quote_result > 0)
{
	function a_com()
	{
		global $id;
		
		echo '
			<div id="main-top">НЕВОЗМОЖНО УДАЛИТЬ АВТОРА!</div>
			<div style="padding: 10px">У данного автора есть цитаты. Удалить автора, имеющего цитаты невозможно!</div>
			</div>
		';
	}
}

else {
	
	// удаляем автора
	mysql_query("DELETE FROM `com_quote_authors` WHERE `id`='$id'") or die ("Невозможно сделать выборку из таблицы - 2");	
		
	Header ("Location: http://".$site."/admin/com/quote/authors"); exit;
}

?>