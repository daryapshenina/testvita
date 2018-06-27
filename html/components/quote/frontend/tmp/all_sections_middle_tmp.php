<?php
// DAN 2012
// выводит статью
defined('AUTH') or die('Restricted access');

echo'<div class="quote_sections_list"><b>'.$otstup.'<a href="http://'.$site.'/quote/section/'.$section_id.'">'.$section_title.'</a></b><span class="quote_sections_number">('.$number_quotes.')</span></div>';

?>