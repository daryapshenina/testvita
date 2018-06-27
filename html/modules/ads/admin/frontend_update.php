<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT'].'/modules/ads/classes/modAds.php';

$id = intval($_POST["id"]);
$type = $_POST["type"];
$data = $_POST["data"];

if($type == 'mod_title')
{
	$stmt_select = $db->prepare("SELECT content FROM modules WHERE id = :id");
	$stmt_select->execute(array('id' => $id));
	$m_s = unserialize($stmt_select->fetchColumn());
	
	$m_s->title = trim(strip_tags($data));
	$s = serialize($m_s);

	$stmt = $db->prepare("UPDATE modules SET content = :content WHERE module = 'ads' AND id = :id");
	$stmt->execute(array('content' => $s, 'id' => $id));		
}

echo 'ok';

?>