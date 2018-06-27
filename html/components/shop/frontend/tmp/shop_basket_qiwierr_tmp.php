<?php
// DAN 2013
// выводит сообщение об ошибки
defined('AUTH') or die('Restricted access');

// ======= ШАБЛОН ДЛЯ EMAIL ========================================================
// шапка корзины
echo 
'
	<div class="main-right-header-1"></div>
	<div class="main-right-header-2">
		<div class="shop-item-title-2">ОШИБКА ОПЛАТЫ '.$data.'</div>
		<div>&nbsp;</div>
		<div>Произошла ошибка при попытке оплаты через QIWI.</div>		
	</div>
';

?>