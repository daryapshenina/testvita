<?php
defined('AUTH') or die('Restricted access');

$stmt_section = $db->prepare("SELECT * FROM com_photo_section WHERE id = :id LIMIT 1");
$stmt_section->execute(array('id' => $d[2]));
$section = $stmt_section->fetch();

$head->addFile('/components/photo/frontend/section/tmp/'.$section['type'].'/style.css');
$head->addFile('/components/photo/frontend/section/tmp/'.$section['type'].'/section.js');

// SEO
if($section['tag_title'] == '') $tag_title = $section['title']." - ".Settings::instance()->getValue('Наименование сайта');
else $tag_title = $section['tag_title'];

if($section['tag_description'] == '') $tag_description = Settings::instance()->getValue('Описание сайта');
else $tag_description = $section['tag_description'];

include($root.'/components/photo/frontend/section/tmp/'.$section['type'].'/tmp.php');

?>