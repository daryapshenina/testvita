<?php
// выводит статью
defined('AUTH') or die('Restricted access');

$out = '<h1 class="title">'.$article_item_title.'</h1>
	<div>'.$text_output.'</div>
	<div>&nbsp;</div>	
	<div class="article_toolbar">'.$toolbar.'</div>
';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_article_item" data-id="'.$article_item_id.'">'.$out.'</div>';}
else {echo $out;}


?>