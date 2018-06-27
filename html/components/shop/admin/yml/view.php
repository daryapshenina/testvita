<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/yml/tmp/yml.css');
$head->addFile('/css/font-awesome/css/font-awesome.min.css');

include_once $root.'/components/shop/classes/classShopSettings.php';

$head->addFile('/components/shop/admin/yml/tmp/yml.css');

function a_com()
{
	global $shopSettings;

	echo '
		<script>
			function deleteModal()
			{
				var deleteText = `
					<div style="text-align:center;font-size:28px;padding-bottom:20px;">Удалить товары старше 1 дня?</div>
					<div style="text-align:center;font-size:18px;padding-bottom:20px;">Это действие нельзя отменить!</div>
					<div style="text-align:center;">
						<a href="#" class="graybutton" style="height:50px;line-height:50px;padding:0px 50px;float:none;" onclick="DAN.modal.del();">Отменить</a>
						<br /><br />
						<a href="/admin/com/shop/yml/delete" class="redbutton" style="height:30px;line-height:30px;float:none;">Удалить</a>
					</div>
				`;

				DAN.modal.add(deleteText, 400, 255);
			}
		</script>
	';

	echo
	'
		<h1>Экспорт и импорт данных для <span style="color:#E52620;">Я</span>ндекс.Маркет</h1>
		<div class="shop_settings_ico">
			<a href="/shop/yml/'.$shopSettings->yml_key.'" class="shop_settings_a">
				<i class="fa fa-upload" aria-hidden="true"></i>
				<br/>
				<span>Экспорт</span>
			</a>
		</div>
		<div class="shop_settings_ico">
			<a href="/admin/com/shop/yml/import" class="shop_settings_a">
				<i class="fa fa-download" aria-hidden="true"></i>
				<br/>
				<span>Импорт</span>
			</a>
		</div>
		<div class="shop_settings_ico">
			<a href="#" class="shop_settings_a" onclick="deleteModal();">
				<i class="fa fa-times" aria-hidden="true"></i>
				<br/>
				<span>Удалить товары<br>старше 1 дня</span>
			</a>
		</div>
	';
}
