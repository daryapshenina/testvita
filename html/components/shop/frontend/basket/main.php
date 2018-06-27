<?php
defined('AUTH') or die('Restricted access');
include_once __DIR__.'/lang/'.LANG.'.php';

switch($d[2])
{
	case 'add_ajax':
		include("components/shop/frontend/basket/add_ajax.php");
		break;
	
	case 'delete':
		include("components/shop/frontend/basket/delete.php");
		break;
		
	case 'client':
		include("components/shop/frontend/basket/client.php");
		break;

	case 'mail':
		include("components/shop/frontend/basket/mail.php");
		break;

	case 'pay':
		include("components/shop/frontend/basket/pay.php");
		break;
		
	case 'pay2':
		include("components/shop/frontend/basket/pay2.php");
		break;

	case 'yandex_cashbox':
		switch($d[3])
		{
			case 'check':
				include("components/shop/frontend/basket/yandex/yandex_check.php");
				break;

			case 'success':
				include("components/shop/frontend/basket/yandex/yandex_success.php");
				break;

			case 'fail':
				include("components/shop/frontend/basket/yandex/yandex_fail.php");
				break;
		}
		break;

	case 'sberbank':
		switch($d[3])
		{
			case 'success':
				include("components/shop/frontend/basket/sberbank/sberbank_success.php");
				break;

			case 'fail':
				include("components/shop/frontend/basket/sberbank/sberbank_fail.php");
				break;
		}
		break;		

	default:
		include("components/shop/frontend/basket/basket.php");
}

?>