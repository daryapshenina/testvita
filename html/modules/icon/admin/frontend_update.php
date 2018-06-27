<?php
defined('AUTH') or die('Restricted access');

$id = $_POST["id"];
$type = $_POST["type"];
$data = $_POST["data"];

if($type == 'title')
{
	$data = strip_tags($data);
	$stmt = $db->prepare("UPDATE modules SET title = :title WHERE module = 'icon' AND id = :id");
	$stmt->execute(array('title' => $data, 'id' => $id));		
}

if($type == 'content_2')
{
	$stmt = $db->prepare("UPDATE modules SET content_2 = :content_2 WHERE module = 'icon' AND id = :id");
	$stmt->execute(array('content_2' => $data, 'id' => $id));		
}

echo 'ok';

?>