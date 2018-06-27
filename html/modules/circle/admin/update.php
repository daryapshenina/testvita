<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

$title = htmlspecialchars($_POST["title"]);
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
if(isset($_POST["block"])){$block = strip_tags($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
$width_f = intval($_POST["width_fix"]);
$height_f = intval($_POST["height_fix"]);
$padding_w = intval($_POST["padding_w"]);
$padding_h = intval($_POST["padding_h"]);
$effect_a = $_POST["effect_a"];
$content = htmlspecialchars($_POST["content"]);
$content_2 = trim($_POST["text"]);
$font_size = intval($_POST["font_size"]);
$text_color = $_POST["text_color"];
$photo_tmp = $_FILES['photo']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.
$bg_color = $_POST["bg_color"];
$link = trim($_POST["link"]);

if($height_f < $width_f){$height_f = $width_f;}
if($content_2 != '' && $height_f < $width_f + 30){$height_f = $width_f + 30;}

// === Загрузка фото
if($photo_tmp)
{
	$photo_name = date("YmdHis").'.jpg';	

	$stmt = $db->prepare("SELECT width_f, p10 FROM modules WHERE id = :id AND module = 'circle' LIMIT 1");
	$stmt->execute(array('id' => $mod_id));
	$mod = $stmt->fetch();
	
	if(!empty($mod['p10'])) unlink($root.'/files/modules/circle/'.$mod['p10']);	

	if(!file_exists($root.'/files/modules/')){mkdir($root.'/files/modules/', 0755);}
	if(!file_exists($root.'/files/modules/circle/')){mkdir($root.'/files/modules/circle/', 0755);}

	include_once($root."/classes/ImageResize/ImageResizeCutting.php");
	$img_small = new ImageResizeCutting($photo_tmp, $root.'/files/modules/circle/'.$photo_name, $mod['width_f'], $mod['width_f']);
	$img_small->run();

	$photo_sql = "p10 = '".$photo_name."',";
}
else{$photo_sql = '';}

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	block = :block,
	width_f = :width_f,
	height_f = :height_f,
	padding_w = :padding_w,
	padding_h = :padding_h,
	effect_a = :effect_a,
	color = :color,
	bg_color = :bg_color,
	content = :content,
	content_2 = :content_2,
	p1 = :p1,
	p2 = :p2,
	".$photo_sql."
	ordering = :ordering
	WHERE id = :id AND module = 'circle' LIMIT 1
");

$stmt_update->execute(array(
	'title' => $title,
	'pub' => $pub,
	'block' => $block,
	'width_f' => $width_f,
	'height_f' => $height_f,
	'padding_w' => $padding_w,
	'padding_h' => $padding_h,
	'effect_a' => $effect_a,
	'color' => $text_color,
	'bg_color' => $bg_color,
	'content' => $content,
	'content_2' => $content_2,
	'p1' => $font_size,
	'p2' => $link,
	'ordering' => $ordering,
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/circle/".$mod_id); exit;}

?>