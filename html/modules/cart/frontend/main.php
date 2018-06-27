<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

if(!isset($shopSettings))
{
	include_once $root.'/components/shop/classes/classShopSettings.php';	
	$s = new classShopSettings;
	$shopSettings = unserialize($s->settings);
}

include_once $root."/components/shop/classes/Orders.php";
include $root.'/modules/cart/frontend/'.$m['p1'].'.php';

?>