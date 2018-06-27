<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/settings/settings.css');

$item_id = intval(isset($admin_d4)); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Интернет - магазин:</div>
		<div>&nbsp;</div>	
	';	
	
	
	
	echo'
	<div class="margin-left-right-10">
		<div class="shop_settings_ico_container">
			<div class="shop_settings_ico">
				<a href="/admin/com/shop/sectionadd" class="shop_settings_a">
					<img border="0" src="/components/shop/admin/tmp/images/section_add.png" style="vertical-align: middle" />
					<br/>
					<span>Добавить раздел</span>
				</a>
			</div>		
			<div class="shop_settings_ico">
				<a href="/admin/com/shop/shoporders" class="shop_settings_a">
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
				<a href="/admin/com/shop/payment" class="shop_settings_a">
					<img border="0" src="/components/shop/admin/tmp/images/money.png" style="vertical-align: middle" />
					<br/>
					<span>Оплата</span>
				</a>
			</div>
			<div class="shop_settings_ico">
				<a href="/admin/com/shop/contract" class="shop_settings_a">
					<img border="0" src="/components/shop/admin/tmp/images/contract.png" style="vertical-align: middle" />
					<br/>
					<span>Договор</span>
				</a>
			</div>			
			<div class="shop_settings_ico">
				<a href="/admin/com/shop/import_and_export/import_excel" class="shop_settings_a">
					<img border="0" src="/components/shop/admin/tmp/images/excell_upload.png" style="vertical-align: middle" />
					<br/>
					<span>Загрузка из Excell</span>
				</a>
			</div>
			<div class="shop_settings_ico">
				<a href="/admin/com/shop/import_and_export/export_excel" class="shop_settings_a">
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
				<a href="/admin/com/shop/yml" class="shop_settings_a" onClick="alert(\'Раздел в разработке\')">
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
		</div>
	</div>
	';	
		
}
?>
