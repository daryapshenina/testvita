<?php
// DAN 2012
// выводит статью
defined('AUTH') or die('Restricted access');

echo'
	<div class="title">
		<div class="title-1"></div>
		<div class="title-2"><h1>'.$article_title.'</h1></div>
		<div class="title-3"></div>			
	</div>
	<div>&nbsp;</div>
	<div>'.$article_description.'</div>
	<div>&nbsp;</div>	
';

?>