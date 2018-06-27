<?php
defined('AUTH') or die('Restricted access');

$id = $_POST["id"];
$type = $_POST["type"];
$data = $_POST["data"];

if($type == 'title')
{
	$data = strip_tags($data);
	$stmt = $db->prepare("UPDATE modules SET title = :title WHERE module = 'form' AND id = :id");
	$stmt->execute(array('title' => $data, 'id' => $id));		
}

if($type == 'content')
{
	$stmt = $db->prepare("UPDATE modules SET content_2 = :content_2 WHERE module = 'form' AND id = :id");
	$stmt->execute(array('content_2' => $data, 'id' => $id));		
}

if($type == 'field_1')
{
	$stmt = $db->prepare("UPDATE modules SET p2 = :p2 WHERE module = 'form' AND id = :id");
	$stmt->execute(array('p2' => $data, 'id' => $id));		
}

if($type == 'field_2')
{
	$stmt = $db->prepare("UPDATE modules SET p4 = :p4 WHERE module = 'form' AND id = :id");
	$stmt->execute(array('p4' => $data, 'id' => $id));		
}

if($type == 'field_3')
{
	$stmt = $db->prepare("UPDATE modules SET p6 = :p6 WHERE module = 'form' AND id = :id");
	$stmt->execute(array('p6' => $data, 'id' => $id));		
}

echo 'ok';

?>