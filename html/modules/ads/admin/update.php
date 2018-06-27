<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT'].'/modules/ads/classes/modAds.php';

$mod = new modAds;

// id модуля
$mod_id  = intval($d[3]);

$mod->title = trim(htmlspecialchars(strip_tags($_POST["title"])));
if(isset($_POST["title_pub"])){$mod->title_pub = intval($_POST["title_pub"]);} else {$mod->title_pub = 0;}
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
if(isset($_POST["block"])){$block = strip_tags($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
if(isset($_POST["quantity"])){$mod->quantity = intval($_POST["quantity"]);} else{$mod->quantity = 0;}

$content = serialize($mod);

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	pub = :pub,
	block = :block,
	content = :content,
	ordering = :ordering
	WHERE id = :id AND module = 'ads' LIMIT 1
");

$stmt_update->execute(array(
	'pub' => $pub,
	'block' => $block,
	'content' => $content,
	'ordering' => $ordering,
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/ads/".$mod_id); exit;}

?>