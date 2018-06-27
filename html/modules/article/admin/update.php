<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[4]);

if(isset($_POST["titlepub"])){$titlepub = intval($_POST["titlepub"]);} else{$titlepub = 0;}
if(isset($_POST["title"])){$title = htmlspecialchars($_POST["title"]);} else {$title = '';}
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
if(isset($_POST["block"])){$block = strip_tags($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
if(isset($_POST["type"])){$type = intval($_POST["type"]);} else{$type = 0;}
if(isset($_POST["view"])){$view = intval($_POST["view"]);} else{$view = 0;}
if(isset($_POST["num_articles"])){$num_articles = intval($_POST["num_articles"]);} else{$num_articles = 0;}
if(isset($_POST["section_link"])){$section_link = intval($_POST["section_link"]);} else {$section_link = 0;}
if(isset($_POST["anchor"])){$anchor = htmlspecialchars($_POST["anchor"]);} else {$anchor = '';}
if(isset($_POST["width_d"])){$width_d = intval($_POST["width_d"]);} else{$width_d = 25;}
if(isset($_POST["width_n"])){$width_n = intval($_POST["width_n"]);} else{$width_n = 25;}
if(isset($_POST["width_t"])){$width_t = intval($_POST["width_t"]);} else{$width_t = 50;}
if(isset($_POST["width_p"])){$width_p = intval($_POST["width_p"]);} else{$width_p = 100;}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 10;}
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
if(isset($_POST["padding_w"])){$padding_w = $_POST["padding_w"];} else{$padding_w = 10;}
if(isset($_POST["padding_h"])){$padding_h = $_POST["padding_h"];} else{$padding_h = 10;}

$margin = $margin_w.';'.$margin_h;
$padding = $padding_w.';'.$padding_h;

$width_prc = $width_d.';'.$width_n.';'.$width_t.';'.$width_p;

// Получаем массив ид категорий
if (isset($_POST["sections"]))
{
	$sections_arr = $_POST["sections"];

	// проверяем есть ли что нибудь в массиве
	$c = 0;
	$sections_arr_bd = '';
	if (!empty($sections_arr))
	{
		foreach ($sections_arr as $section)
		{
			$c++;
			if ($c == 1){$sections_arr_bd .= $section;} else{$sections_arr_bd .= ";".$section;}
		}
	}
	else
	{
		$sections_arr_bd = '0';
	}
}
else
{
	$sections_arr_bd = '0';
}



// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
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
	'title' => $title,
	'pub' => $pub,
	'titlepub' => $titlepub,
	'block' => $block,
	'ordering' => $ordering,
	'p1' => $type,
	'p2' => $sections_arr_bd,
	'p3' => $num_articles,
	'p4' => $view,
	'p5' => $section_link,
	'p6' => $anchor,
	'p7' => '',
	'p8' => $width_prc,
	'p9' => $margin,
	'p10'=> $padding,
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/article/".$mod_id); exit;}



?>