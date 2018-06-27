<?php
// DAN 2010
// выводит заголовок интернет - магазина.
defined('AUTH') or die('Restricted access');

echo
'
<div class="title">
	<div class="title-1"></div>
	<h1 class="title-2">'.$shop_title.'</h1>
	<div class="title-3"></div>			
</div>
<br/>
<div>'.$shop_description.'</div>
<hr/>
';

?>