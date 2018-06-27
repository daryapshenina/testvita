<?php
// Редактируемый модуль >>> добавить
defined('AUTH') or die('Restricted access');

$id = intval($d[3]);

$stmt_select = $db->prepare("SELECT * FROM modules WHERE id = :id LIMIT 1");
$stmt_select->execute(array('id' => $id));

$copy = $stmt_select->fetch();

$copy['title'] = 'Копия '.$copy['title'];
$copy['ordering'] = $copy['ordering'] + 1;

$stmt_insert = $db->prepare("
	INSERT INTO modules SET
	title = :title,
	module = :module,
	module_csssuf = :module_csssuf,
	pub = :pub,
	titlepub = :titlepub,
	enabled = '1',
	description = :description,
	width_p = :width_p,
	width_f = :width_f,
	height_f = :height_f,
	margin_w = :margin_w,
	margin_h = :margin_h,
	padding_w = :padding_w,
	padding_h = :padding_h,
	effect_a = :effect_a,
	effect = :effect,
	color = :color,
	bg_color = :bg_color,
	content = :content,
	content_2 = :content_2,
	p1 = :p1,
	p2 = :p2,
	p3 = :p3,
	p4 = :p4,
	p5 = :p5,
	p6 = :p6,
	p7 = :p7,
	p8 = :p8,
	p9 = :p9,
	p10 = :p10,
	block = :block,
	ordering = :ordering
");

$stmt_insert->execute(array(
	'title' => $copy['title'],
	'module' => $copy['module'],	
	'module_csssuf' => $copy['module_csssuf'],
	'pub' => $copy['pub'],
	'titlepub' => $copy['titlepub'],
	'description' => $copy['description'],
	'width_p' => $copy['width_p'],
	'width_f' => $copy['width_f'],
	'height_f' => $copy['height_f'],
	'margin_w' => $copy['margin_w'],
	'margin_h' => $copy['margin_h'],
	'padding_w' => $copy['padding_w'],
	'padding_h' => $copy['padding_h'],
	'effect_a' => $copy['effect_a'],
	'effect' => $copy['effect'],
	'color' => $copy['color'],
	'bg_color' => $copy['bg_color'],
	'content' => $copy['content'],
	'content_2' => $copy['content_2'],
	'p1' => $copy['p1'],
	'p2' => $copy['p2'],
	'p3' => $copy['p3'],
	'p4' => $copy['p4'],
	'p5' => $copy['p5'],
	'p6' => $copy['p6'],
	'p7' => $copy['p7'],
	'p8' => $copy['p8'],
	'p9' => $copy['p9'],
	'p10' => $copy['p10'],	
	'block' => $copy['block'],
	'ordering' => $copy['ordering']
));

$new_id = $db->lastInsertId();

if(isset($d[4]) && $d[4] == 'frontend')
{
	Header ("Location: ".$_SERVER['HTTP_REFERER']); 	
}
else
{
	Header ("Location: /admin/modules"); 
}

exit;

?>