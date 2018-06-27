<?php
defined('AUTH') or die('Restricted access');

$ordering_str = $_POST["images_order"];
$section = $_POST["section"];

$ordering_arr = $qs_arr = explode(';', $ordering_str);
$stmt_update = $db->prepare("UPDATE com_photo_items SET ordering = :ordering WHERE id = :id");	

foreach($ordering_arr as $ordering => $id)
{
	$stmt_update->execute(array('ordering' => ($ordering + 1), 'id' => $id));
}

Header ("Location: http://".$site."/admin/com/photo/section/".$section);
exit;
?>