<?php
defined('AUTH') or die('Restricted access');

if(!isset($shopSettings))
{
	include_once($root."/components/shop/classes/classShopSettings.php");	
	$s = new classShopSettings;
	$shopSettings = unserialize($s->settings);	
}

$head->addFile('/components/shop/frontend/tmp/style.css');
$head->addFile('/components/shop/frontend/shop_script.js');

$head->addFile('/modules/shop/frontend/scroll.js');
$head->addFile('/modules/shop/frontend/style.css');


include_once($root."/components/shop/classes/classShopItem.php");

$shop_mapping = $shopSettings->mapping;

if($shop_mapping == 999)
{
	include_once($root."/tmp/shop/section/tmp.php");
	$head->addFile('/tmp/shop/section/style.css');
	if(file_exists($root.'/tmp/shop/section/tmp/tmp.js')){$head->addFile('/tmp/shop/section/tmp.js');}
}
else
{
	$head->addFile('/components/shop/frontend/section/tmp/'.$shop_mapping.'/style.css');
	if(file_exists($root.'/components/shop/frontend/section/tmp/'.$shop_mapping.'/tmp.js')){$head->addFile('/components/shop/frontend/section/tmp/'.$shop_mapping.'/tmp.js');}
	include_once($root.'/components/shop/frontend/section/tmp/'.$shop_mapping.'/tmp.php');
}

if($frontend_edit == 1){$head->addFile('/modules/shop/frontend/edit.js');}
