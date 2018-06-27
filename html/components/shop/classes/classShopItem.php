<?php
// Класс вывода товаров
//	$shopItem->addSection($section_id); // из какого раздела / разделов выводить
//	$shopItem->setStart($pq); // устанавливаем с какого товара выводить
//	$shopItem->setQuantity($shopSettings->quantity); // устанавливаем количество выводимых товаров
//	$shopItem->setViewItemWithoutImage(false); // устанавливаем, что выводить все товары, даже без изображений
//	$shopItem->setMode(0);	// 0 - вывести все 1 - вывести только товары со скидкой 2 - вывести только новые товары 3 - выводить и новые товары и\или со скидкой
//  $this->filterStringArr = array();  // массив фильтра поиска типа строка
//  $this->filterNumberArr1 = array();  // массив фильтра поиска типа номер "от"
//  $this->filterNumberArr2 = array();  // массив фильтра поиска типа номер "до"
//  $this->groupingItem = $this->shopSettings->grouping;  // группировка товара по идентификатору группы
//	$shopItem->viewItems(); // Вывести товары
//  $this->typeOut = 0;  // Вывод компонентом, 1 - модулем - добавляет edit_mode
defined('AUTH') or die('Restricted access');

include_once($root."/lib/currency.php");



class classShopItem
{
	// &$_shopSettings - Объект с настройками
	public function __construct(&$_shopSettings)
	{
		$this->domain = $_SERVER['SERVER_NAME'];
		$this->shopSettings = $_shopSettings;
		$this->arrayItem = array();
		$this->arraySection = array();
		$this->quantity = 10;
		$this->start = 0;
		$this->viewHideItem = false;
		$this->allCategoryIfNotAdd = false;
		$this->viewItemWithoutImage = true;
		$this->mode = 0;
		$this->filterStringArr = array();
		$this->filterNumberArr1 = array();
		$this->filterNumberArr2 = array();
		$this->filterPrice1 = 0;
		$this->filterPrice2 = 0;
		$this->priceTypeId = false;
		$this->sorting = $_shopSettings->sorting_items;
		$this->groupingItem = $_shopSettings->grouping;
		$this->typeOut = 1;
		$this->ue = (float)$_shopSettings->ue;

		CCurrency::update();
	}


	// $_idSection - номер раздела с товарам. Должен быть больше нуля
	public function addSection($_idSection)
	{
		$_idSection = intval($_idSection);
		array_push($this->arraySection, $_idSection);
	}


	// $_start - с какого товара произвести вывод. Нуль или больше. 0 по-умолчанию
	public function setStart($_start)
	{
		if($_start >= 0)
		{
			$this->start = $_start;
		}
	}


	// $_quantity - кол-во товаров для вывода. Выводит все по-умолчанию
	public function setQuantity($_quantity)
	{
		if($_quantity >= 0)
		{
			$this->quantity = $_quantity;
		}
	}


	// $_view - true отображать скрытые товары. false по-умолчанию
	public function setHideView($_view)
	{
		$this->viewHideItem = $_view;
	}


	// $_view - true отображать товары из всех категорий если не одной категории не выбрано. false по-умолчанию
	public function setAllCategoryIfNotAdd($_view)
	{
		$this->allCategoryIfNotAdd = $_view;
	}


	// $_view - true отображает товары без фотографий. true - по-умолчанию
	public function setViewItemWithoutImage($_view)
	{
		$this->viewItemWithoutImage = $_view;
	}

	// $_mode - задает тип вывода. 0 - по-умолчанию.
	/*
		0 - вывести все
		1 - вывести только товары со скидкой
		2 - вывести только новые товары
		3 - выводить и новые товары и\или со скидкой
	*/
	public function setMode($_mode)
	{
		$this->mode = intval($_mode);
	}

	public function setFilterString($stringArr)
	{
		$this->filterStringArr = $stringArr;
	}

	public function setFilterNumber($numberArr1, $numberArr2)
	{
		$this->filterNumberArr1 = $numberArr1;
		$this->filterNumberArr2 = $numberArr2;
	}

	public function setPrice($price1 = 0, $price2 = 0)
	{
		$this->filterPrice1 = $price1;
		$this->filterPrice2 = $price2;
	}

	public function setPriceTypeId($priceTypeId)
	{
		$this->priceTypeId = $priceTypeId;
	}

	public function setSorting($sorting)
	{
		$this->sorting = $sorting;
	}

	public function setGroupingItem($groupingItem)
	{
		$this->groupingItem = $groupingItem;
	}

	public function setTypeOut($typeOut)
	{
		$this->typeOut = $typeOut;
	}

	public function getItemSum()
	{
		return $this->itemSum;
	}

	public function getTypeOut()
	{
		return $this->typeOut;
	}

	public function viewItems()
	{
		$this->sqlItems();
		$out = '';

		foreach($this->arrayItem as &$iter)
		{
			$out .= $this->templateItem($iter);
		}

		return $out;
	}

	public function viewItemsRandom()
	{
		$this->sqlItemsRandom();
		$out = '';

		foreach($this->arrayItem as &$iter)
		{
			$out .= $this->templateItem($iter);
		}

		return $out;
	}


	private function sqlItems() 
	{
		global $db;

		if(count($this->arraySection) > 0 || $this->allCategoryIfNotAdd)
		{
			$SQL_query = $this->SQL_query();
			$SQL_sorting = $this->SQL_sorting();

			// --- ТИПЫ ЦЕН ДЛЯ ЗАРЕГИСТРИРОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ ---
			if($this->priceTypeId) // Тип цены (id) для пользователей
			{
				$SQL_pu = 'pi.price price_u,';
				$SQL_pu_case = ",
					CASE currency
					WHEN '0' THEN pi.price
					WHEN '1' THEN pi.price * ".CCurrency::getUSD()."
					WHEN '2' THEN pi.price * ".CCurrency::getEUR()."
					END as price_user
				";
				$SQL_pu_join = "LEFT JOIN com_shop_price_item pi ON pi.item_id = i.id AND pi.price_type_id = '".$this->priceTypeId."' ";
			}
			else
			{
				$SQL_pu = '';
				$SQL_pu_case = '';
				$SQL_pu_join = '';
			}	

			$SQL = "
				SELECT *
				FROM
				(
					SELECT i.id, group_identifier, section, title, pub, ordering, intro_text, i.price, price_old, ".$SQL_pu." currency, quantity, photo, photo_big, photo_more, new, discount, hit, rating, cdate,
					CASE currency
					WHEN '0' THEN i.price
					WHEN '1' THEN i.price * ".CCurrency::getUSD()."
					WHEN '2' THEN i.price * ".CCurrency::getEUR()."
					WHEN '3' THEN i.price * ".$this->ue."
					END as price_c,
					CASE currency
					WHEN '0' THEN price_old
					WHEN '1' THEN price_old * ".CCurrency::getUSD()."
					WHEN '2' THEN price_old * ".CCurrency::getEUR()."
					WHEN '3' THEN price_old * ".$this->ue."
					END as price_old_c
					".$SQL_pu_case."
					FROM com_shop_item i
					".$SQL_pu_join."
				)x
				".$SQL_query."
				ORDER BY ".$SQL_sorting."
				LIMIT ".$this->start.", ".$this->quantity."
			";

			$stmt_item = $db->query($SQL);

			$stmt_item_sum = $db->query("
				SELECT *
				FROM
				(
					SELECT id, group_identifier, section, pub, price, currency, quantity, photo, new, discount, hit, cdate,
					CASE currency
					WHEN '0' THEN price
					WHEN '1' THEN price * ".CCurrency::getUSD()."
					WHEN '2' THEN price * ".CCurrency::getEUR()."
					WHEN '3' THEN price * ".$this->ue."
					END as price_c,
					CASE currency
					WHEN '0' THEN price_old
					WHEN '1' THEN price_old * ".CCurrency::getUSD()."
					WHEN '2' THEN price_old * ".CCurrency::getEUR()."
					WHEN '3' THEN price_old * ".$this->ue."
					END as price_old_c
					FROM com_shop_item
				)x
				".$SQL_query."
			");
			$this->itemSum = $stmt_item_sum->rowCount();

			while($iter = $stmt_item->fetch())
			{
				// Перенесены в шаблоны вывода
				//$iter['price'] = number_format($iter['price_c'], 0, '', ' ');
				//$iter['price_old'] = number_format($iter['price_old_c'], 0, '', ' ');
				
				if($iter['price_c'] > 999999999) $iter['price_c'] = 0;
				if(!empty($iter['price_user'])){$iter['price_c'] = $iter['price_user'];} // Если для пользователя есть специальная цена - заменяем её вместо обычной.

				$iter['title'] = preg_replace("/\\\/", "<br>", $iter['title']);

				array_push($this->arrayItem, array(
					'id' => $iter['id'],
					'group_identifier' => $iter['group_identifier'],
					'title' => $iter['title'],
					'intro_text' => $iter['intro_text'],
					'price' => $iter['price_c'],
					'price_old' => $iter['price_old_c'],
					'quantity' => $iter['quantity'],
					'photo' => $iter['photo'],
					'photo_big' => $iter['photo_big'],
					'photo_more' => $iter['photo_more'],
					'new' => $iter['new'],
					'rating' => $iter['rating'],
					'discount' => $iter['discount'],
					'hit' => $iter['hit'],					
					'pub' => $iter['pub']
				));
			}
		}
	}

	private function sqlItemsRandom()
	{
		global $db;

		if(count($this->arraySection) > 0 || $this->allCategoryIfNotAdd)
		{
			$SQL_query = $this->SQL_query();
			$SQL_sorting = $this->SQL_sorting();
			
			// --- ТИПЫ ЦЕН ДЛЯ ЗАРЕГИСТРИРОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ ---
			if($this->priceTypeId) // Тип цены (id) для пользователей
			{
				$SQL_pu = 'pi.price price_u,';
				$SQL_pu_case = ",
					CASE currency
					WHEN '0' THEN pi.price
					WHEN '1' THEN pi.price * ".CCurrency::getUSD()."
					WHEN '2' THEN pi.price * ".CCurrency::getEUR()."
					WHEN '3' THEN pi.price * ".$this->ue."
					END as price_user
				";
				$SQL_pu_join = "LEFT JOIN com_shop_price_item pi ON pi.item_id = i.id AND pi.price_type_id = '".$this->priceTypeId."' ";
			}
			else
			{
				$SQL_pu = '';
				$SQL_pu_case = '';
				$SQL_pu_join = '';
			}			

			$stmt_item_count = $db->query(
				"SELECT *
				FROM
				(
					SELECT id, group_identifier, section, pub, price, currency, quantity, photo, new, discount, hit, cdate,
					CASE currency
					WHEN '0' THEN price
					WHEN '1' THEN price * ".CCurrency::getUSD()."
					WHEN '2' THEN price * ".CCurrency::getEUR()."
					WHEN '3' THEN price * ".$this->ue."
					END as price_c,
					CASE currency
					WHEN '0' THEN price_old
					WHEN '1' THEN price_old * ".CCurrency::getUSD()."
					WHEN '2' THEN price_old * ".CCurrency::getEUR()."
					WHEN '3' THEN price_old * ".$this->ue."
					END as price_old_c
					FROM com_shop_item
				)x
				".$SQL_query." "
			);

			$dbItemCountMax = $stmt_item_count->rowCount();

			if($this->quantity < $dbItemCountMax){$count = $this->quantity;} else{$count = $dbItemCountMax;} // иначе при $this->quantity >= $dbItemCountMax - попадём в бесконечный цикл

			$random_arr = array(); // для исключения повторения элементов создаём массив тех случайных чисел, которые уже есть
			$i = 1;

			while($i <= $count)
			{
				$random = round(rand(0, ($dbItemCountMax - 1)));

				if(!isset($random_arr[$random])) // только если этого случайного значения ещё нет - делаем запрос к БД
				{
					$random_arr[$random] = 1; //
					$i++;

					$stmt_item = $db->query("
					SELECT *
					FROM
					(
						SELECT i.id, group_identifier, section, title, pub, ordering, intro_text, i.price, price_old, ".$SQL_pu." currency, quantity, photo, photo_big, photo_more, new, discount, hit, rating, cdate,
						CASE currency
						WHEN '0' THEN i.price
						WHEN '1' THEN i.price * ".CCurrency::getUSD()."
						WHEN '2' THEN i.price * ".CCurrency::getEUR()."
						WHEN '3' THEN i.price * ".$this->ue."
						END as price_c,
						CASE currency
						WHEN '0' THEN price_old
						WHEN '1' THEN price_old * ".CCurrency::getUSD()."
						WHEN '2' THEN price_old * ".CCurrency::getEUR()."
						WHEN '3' THEN price_old * ".$this->ue."
						END as price_old_c
						".$SQL_pu_case."
						FROM com_shop_item i
						".$SQL_pu_join."
					)x
					".$SQL_query."
					ORDER BY ".$SQL_sorting."
					LIMIT ".$random.", 1
					");

					while($iter = $stmt_item->fetch())
					{
						//$iter['price'] = number_format($iter['price_c'], 0, '', ' ');
						//$iter['price_old'] = number_format($iter['price_old_c'], 0, '', ' ');
				
						if($iter['price_c'] > 999999999) $iter['price_c'] = 0;
						if(!empty($iter['price_user'])){$iter['price_c'] = $iter['price_user'];}					

						array_push($this->arrayItem, array(
							'id' => $iter['id'],
							'group_identifier' => $iter['group_identifier'],
							'title' => $iter['title'],
							'pub' => $iter['pub'],
							'intro_text' => $iter['intro_text'],
							'price' => $iter['price_c'],
							'price_old' => $iter['price_old_c'],
							'quantity' => $iter['quantity'],
							'photo' => $iter['photo'],
							'photo_big' => $iter['photo_big'],
							'photo_more' => $iter['photo_more'],
							'new' => $iter['new'],
							'discount' => $iter['discount'],
							'hit' => $iter['hit'],
							'rating' => $iter['rating']
						));
					}
				}
			}
		}
	}

	private function SQL_query()
	{
		$strReturn = " WHERE 1 ";

		if(!$this->viewHideItem)
		{
			$strReturn .= " AND pub = '1' ";
		}

		if(count($this->arraySection) > 0)
		{
			$listSection = implode(",", $this->arraySection);

			if($listSection != "all")
			{
				$strReturn .= " AND section IN (".$listSection.") ";
			}
		}

		// Проверяем что бы "от" не было больше чем "до"
		if($this->filterPrice1 >= $this->filterPrice2 && $this->filterPrice2 != 0){$this->filterPrice1 = 0;}

		// Добавляем запрос в бд
		if($this->filterPrice1 > 0)
		{
			$strReturn .= ' AND price_c >=  "'.$this->filterPrice1.'"';
		}

		if($this->filterPrice2 > 0)
		{
			$strReturn .= ' AND price_c <=  "'.$this->filterPrice2.'"';
		}

		if(!$this->viewItemWithoutImage)
		{
			$strReturn .= " AND photo != '' AND photo_big != '' ";
		}

		switch($this->mode)
		{
			case 1:
			{
				$strReturn .= " AND discount = '1' ";
			} break;

			case 2:
			{
				$strReturn .= " AND new = '1' ";
			} break;

			case 3:
			{
				$strReturn .= " AND (discount = '1' OR new = '1') ";
			} break;
			
			case 4:
			{
				$strReturn .= " AND hit = '1' ";
			} break;			
		}

		// фильтр по характеристикам типа string
		if(count($this->filterStringArr) > 0)
		{
			foreach ($this->filterStringArr as $char_id => $value_s)
			{

				if(gettype($value_s) == 'string') // Когда фильтр <select>
				{
					$value_s = trim(htmlspecialchars(strip_tags($value_s)));
					$char_value_sql = "AND value = '".$value_s."'";
				}
				if(gettype($value_s) == 'array') // Когда фильтр <checkbox> с множеством значений
				{
					$value_str = "'".implode("','", $value_s)."'";
					$value_str = trim(htmlspecialchars(strip_tags($value_str)));
					$char_value_sql = "AND value IN(".$value_str.")";
				}

				if ($value_s != '')
				{
					 $strReturn .= "
					 AND id IN (
						SELECT item_id
						FROM com_shop_char
						WHERE name_id = '".intval($char_id)."'
						".$char_value_sql."
					)
					 ";
				}
			}
		}


		// Фильтр по характеристикм типа number
		if(isset($this->filterNumberArr1) || isset($this->filterNumberArr2))
		{
			foreach ($this->filterNumberArr1 as $char_id => $value_n1)
			{
				$value_n1 = (float)$value_n1;
				$value_n2 = (float)$this->filterNumberArr2[$char_id];

				if ($value_n2 > 0){$sql_n2 = " AND value <= '".$value_n2."'+0 ";} else {$sql_n2 = '';} // Когда прибавляем "0" - то приводим строку к числу

				// ищем только в том случае, если хотя бы одно поле заполнено
				if ($value_n1 != 0 || $value_n2 != 0)
				{
					 $strReturn .= "
					 AND id IN (
						SELECT item_id
						FROM com_shop_char
						WHERE name_id = '".intval($char_id)."'
						AND value >= '".$value_n1."'+0 ".$sql_n2."
					 )
					 ";
				}
			}
		}


		if($this->groupingItem == 1)
		{
			$strReturn .= " GROUP BY group_identifier";
		}


		return $strReturn;
	}


	private function SQL_sorting()
	{
		switch($this->sorting)
		{
			case 0:
				$sorting_sql = 'ordering ASC';
				break;

			case 1:
				$sorting_sql = 'price_c ASC';
				break;

			case 2:
				$sorting_sql = 'price_c DESC';
				break;

			case 3:
				$sorting_sql = 'title ASC';
				break;

			case 4:
				$sorting_sql = 'title DESC';
				break;

			case 5:
				$sorting_sql = 'cdate DESC';
				break;
		}

		return $sorting_sql;
	}

	// $_item - элемент массива с товаром
	protected function templateItem($_item)
	{
		$id = $_item['id'];
		$title = $_item['title'];
		$price = $_item['price'];
		$photo_path = $_item['photo'];

		return '
			<div>
				<a href="/shop/item/'.$id.'">'.$title.'</a><br />
				<img src="/components/shop/photo/'.$photo_path.'" /><br />
				'.$price.' руб.
			</div><br />
		';
	}

	public function debug()
	{
		echo "Вывод из разделов: ";
		foreach($this->arraySection as $id)
		{
			echo $id.', ';
		}
		echo "<br />В количестве: ".$this->quantity."<br />Начиная с: ".$this->start."<br />";
	}


	protected $domain;

	protected $shopSettings;

	private $arrayItem;
	private $arraySection;
	private $quantity;
	private $start;
	private $viewHideItem;
	private $allCategoryIfNotAdd;
	private $viewItemWithoutImage;
	private $mode;
	private $filterStringArr;
	private $filterNumberArr1;
	private $filterNumberArr2;
	private $filterPrice1;
	private $filterPrice2;
	private $sorting;
	private $priceTypeId;
	private $groupingItem;
	private $typeOut;
	private $itemSum;
};

?>
