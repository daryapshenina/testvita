<?php
defined('AUTH') or die('Restricted access');
include_once($root."/classes/Auth.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/components/ads/admin/item/image_resize.php');

$section_id = intval($_POST['section']);
$title_input = trim(htmlspecialchars(strip_tags($_POST['title'])));
$content_input = trim(htmlspecialchars(strip_tags($_POST['content'])));
$date = $_POST['date'];
$ads_id = intval($d[5]);

if(isset($_POST["bt_none"]))
{
	Header ("Location: /admin/com/ads/section/".$section_id); exit;
}

if(!validateDate($date)) exit('Ошибка даты или времени');

// Обрезаем текст по словам.
if(mb_strlen($title_input) > 70) $title = mb_substr($title_input, 0, mb_strrpos(mb_substr($title_input, 0, 70), ' '));
	else $title = $title_input;
if(mb_strlen($content_input) > 70) $content = mb_substr($content_input, 0, mb_strrpos(mb_substr($content_input, 0, 1000), ' '));
	else $content = $content_input;

$stmt_update = $db->prepare("
	UPDATE com_ads_item SET
	section = :section,
	title = :title,
	content = :content,
	date_c = :date_c
	WHERE id = :id
");

$stmt_update->execute(array(
	'section' => $section_id,
	'title' => $title,
	'content' => $content,
	'date_c' => $date,
	'id' => $ads_id,
));


// Обработка изображения
$stmt_select = $db->prepare("SELECT user_id FROM com_ads_item WHERE id = :id");
$stmt_select->execute(array('id' => $ads_id));
$user_id = $stmt_select->fetchColumn();

$image = ads_image_resize($user_id, $ads_id);
if($image == 1)
{
	$stmt_update = $db->prepare("UPDATE com_ads_item SET image = 1 WHERE id = :id");
	$stmt_update->execute(array('id' => $ads_id));
}

if(isset($_POST["bt_prim"])) Header ("Location: /admin/com/ads/item/edit/".$ads_id); 
	else Header ("Location: /admin/com/ads/section/".$section_id); 
exit;


function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>