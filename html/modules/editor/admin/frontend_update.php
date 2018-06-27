<?php
defined('AUTH') or die('Restricted access');

$id = $_POST["id"];
$type = $_POST["type"];
$data = $_POST["data"];

if($type == 'title')
{
	$data = strip_tags($data);
	$stmt = $db->prepare("UPDATE modules SET title = :title WHERE module = 'editor' AND id = :id");
	$stmt->execute(array('title' => $data, 'id' => $id));
}

if($type == 'content')
{
	$stmt = $db->prepare("UPDATE modules SET content = :content WHERE module = 'editor' AND id = :id");
	$stmt->execute(array('content' => $data, 'id' => $id));
}

echo 'ok';

?>