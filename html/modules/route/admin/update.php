<?php
defined('AUTH') or die('Restricted access');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// id модуля
$mod_id  = intval($d[4]);

if(isset($_POST["titlepub"])){$titlepub = intval($_POST["titlepub"]);} else{$titlepub = 0;}
if(isset($_POST["title"])){$title = htmlspecialchars($_POST["title"]);} else {$mod_title = '';}
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
if(isset($_POST["block"])){$block = strip_tags($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 10;}
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
if(isset($_POST["padding_w"])){$padding_w = $_POST["padding_w"];} else{$padding_w = 10;}
if(isset($_POST["padding_h"])){$padding_h = $_POST["padding_h"];} else{$padding_h = 10;}

$height = isset($_POST["height"]) ? intval($_POST["height"]) : 300;
$y = isset($_POST["y"]) ? (string)$_POST["y"] : 0;
$x = isset($_POST["x"]) ? (string)$_POST["x"] : 0;

$margin = $margin_w.';'.$margin_h;
$padding = $padding_w.';'.$padding_h;

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
	block = :block,
	ordering = :ordering,
	p1 = :p1,
	p2 = :p2,
	p3 = :p3,
	p5 = :p5,
	p6 = :p6
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'title' => $title,
	'pub' => $pub,
	'titlepub' => $titlepub,
	'block' => $block,
	'ordering' => $ordering,
	'p1' => $height,
	'p2' => $y,
	'p3' => $x,
	'p5' => $margin,
	'p6' => $padding,
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/route/".$mod_id); exit;}
