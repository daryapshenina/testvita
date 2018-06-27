<?php
// DAN 2013
// выводит заголовок раздела
defined('AUTH') or die('Restricted access');

echo
'
<div class="section-block">
	<h1 class="section-title">'.$section_title.'</h1>
	'.$section_description.'
</div>
';

?>