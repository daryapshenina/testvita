<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/mainpage/mainpage.css');

function a_com()
{
	global $domain, $item_section_id ;

	echo'
	<h1>Интернет - магазин:</h1>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/section/add" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/section_add.png" style="vertical-align: middle" />
			<br/>
			<span>Добавить раздел</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/orders" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/shopping_cart.png" style="vertical-align: middle" />
			<br/>
			<span>Заказы</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/settings" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/settings.png" style="vertical-align: middle" />
			<br/>
			<span>Настройки магазина</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/chars" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/filter.png" style="vertical-align: middle" />
			<br/>
			<span>Характеристики</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/stickers" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/stickers.png" style="vertical-align: middle" />
			<br/>
			<span>Стикеры</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/price_type" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/price_type.png" style="vertical-align: middle" />
			<br/>
			<span>Типы цен</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/users" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/price_user.png" style="vertical-align: middle" />
			<br/>
			<span>Пользователи</span>
		</a>
	</div>		
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/payment" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/money.png" style="vertical-align: middle" />
			<br/>
			<span>Оплата</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/excel/import" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/excell_upload.png" style="vertical-align: middle" />
			<br/>
			<span>Загрузка из Excell</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/excel/export" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/excell_download.png" style="vertical-align: middle" />
			<br/>
			<span>Выгрузка в Excell</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/1c" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/1c.png" style="vertical-align: middle" />
			<br/>
			<span>Обмен с 1С</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="/admin/com/shop/yml" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/yml.png" style="vertical-align: middle" />
			<br/>
			<span>Яндекс-маркет</span>
		</a>
	</div>
	<div class="shop_settings_ico">
		<a href="http://5za.ru/page/26" target="_blank" class="shop_settings_a">
			<img border="0" src="/components/shop/admin/tmp/images/help.png" style="vertical-align: middle" />
			<br/>
			<span>Помощь</span>
		</a>
	</div>
	';

}
?>