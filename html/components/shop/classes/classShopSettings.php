<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';

class classShopSettings
{
	public $agreement = 1;
	public $agreement_text = 'Согласен на обработку персональных данных';
	public $company_name = '';	
	public $basket_type = 1;
	public $c1_psw = ''; // Пароль для загрузки из 1С	
	public $c1_db_reset = 1; // При полной выгрузке из 1С - база стирается и загружается полностью с нуля
	public $currency = 'руб.';
	public $delivery = '';
	public $grouping = 0;
	public $item_quantity = 0; // Не учитывать количество
	public $mapping = 11; // Вид отображения товаров в категории
	public $nds = 1; // НДС 	
	public $output_un_section = 1; // Вывод товаров из подразделов
	public $payment_method_cash = 0; // Оплата при получении
	public $payment_method_сash_on_delivery = 0;  // Наложенным платежом	
	public $payment_method_prepayment = 0; // Предоплата
	public $payment_method_sberbank = 0; // 1 - Касса, 2 - Яндекс-деньги
	public $payment_method_yandex = 0; // 1 - Касса, 2 - Яндекс-деньги
	public $quantity = 100;
	public $question = 1;
	public $related_items = 'Сопутствующие товары';
	public $sberbank_login = '';
	public $sberbank_password = '';
	public $sberbank_test = 1;
	public $section_description = 1;
	public $section_filters = 1;
	public $shop_name = '';
	public $shop_title = '';
	public $shop_text = '';
	public $small_resize_method = 1;
	public $sorting_items = 0; // Сортировка товара в категории
	public $sticker_add_to_cart = 'В корзину';
	public $sticker_hit = 'Хит';
	public $sticker_new = 'Новинка';	
	public $sticker_order = 'Под заказ';
	public $sticker_rating = 'Рейтинг';
	public $sticker_sale = 'Акция';	
	public $sub_sections = 1;
	public $tag_title = '';
	public $tag_description = '';
	public $ue = 100;
	public $view_item_card = 8; // Вид отображения товаров в категории	
	public $x_small = 240;
	public $x_big = 1000;	
	public $y_big =	1000;
	public $yandex_cashbox_check_url = '/shop/basket/yandex_cashbox/check';
	public $yandex_cashbox_success_url = '/shop/basket/yandex_cashbox/success';
	public $yandex_cashbox_fail_url = '/shop/basket/yandex_cashbox/fail';
	public $yandex_cashbox_password = '';	
	public $yandex_cashbox_scid = '';
	public $yandex_cashbox_shop_id = '';
	public $yandex_cashbox_test = 1;
	public $y_small = 180;
	public $yml_key = '';
	public $yandex_secret = '';
	public $yandex_money_id = '';

	
	public function __construct()
	{
		global $db;
		$stmt_settings = $db->query("SELECT parametr FROM com_shop_settings WHERE name = 'settings' LIMIT 1");
		$this->settings = $stmt_settings->fetchColumn();
	}
	
	static public function instance()
	{
		if(classShopSettings::$instance == null)
			classShopSettings::$instance = new classShopSettings();

		return classShopSettings::$instance;
	}	

	public function __sleep()
	{
		return array(
		'shop_name',
		'company_name',
		'shop_title',
		'shop_text',
		'shop_description',			
		'tag_title',
		'tag_description',	
		'x_small',
		'y_small',
		'x_big',
		'y_big',
		'small_resize_method',
		'section_filters',
		'sub_sections',
		'output_un_section',
		'section_description',	
		'sorting_items',
		'view_item_card',
		'quantity',
		'mapping',
		'question',
		'item_quantity',
		'grouping',
		'currency',
		'c1_psw',	
		'c1_db_reset',	
		'sticker_new',
		'sticker_sale',	
		'sticker_hit',
		'sticker_order',	
		'sticker_rating',
		'sticker_add_to_cart',
		'related_items',
		'agreement',
		'agreement_text',	
		'basket_type',	
		'delivery',
		'yml_key',
		'nds',
		'payment_method_cash',
		'payment_method_prepayment',
		'payment_method_сash_on_delivery',
		'payment_method_yandex',
		'payment_method_sberbank',
		'sberbank_test',
		'sberbank_login',
		'sberbank_password',	
		'yandex_money_id',
		'yandex_secret',
		'yandex_cashbox_check_url',
		'yandex_cashbox_success_url',
		'yandex_cashbox_fail_url',
		'yandex_cashbox_test',
		'yandex_cashbox_shop_id',
		'yandex_cashbox_scid',
		'yandex_cashbox_password',
		'ue',
		);
	}
}


class ShopSettings
{
	static public function instance()
	{
		if(ShopSettings::$instance == null)
			ShopSettings::$instance = new ShopSettings();

		return ShopSettings::$instance;
	}

	private function __construct()
	{
		global $db;

		$this->arraySettings = array();

		$SQL = $db->query('SELECT name, parametr FROM com_shop_settings');
		$array = $SQL->fetchAll();

		foreach($array as $name => $value)
			$this->arraySettings[$value['name']] = $value['parametr'];
	}

	public function getValue($_name)
	{
		if(array_key_exists($_name, $this->arraySettings))
			return $this->arraySettings[$_name];

		return 'ERROR SETTINGS NAME';
	}

	public function getArray()
	{
		return $this->arraySettings;
	}

	public function debug()
	{
		foreach($this->arraySettings as $name => $value)
			echo $name.' => '.$value.'<br />';
	}

	static private $instance = null;
	private $arraySettings;
}

?>
