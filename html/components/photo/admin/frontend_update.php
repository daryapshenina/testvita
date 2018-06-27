<?php
defined('AUTH') or die('Restricted access');

$id = $_POST["id"];
$type = $_POST["type"];
$data = $_POST["data"];

if($type == 'title')
{
	$data = strip_tags($data);
	$stmt = $db->prepare("UPDATE com_photo_section SET title = :title WHERE id = :id");
	$stmt->execute(array('title' => $data, 'id' => $id));		
}

if($type == 'text_top')
{
	$stmt = $db->prepare("UPDATE com_photo_section SET text_top = :text_top WHERE id = :id");
	$stmt->execute(array('text_top' => $data, 'id' => $id));		
}

if($type == 'text_bottom')
{
	$stmt = $db->prepare("UPDATE com_photo_section SET text_bottom = :text_bottom WHERE id = :id");
	$stmt->execute(array('text_bottom' => $data, 'id' => $id));		
}

echo 'ok';

?>