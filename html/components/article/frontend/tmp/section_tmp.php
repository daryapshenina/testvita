<?php
// выводит заголовок, описание и фильтры раздела
defined('AUTH') or die('Restricted access');

if(strlen($section_title) > 0){$out .= '<h1 class="title">'.$section_title.'</h1>';}

$out .=
'
<div style="display:table;">'.$section_display_sorting_tmp.'</div>
'.$section_description.'
'.$article_subsection.'
'.$article_items.'
'.$article_nav.'
';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_article_section" data-id="'.$section_id.'">'.$out.'</div>';}
else {echo $out;}

?>