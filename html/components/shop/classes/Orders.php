<?php
defined('AUTH') or die('Restricted access');

include_once($root."/lib/currency.php");
include_once $root."/components/shop/classes/classShopSettings.php";

class Orders
{
    protected static $_instance; // Защищенная статическая переменная $_instance - хранение в себе единственного экземпляра данного класса.	
	private function __construct() // Защищаем конструктор от публичного вывода - исключаем дублирование объектов.
	{
		global $db, $domain;

		CCurrency::update(); // Получаем курс доллара с ЦБР
		
		$user_id = Auth::check();
	

		// Хеш заказа в куках
		if(isset($_COOKIE['shop_order_hash']))
		{			
			$hash = $_COOKIE['shop_order_hash'];

			// Ищем открытый заказ в БД
			$stmt_order_hash = $db->prepare("SELECT id, user_id FROM com_shop_orders WHERE hash = :hash AND status = '0' LIMIT 1");
			$stmt_order_hash->execute(array('hash' => $hash));

			$order = $stmt_order_hash->fetch();
			$order_id = $order['id'];
			$order_user_id = $order['user_id'];
			
			if($user_id && $order_user_id == 0) // Если пользователь авторизирован а у заказа (который был сделан до авторизации) не указан пользователь - обновим пользователя
			{
				// Ищем старые открытые заказы (брошенные корзины) и удвляем их
				$stmt_order_user = $db->prepare("SELECT id FROM com_shop_orders WHERE user_id = :user_id AND status = '0'");
				$stmt_order_user->execute(array('user_id' => $user_id));
				
				while($o = $stmt_order_user->fetch())
				{
					$stmt_items_delete = $db->prepare('DELETE FROM com_shop_orders_items WHERE order_id = :order_id LIMIT 1');
					$stmt_items_delete->execute(array('order_id' => $o['id']));

					$stmt_order_delete = $db->prepare('DELETE FROM com_shop_orders WHERE id = :id LIMIT 1');
					$stmt_order_delete->execute(array('id' => $o['id']));				
				}
				
				$stmt_update = $db->prepare("UPDATE com_shop_orders SET user_id = :user_id WHERE id = :order_id");
				$stmt_update->execute(array('user_id' => $user_id, 'order_id' => $order_id));
			}
		}
		else	
		{
			if($user_id) // Проверка авторизации пользователя
			{
				// Пользователь авторизирован - ищем уже открытый заказ в БД.
				$stmt_order = $db->prepare("SELECT id, hash FROM com_shop_orders WHERE status = '0' AND user_id = :user_id AND user_id <> '0' ORDER BY id DESC LIMIT 1");
				$stmt_order->execute(array('user_id' => $user_id));
				
				if($stmt_order->rowCount() > 0)
				{
					$order_arr = $stmt_order->fetch();
					$order_id = $order_arr['id'];					
				}
				else {$order_id = null;}
			}
			else {$order_id = null;}
		}
		$this->order_id = $order_id;	
	}


	public static function getOrders($_user_id) // Все заказы
	{
		global $db;

		$stmt_orders = $db->prepare("
			SELECT id, items, sum, status, date_order, date_payment 
			FROM com_shop_orders 
			WHERE user_id = :user_id 
			ORDER BY id DESC
		");
		
		$stmt_orders->execute(array('user_id' => $_user_id));
		
		return $stmt_orders->fetchAll();
	}
	
	
	public static function getOrder($_id, $_user_id) // Конкретный заказ, для параметра - защита от перебора по id
	{
		global $db;

		$stmt_order = $db->prepare("SELECT id, orders, items, sum, status, date_order, date_payment FROM com_shop_orders WHERE id = :id AND user_id = :user_id LIMIT 1");
		$stmt_order->execute(array('id' => $_id, 'user_id' => $_user_id));
		
		return $stmt_order->fetch();
	}	
	

	// Получаем номер заказа
    public static function getOrderId() 
	{
        if (self::$_instance === null){self::$_instance = new self;}
        return self::$_instance->order_id;
    }


	// Добавляем товар к заказу
    public static function addItem($item_id, $quantity, $chars)
	{
		global $db, $domain;

		// Если нет открытого заказа - создаём его
		if(!self::getOrderId()){self::newOrder();}		
		
		$order_id = self::getOrderId();		

		// Ищем - есть ли подобный товар уже в заказе
		$stmt_order = $db->prepare("SELECT * FROM com_shop_orders_items WHERE order_id = :order_id AND item_id = :item_id AND chars = :chars ORDER BY id DESC LIMIT 1");
		$stmt_order->execute(array('order_id' => $order_id, 'item_id' => $item_id, 'chars' => $chars));
		
		if($stmt_order->rowCount() > 0)
		{
			$item = $stmt_order->fetch();
			$quantity_new = $item['quantity'] + $quantity;
			$stmt_insert = $db->prepare("UPDATE com_shop_orders_items SET quantity = :quantity WHERE order_id = :order_id AND item_id = :item_id AND chars = :chars");
			$stmt_insert->execute(array('quantity' => $quantity_new,'order_id' => $order_id, 'item_id' => $item_id, 'chars' => $chars));
		}
		else 
		{
			$stmt_insert = $db->prepare("
				INSERT INTO com_shop_orders_items SET
				order_id = :order_id,
				item_id = :item_id,
				quantity = :quantity,
				chars = :chars			
			");
			$stmt_insert->execute(array('order_id' => $order_id, 'item_id' => $item_id, 'quantity' => $quantity, 'chars' => $chars));
		}
    }
	
	
	// Консервируем заказ без возможности редактирования
	public static function checkout($order_id)
	{
		global $db, $SITE, $shopSettings;

		$items = self::getItems($order_id);

		if(count($items) > 0)
		{
			$summa = 0;
			$yandex_items = '';
			$items_out = '';
			$items_email_out = '';
			$items_arr = array();

			foreach($items as $key => $item)
			{
				if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
				if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}
				$sum = $item['price'] * $item['quantity'];

				if(isset($item['nds'])){$nds = $item['nds'];} else{$nds = 1;}

				$summa += $sum;

				$s = array("'", '"');
				$item_title = str_replace($s, "", $item['title']);
				
				$price = number_format($item['price'], 0, '', ' ');
				$sum_format = number_format($sum, 0, '', ' ');			
				if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);} // Приведение типов

				$items_out .= '<a target="_blank" href="/shop/item/'.$item['item_id'].'">'.$item['title'].' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';
				$items_email_out .= '<a target="_blank" href="http://'.$SITE->domain.'/shop/item/'.$item['item_id'].'">'.$item_title.' '.$item['chars'].' '.$item['quantity'].' x '.$price.' руб. = '.$sum_format.' руб. </a></br>';
			}

			$summa_format = number_format($summa, 0, '', ' ');		
			$order = "<span>".$items_out."<b>Сумма: ".$summa_format." ".$shopSettings->currency."</b></span>";

			$stmt_update = $db->prepare("
			UPDATE com_shop_orders
			SET orders = :orders,
			sum = :sum,
			status = '2'
			WHERE id = :id
			");

			$stmt_update->execute(array(
			'orders' => $order,
			'sum' => $summa,
			'id' => $order_id,
			));
		}	
	}
	
	
	// Обновляем количество товара
    public static function updateItem($id, $quantity)
	{
		global $db;		
		$stmt_update = $db->prepare('UPDATE com_shop_orders_items SET quantity = :quantity WHERE id = :id');
		$stmt_update->execute(array('id' => $id, 'quantity' => $quantity));		
	}		


	// Возвращает массив товаров в заказе
    public static function getItems($order_id = null)
	{
		global $db, $domain;

		if(!$order_id) $order_id = self::getOrderId();


		$user_id = Auth::check();

		// Типы цен для пользователя
		$stmt_pu = $db->prepare("SELECT u.price_type_id, t.name FROM com_shop_price_user u JOIN com_shop_price_type t ON t.id = u.price_type_id  WHERE user_id = :user_id LIMIT 1");
		$stmt_pu->execute(array('user_id' => $user_id));
		$p = $stmt_pu->fetch();

		if($stmt_pu->rowCount() > 0)
		{			
			$SQL_pu = 'pi.price price_u,';
			$SQL_pu_case = ",
			CASE currency
			WHEN '0' THEN pi.price
			WHEN '1' THEN pi.price * ".CCurrency::getUSD()."
			WHEN '2' THEN pi.price * ".CCurrency::getEUR()."
			END as price_user
			";
			$SQL_pu_join = "LEFT JOIN com_shop_price_item pi ON pi.item_id = i.id AND pi.price_type_id = '".$p['price_type_id']."' ";
		}
		else
		{
			$SQL_pu = '';
			$SQL_pu_case = '';
			$SQL_pu_join = '';			
		}

		
		$stmt_items = $db->prepare("
			SELECT 
			o.id, 
			o.order_id, 
			o.item_id, 
			o.quantity, 
			o.chars, 
			i.title, 
			i.price, 
			i.currency, 
			i.photo,
			".$SQL_pu."
				CASE currency
				WHEN '0' THEN i.price
				WHEN '1' THEN i.price * ".CCurrency::getUSD()."
				WHEN '2' THEN i.price * ".CCurrency::getEUR()."
				END as price
			".$SQL_pu_case."
			FROM com_shop_orders_items o 
			JOIN com_shop_item i ON i.id = o.item_id
			".$SQL_pu_join."
			WHERE o.order_id = :order_id
			
		");
		
		$stmt_items->execute(array('order_id' => $order_id));

		$arr = $stmt_items->fetchAll();

		foreach ($arr as $i => $a)
		{
			if(!empty($arr[$i]['price_user'])){$arr[$i]['price'] = $arr[$i]['price_user'];}
		}

		return $arr;
    }
	
	
	// Обновляем количество товара
    public static function updateOrderStatus($status = 0)
	{
		global $db;		
		$order_id = self::getOrderId();
		
		$stmt_update = $db->prepare('UPDATE com_shop_orders SET status = :status WHERE id = :id');
		$stmt_update->execute(array('id' => $order_id, 'status' => $status));	
	}		

	
	// Удаляет товар из заказа с проверкой $order_id
    public static function deleteItems($_id)
	{
		global $db, $domain;

		$order_id = self::getOrderId();
		
		$stmt_items = $db->prepare("
			SELECT i.id, i.order_id
			FROM com_shop_orders o 
			JOIN com_shop_orders_items i ON i.order_id = o.id
			WHERE i.order_id = :order_id AND i.id = :id	
		");
		
		$stmt_items->execute(array('order_id' => $order_id, 'id' => $_id));
		
		if($stmt_items->rowCount() > 0)
		{
			$arr = $stmt_items->fetch();
			$id = $arr['id'];
			$stmt_delete = $db->prepare('DELETE FROM com_shop_orders_items WHERE id = :id');
			$stmt_delete->execute(array('id' => $id));
		}
    }	
	

	// Добваить новый заказ
	public static function newOrder()
	{
		global $db, $domain;

		$user_id = Auth::check();
		if(empty($user_id)){$user_id = 0;} // $user_id = 0; === незарегистрированный пользователь

		$rand = time().mt_rand(0, 999999);
		$hash = hash("sha256", 'shop_order_hash'.$rand);		
		
		setcookie('shop_order_hash', $hash, (time() + 60*60*24*365), '/', '.'.$domain, False, True);

		$stmt_insert = $db->prepare("
			INSERT INTO com_shop_orders SET
			user_id = :user_id,
			hash = :hash,
			orders = '',
			items = '',
			sum = '0',
			status = '0',
			payment_system = '',
			date_order = :date_order,
			date_payment = :date_payment,
			fio = '',
			tel = '',
			email = '',
			address = '',
			comments = '',
			payer = ''
		");

		$stmt_insert->execute(array('user_id' => $user_id, 'hash' => $hash, 'date_order' => date("Y-m-d H:i:s"), 'date_payment' => '0000-00-00 00:00:00'));
		$order_id = $db->lastInsertId();
		self::$_instance->order_id = $order_id;
		return $order_id;
	}
	
	
	// Добавить новый товар к заказу.
	
	// предотвращаем возможные дублирования объекта.
    private function __clone(){}
    private function __wakeup(){} 
	private $order_id;
}
