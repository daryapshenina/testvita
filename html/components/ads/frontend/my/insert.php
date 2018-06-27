<?php
defined('AUTH') or die('Restricted access');

include_once($root."/classes/Auth.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/components/ads/frontend/my/image_resize.php');

if(isset($_POST["cancel"]))
{
	Header ("Location: /ads/my");
	exit;
}

if(!Auth::check())
{
	Header ("Location: /ads/my");
	exit;
}

$user_id = Auth::check();

$section_id = intval($_POST['section']);
$title_input = trim(htmlspecialchars(strip_tags($_POST['title'])));
$content_input = trim(htmlspecialchars(strip_tags($_POST['content'])));


// Обрезаем текст по словам.
if(mb_strlen($title_input) > 70) $title = mb_substr($title_input, 0, mb_strrpos(mb_substr($title_input, 0, 70), ' '));
	else $title = $title_input;
if(mb_strlen($content_input) > 70) $content = mb_substr($content_input, 0, mb_strrpos(mb_substr($content_input, 0, 1000), ' '));
	else $content = $content_input;


$stmt_ads_insert = $db->prepare("
	INSERT INTO com_ads_item SET
	user_id = :user_id,
	section = :section,
	title = :title,
	content = :content,
	image = 0,
	price = 0,
	date_c = :date_c,
	options = '',
	pub = 1
");

$stmt_ads_insert->execute(array(
	'user_id' => $user_id,
	'section' => $section_id,
	'title' => $title,
	'content' => $content,
	'date_c' => date("Y-m-d H:i:s")
));


$ads_id = $db->lastInsertId();

// Обработка изображения
$image = ads_image_resize($user_id, $ads_id);
if($image == 1)
{
	$stmt_update = $db->prepare("UPDATE com_ads_item SET image = 1 WHERE id = :id");
	$stmt_update->execute(array('id' => $ads_id));	
}


Header ("Location: /ads/my"); exit;

?>