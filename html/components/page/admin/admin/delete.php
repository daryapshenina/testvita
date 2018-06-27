<?php
// Удаление пункта меню и страницы

defined('AUTH') or die('Restricted access');

// id_com
$id = intval($d[4]); 

$stmt_menu = $db->prepare("SELECT id FROM menu WHERE parent = (SELECT id FROM menu WHERE component = 'page' AND id_com = :id)");
$stmt_menu->execute(array('id'=>$id));


if($stmt_menu->rowCount() > 0) // Есть дочерние пункты меню
{
	function a_com()
	{ 	
		echo '<div align="center"><h2><font color="#FF0000">Данный пункт меню невозможно удалить, т.к. он содержит подпункты. 
		<br/>Удалите сначала подпункты.</font></h2></div>';
	}	
}
else
{
	$stmt_delete = $db->prepare("DELETE FROM com_page WHERE id = :id");
	$stmt_delete->execute(array('id'=>$id));

	$stmt_delete = $db->prepare("DELETE FROM menu WHERE component = 'page' AND id_com = :id");
	$stmt_delete->execute(array('id'=>$id));

	$stmt_delete = $db->prepare("DELETE FROM url WHERE url = :url");
	$stmt_delete->execute(array('url'=>'page/'.$id));	

	Header ("Location: /admin/site/");
	exit;
}

?>