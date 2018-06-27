<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';
include_once($root."/classes/Auth.php");

$head->addFile('/components/account/frontend/mainpage/tmp/mainpage.css');


function component()
{
	if(Auth::check())
	{
		$out = '';
		$settings = accountSettings::getInstance();

		if($settings->shop_allow) $out .= '<div class="account_ico"><a href="/shop/account" class="account_a"><img border="0" src="/components/account/frontend/mainpage/tmp/online-shop.png"><br><span>Заказы в<br>интернет магазине</span></a></div>';
		if($settings->ads_allow) $out .= '<div class="account_ico"><a href="/ads/my" class="account_a"><img border="0" src="/components/account/frontend/mainpage/tmp/ads.png"><br><span>Мои объявления</span></a></div>';
		
		echo '
		<div class="account_ico">
			<a href="/account/view" class="account_a"><img border="0" src="/components/account/frontend/mainpage/tmp/user.png"><br><span>Мой профиль</span></a>
		</div>'.$out;
	}
	else
	{
		$url = '/account';
		echo Auth::formLogin("Вход", $url);
	}
}

?>