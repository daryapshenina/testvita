<?php
// DAN 2013
// выводит заголовок раздела
defined('AUTH') or die('Restricted access');

if(!isset($items_out)){$items_out = '';}

// выводить описание внизу / вверху
if ($shopSettings->getValue('section_description') == 1)
{
	$section_out = $items_out.$nav_out.'<div>&nbsp</div>'.$section_description;
}
else
{
	$section_out = $section_description.'<div>&nbsp</div>'.$items_out.$nav_out;
}

$out =
'<div class="section-block">
	<h1 class="section-title">'.$section_title.'</h1>
	'.$section_out.'
</div>
';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_shop_section" data-id="'.$section_id.'">'.$out.'</div>';}
else {echo $out;}

?>
