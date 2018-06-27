<?php
// DAN 2012
// выводит статью
defined('AUTH') or die('Restricted access');

echo'
	<div class="title">
		<div class="title-1"></div>
		<div class="title-2"><h1>'.$shopSettings->getValue('shop_title').'</h1></div>
		<div class="title-3"></div>
	</div>
	<div>&nbsp;</div>
	<div>'.$shopSettings->getValue('shop_description').'</div>
';

?>
