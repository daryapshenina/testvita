<?php
// Скроллер
// p1 - количество изображений (максимальное)
// p2 - минимальная ширина изображения
defined('AUTH') or die('Restricted access');

$id = $d[3];

if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else{$pub = 0;}
if(isset($_POST["title"])){$title = htmlspecialchars($_POST["title"]);} else{$title = 'Скроллер';}
if(isset($_POST["titlepub"])){$titlepub = htmlspecialchars($_POST["titlepub"]);} else{$titlepub = 0;}
if(isset($_POST["images_order"])){$images = htmlspecialchars($_POST["images_order"]);} else{$images = '';}
if(isset($_POST["block"])){$block = htmlspecialchars($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = '';}
if(isset($_POST["num_max"])){$num_max = intval($_POST["num_max"]);} else{$num_max = '8';}
if(isset($_POST["width"])){$width = intval($_POST["width"]);} else{$width = '100';}

if(isset($_POST["autoscroll"]))
{
	$autoscroll = $_POST["autoscroll"] == '1' ? '1' : '0';
}
else
{
	$autoscroll = '0';
}

if(isset($_POST["speed"])){$speed = floatval($_POST["speed"]);} else{$speed = '1';}
if(isset($_POST["pause"])){$pause = intval($_POST["pause"]);} else{$pause = '1';}

$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
	block = :block,
	content = :content,
	p1 = :p1,
	p2 = :p2,
	p3 = :p3,
	p4 = :p4,
	p5 = :p5,
	ordering = :ordering
	WHERE id = :id AND module = 'scroll_images'
");

$stmt_update->execute(array(
'title' => $title,
'pub' => $pub,
'titlepub' => $titlepub,
'block' => $block,
'content' => $images,
'p1' => $num_max,
'p2' => $width,
'p3' => $speed,
'p4' => $pause,
'p5' => $autoscroll,
'ordering' => $ordering,
'id' => $id
));


if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/scroll_images/".$id); exit;}

?>