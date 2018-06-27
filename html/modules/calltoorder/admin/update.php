<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

$mod_title = htmlspecialchars($_POST["title"]);
$mod_pub = intval($_POST["pub"]);
$mod_block = htmlspecialchars($_POST["block"]);
$mod_view = intval($_POST["view"]);
$mod_size = intval($_POST["size"]);
$mod_right = intval($_POST["right"]);
$mod_bottom = intval($_POST["bottom"]);	
$mod_color = htmlspecialchars($_POST["color"]);
$mod_ordering = intval($_POST["ordering"]);

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET 
	title = :title, 
	pub = :pub,
	block = :block, 
	ordering = :ordering,
	p1 = :p1,
	p2 = :p2,
	p3 = :p3,
	p4 = :p4,
	p5 = :p5
	WHERE id = :id AND module = 'calltoorder' LIMIT 1
");

$stmt_update->execute(array(
	'title' => $mod_title,
	'pub' => $mod_pub,
	'block' => $mod_block,
	'ordering' => $mod_ordering,
	'p1' => $mod_view,
	'p2' => $mod_size,
	'p3' => $mod_right,
	'p4' => $mod_bottom,
	'p5' => $mod_color,
	'id' => $mod_id
));

if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/calltoorder/".$mod_id); exit;}

?>