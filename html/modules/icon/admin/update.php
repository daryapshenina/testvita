<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else{$mod_titlepub = 0;}
if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else {$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["block"])){$mod_block = strip_tags($_POST["block"]);} else{$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else{$mod_ordering = 0;}
if(isset($_POST["icon_type"])){$icon_type = $_POST["icon_type"];} else{$icon_type = '';}
if(isset($_POST["size_type"])){$size_type = intval($_POST["size_type"]);} else{$size_type = 0;}
if(isset($_POST["width_d"])){$width_d = intval($_POST["width_d"]);} else{$width_d = 25;}
if(isset($_POST["width_n"])){$width_n = intval($_POST["width_n"]);} else{$width_n = 25;}
if(isset($_POST["width_t"])){$width_t = intval($_POST["width_t"]);} else{$width_t = 50;}
if(isset($_POST["width_p"])){$width_p = intval($_POST["width_p"]);} else{$width_p = 100;}
if(isset($_POST["width_fix"])){$width_fix = intval($_POST["width_fix"]);} else{$width_fix = 200;}
if(isset($_POST["height_fix"])){$height_fix = intval($_POST["height_fix"]);} else{$height_fix = 200;}
if(isset($_POST["autoheight"])){$autoheight = intval($_POST["autoheight"]);} else{$autoheight = 0;}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 10;}
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
if(isset($_POST["padding_w"])){$padding_w = $_POST["padding_w"];} else{$padding_w = 10;}
if(isset($_POST["padding_h"])){$padding_h = $_POST["padding_h"];} else{$padding_h = 20;}
if(isset($_POST["effect_a"])){$effect_a = $_POST["effect_a"];} else{$effect_a = '';}
if(isset($_POST["color"])){$color = $_POST["color"];} else{$color = '#ccc';}
if(isset($_POST["icon_size"])){$icon_size = $_POST["icon_size"];} else{$icon_size = 100;}
if(isset($_POST["icon_style"])){$icon_style = intval($_POST["icon_style"]);} else{$icon_style = 1;}
if(isset($_POST["text"])){$text = $_POST["text"];} else{$text = '';}
if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

$margin = $margin_w.';'.$margin_h;
$padding = $padding_w.';'.$padding_h;

$width_prc = $width_d.';'.$width_n.';'.$width_t.';'.$width_p;
$width_height_fix = $width_fix.';'.$height_fix;

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
	effect_a = :effect_a,
	content = :content,
	content_2 = :content_2,	
	block = :block,
	ordering = :ordering,
	p1  = :p1,
	p2  = :p2,
	p3  = :p3,
	p4  = :p4,
	p5  = :p5,
	p6  = :p6,
	p7  = :p7,
	p8  = :p8,
	p10 = :p10
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'title' => $mod_title,
	'pub' => $mod_pub,
	'titlepub' => $mod_titlepub,
	'effect_a' => $effect_a,
	'content' => $icon_type,
	'content_2' => $text,	
	'block' => $mod_block,
	'ordering' => $mod_ordering,
	'p1' => $icon_style,
	'p2' => $size_type,
	'p3' => $width_prc,
	'p4' => $width_height_fix,
	'p5' => $margin,
	'p6' => $padding,
	'p7' => $icon_size,
	'p8' => $color,
	'p10'=> $autoheight,
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/icon/".$mod_id); exit;}



?>