<?php
// DAN обновление - январь 2014
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.
defined('AUTH') or die('Restricted access');

$basket_item_id = intval($d[3]);
$char_md5 = htmlspecialchars($d[4]);

// ======= СЕССИИ ========================================================================
session_start();

if (isset ($_SESSION['basket'])) 
{  
	unset($_SESSION['basket']["$basket_item_id"]["$char_md5"]);	
}

Header ("Location: http://".$site."/shop/basket"); exit;

?>