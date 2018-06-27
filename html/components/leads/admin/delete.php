<?php
defined('AUTH') or die('Restricted access');

$id = $d[4];
if($d[4] == 'all')
{
	$stmt_delete = $db->exec("DELETE FROM com_leads");
}
else 
{
	$stmt_delete = $db->prepare("DELETE FROM com_leads WHERE id = :id");
	$stmt_delete->execute(array('id' => $id));
}

if($d[5] == 'ajax'){exit;}
else {Header ('Location: /admin/com/leads'); exit;}
?>