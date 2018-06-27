<?php
defined('AUTH') or die('Restricted access');

$id = $d[3];

if(isset($_POST["title"])){$title = htmlspecialchars($_POST["title"]);} else {$title = '';}
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
//if(isset($_POST["titlepub"])){$titlepub = intval($_POST["titlepub"]);} else{$titlepub = 0;}
if(isset($_POST["block"])){$block = htmlspecialchars($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
if(isset($_POST["size"])){$size = intval($_POST["size"]);} else{$size = 0;}
if(isset($_POST["height"])){$height = intval($_POST["height"]);} else{$height = 0;}
if(isset($_POST["flat_width"])){$flat_width = intval($_POST["flat_width"]);} else{$flat_width = 200;}
if(isset($_POST["width_d"])){$width_d = intval($_POST["width_d"]);} else{$width_d = 25;}
if(isset($_POST["width_n"])){$width_n = intval($_POST["width_n"]);} else{$width_n = 25;}
if(isset($_POST["width_t"])){$width_t = intval($_POST["width_t"]);} else{$width_t = 50;}
if(isset($_POST["width_p"])){$width_p = intval($_POST["width_p"]);} else{$width_p = 100;}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 10;}	
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
if(isset($_POST["button_type"])){$button_type = intval($_POST["button_type"]);} else{$button_type = 0;}
if(isset($_POST["button_text"])){$button_text = htmlspecialchars($_POST["button_text"]);} else{$button_text = '';}
if(isset($_POST["button_color"])){$button_color = $_POST["button_color"];} else{$button_color = '#ff6600';}
if(isset($_POST["sub_color"])){$sub_color = $_POST["sub_color"];} else{$sub_color = '#000000';}
if(isset($_POST["transparent"])){$transparent = floatval($_POST["transparent"]);} else{$transparent = 0.5;}
if(isset($_POST["link"])){$link = htmlspecialchars($_POST["link"]);} else{$link = '';}
if(isset($_POST["content"])){$content = $_POST["content"];} else{$content = '';}

$margin = $margin_w.';'.$margin_h;

if($height < 150){$height = 150;}
if($height > 600){$height = 600;}
$width_prc = $width_d.';'.$width_n.';'.$width_t.';'.$width_p;

$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,	
	block = :block,
	content	= :content,
	content_2 = :content_2,
	ordering = :ordering,
	p1 = :p1,
	p2 = :p2,
	p3 = :p3,
	p4 = :p4,
	p5 = :p5,
	p6 = :p6,
	p7 = :p7,
	p8 = :p8,
	p9 = :p9,
	p10 = :p10
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'title' => $title,
	'pub' => $pub,
	'block' => $block,
	'content' => $content,
	'content_2' => $link,
	'ordering' => $ordering,
	'p1' => $size,
	'p2' => $height,
	'p3' => $flat_width,
	'p4' => $width_prc,
	'p5' => $margin,
	'p6' => $button_type,
	'p7' => $button_text,
	'p8' => $button_color,
	'p9' => $sub_color,
	'p10' => $transparent,
	'id' => $id
));

if($bt_save == LANG_M_FLAT_SHADOW_BUTTON_SAVE){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/flat_shadow_button/".$id); exit;}



?>