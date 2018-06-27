<?php
// DAN 2012
// выводит статью
defined('AUTH') or die('Restricted access');

echo'<div class="article_sections_list"><b>'.$otstup.'<a href="/article/section/'.$section_id.'">'.$section_title.'</a></b><span class="article_sections_number">('.$number_articles.')</span></div>';

?>