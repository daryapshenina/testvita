<?php
defined('AUTH') or die('Restricted access');

if(!isset($items_out)){$items_out = '';}
if($SITE->d[1] == "all") {$section['description'] = $shopSettings->shop_text;}

// выводить описание внизу / вверху
if ($shopSettings->section_description == 1)
{ 
	$section_out = $items_out.$nav_out.'<div>&nbsp</div>'.$section['description'];
}
else
{
	$section_out = $section['description'].'<div>&nbsp</div>'.$items_out.$nav_out;
}

$out =
'<div class="section-block">
	<h1 class="title">'.$section['title'].'</h1>
	'.$section_out.'
</div>
';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_shop_section" data-id="'.$section['id'].'">'.$out.'</div>';}
else {echo $out;}

?>