<?php
// Редактируемый модуль
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else {$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else{$mod_titlepub = 0;}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else{$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else{$mod_ordering = 0;}
if(isset($_POST["size_type"])){$size = intval($_POST["size_type"]);} else{$size = 0;}
if(isset($_POST["height"])){$height = intval($_POST["height"]);} else{$height = 0;}
if(isset($_POST["width"])){$flat_width = intval($_POST["width"]);} else{$flat_width = 200;}
if(isset($_POST["width_d"])){$width_d = intval($_POST["width_d"]);} else{$width_d = 25;}
if(isset($_POST["width_n"])){$width_n = intval($_POST["width_n"]);} else{$width_n = 25;}
if(isset($_POST["width_t"])){$width_t = intval($_POST["width_t"]);} else{$width_t = 50;}
if(isset($_POST["width_p"])){$width_p = intval($_POST["width_p"]);} else{$width_p = 100;}
if(isset($_POST["autoheight"])){$autoheight = intval($_POST["autoheight"]);} else{$autoheight = 0;}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 10;}
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
if(isset($_POST["padding_w"])){$padding_w = $_POST["padding_w"];} else{$padding_w = 10;}
if(isset($_POST["padding_h"])){$padding_h = $_POST["padding_h"];} else{$padding_h = 20;}
if(isset($_POST["effect"])){$effect = $_POST["effect"];} else{$effect = '';}
if(isset($_POST["bg_color_enable"])){$bg_color_enable = $_POST["bg_color_enable"];} else{$bg_color_enable = '';}
if(isset($_POST["bg_color"])){$bg_color = $_POST["bg_color"];} else{$bg_color = '';}
if(isset($_POST["content"])){$mod_content = $_POST["content"];} else{$mod_content = '';}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

$margin = $margin_w.';'.$margin_h;
$padding = $padding_w.';'.$padding_h;

if($height < 150){$height = 150;}
if($height > 600){$height = 600;}
$width_prc = $width_d.';'.$width_n.';'.$width_t.';'.$width_p;

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
	content = :content,
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
	p9  = :p9,
	p10 = :p10
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'title' => $mod_title,
	'pub' => $mod_pub,
	'titlepub' => $mod_titlepub,
	'content' => $mod_content,
	'block' => $mod_block,
	'ordering' => $mod_ordering,
	'p1' => $size,
	'p2' => $height,
	'p3' => $flat_width,
	'p4' => $width_prc,
	'p5' => $margin,
	'p6' => $padding,
	'p7' => $bg_color_enable,
	'p8' => $bg_color,
	'p9' => $effect,
	'p10'=> $autoheight,
	'id' => $mod_id
));

if($bt_save == LANG_M_EDITOR_SAVE){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/editor/".$mod_id); exit;}



?>