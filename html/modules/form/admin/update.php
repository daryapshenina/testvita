<?php
// Редактируемый модуль
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

$mod_title = htmlspecialchars(strip_tags($_POST["title"]));
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else{$mod_titlepub = 0;}
$mod_block = htmlspecialchars($_POST["block"]);
$mod_type = intval($_POST["type"]);
$mod_ordering = intval($_POST["ordering"]);
if(isset($_POST["width_d"])){$width_d = intval($_POST["width_d"]);} else{$width_d = 25;}
if(isset($_POST["width_n"])){$width_n = intval($_POST["width_n"]);} else{$width_n = 25;}
if(isset($_POST["width_t"])){$width_t = intval($_POST["width_t"]);} else{$width_t = 50;}
if(isset($_POST["width_p"])){$width_p = intval($_POST["width_p"]);} else{$width_p = 100;}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 0;}
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
$effect_a = $_POST["effect_a"];
$color = $_POST["color"];
$bg_color = $_POST["bg_color"];

if(isset($_POST["text_pub"])){$text_pub = $_POST["text_pub"];} else{$text_pub = '';}
if(isset($_POST["text"])){$text = $_POST["text"];} else{$text = '';}
if(isset($_POST["field_1_pub"])){$field_1_pub = $_POST["field_1_pub"];} else{$field_1_pub = 0;}
$field_1 = htmlspecialchars(strip_tags($_POST["field_1"]));
if(isset($_POST["field_2_pub"])){$field_2_pub = $_POST["field_2_pub"];} else{$field_2_pub = 0;}
$field_2 = htmlspecialchars(strip_tags($_POST["field_2"]));
if(isset($_POST["field_3_pub"])){$field_3_pub = $_POST["field_3_pub"];} else{$field_3_pub = 0;}
$field_3 = htmlspecialchars(strip_tags($_POST["field_3"]));
if(isset($_POST["file_pub"])){$mod_file = $_POST["file_pub"];} else{$mod_file = 0;}
if(isset($_POST["captcha_pub"])){$mod_captcha = $_POST["captcha_pub"];} else{$mod_captcha = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

$width_prc = $width_d.';'.$width_n.';'.$width_t.';'.$width_p;

$mod_title = strip_tags($mod_title);
$mod_title = mb_substr($mod_title, 0, 50, 'UTF-8');

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
	width_p = :width_p,
	margin_w = :margin_w,
	margin_h = :margin_h,
	effect_a = :effect_a,
	color = :color,
	bg_color = :bg_color,
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
	'width_p' => $width_prc,
	'margin_w' => $margin_w,
	'margin_h' => $margin_h,
	'effect_a' => $effect_a,
	'color' => $color,
	'bg_color' => $bg_color,
	'content' => $text_pub,
	'content_2' => $text,
	'block' => $mod_block,
	'ordering' => $mod_ordering,
	'p1' => $field_1_pub,
	'p2' => $field_1,
	'p3' => $field_2_pub,
	'p4' => $field_2,
	'p5' => $field_3_pub,
	'p6' => $field_3,
	'p7' => $mod_file,
	'p8' => $mod_captcha,
	'p10'=> $mod_type,
	'id' => $mod_id
));

if($bt_save == LANG_M_FORM_SAVE){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/form/".$mod_id); exit;}

?>