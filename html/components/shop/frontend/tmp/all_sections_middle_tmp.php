<?php
// DAN 2012
// выводит статью
defined('AUTH') or die('Restricted access');

echo'<div class="shop_sections_list"><b>'.$otstup.'<a href="/shop/section/'.$section_id.'">'.$section_title.'</a></b><span class="shop_sections_number">('.$number_shop.')</span></div>';

?>