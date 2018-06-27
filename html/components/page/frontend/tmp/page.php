<?php
defined('AUTH') or die('Restricted access');

$out = '';

if (strlen($page_item['title']) > 0){$out .= '<h1 class="title">'.$page_item['title'].'</h1>';}
$out .= '<div>'.$page_item['text'].'</div>';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_page" data-id="'.$page_item['id'].'">'.$out.'</div>';}
else {echo $out;}

?>
