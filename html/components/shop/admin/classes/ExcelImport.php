<?php

include_once './classes/Excel.php';

const EXCEL_CURRENCY_RUB = 'rub';
const EXCEL_CURRENCY_USD = 'usd';
const EXCEL_CURRENCY_EUR = 'eur';

const EXCEL_NAME_COLUMN_SECTION = 'Раздел';
const EXCEL_NAME_COLUMN_CHARACTERISTIC = 'Характеристика';

class ExcelImport extends Excel
{
	public function __construct($_pathToFile)
	{
		parent::__construct($_pathToFile);

		$this->loadSections();
		$this->loadItems();
		$this->loadCharacteristics();
		$this->loadColumns();
	}

	static public function deleteAllItems()
	{
		global $db;

		$db->query('DELETE FROM com_shop_char_name');
		$db->query('DELETE FROM com_shop_char');
		$db->query('DELETE FROM com_shop_filter');
		$db->query('DELETE FROM com_shop_item');
		$db->query('DELETE FROM com_shop_section');
		$db->query('DELETE FROM menu WHERE component = "shop" AND p1 = "section"');

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo'))
		{
			foreach (glob($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/*.jpg') as $file)
				unlink($file);
		}
	}

	public function run()
	{
	   	$array = $this->sheet->toArray();
	    array_shift($array);

		for($i = 0;$i < count($array);$i++)
		{
			// Вставка категорий
			$arraySections = array();
			for($s = 0;$s < $this->maxDepthSections;$s++)
			{
				if(count($array[$i][$s]) > 0)
					$arraySections[] = $array[$i][$s];
			}

			$menuID = $this->createPath($arraySections);

			// Получение данных о товаре
			$itemName = $array[$i][$this->arrayColumns['title']['number']];
			$itemIdentifier = $array[$i][$this->arrayColumns['identifier']['number']];
			$itemPub = 1;
			$itemAction = $array[$i][$this->arrayColumns['action']['number']];
			$itemIntroText = $array[$i][$this->arrayColumns['intro_text']['number']];
			$itemFullText = $array[$i][$this->arrayColumns['full_text']['number']];
			$itemPrice = $array[$i][$this->arrayColumns['price']['number']];
			$itemPriceOld = $array[$i][$this->arrayColumns['price_old']['number']];
			$itemCurrency = $array[$i][$this->arrayColumns['currency']['number']];
			$itemPhoto = $array[$i][$this->arrayColumns['image']['number']];
			$itemGallery = $array[$i][$this->arrayColumns['gallery']['number']];
			$itemNew = $array[$i][$this->arrayColumns['new']['number']];
			$itemDiscount = $array[$i][$this->arrayColumns['discount']['number']];

			if($itemAction == "удалить")
			{
				$this->deleteItemByIdentifier($itemIdentifier);
				continue;
			}
			else if($itemAction == "скрыт")
			{
				$itemPub = 0;
			}

			switch($itemCurrency)
			{
				case EXCEL_CURRENCY_USD:
					$itemCurrency = 1;
					break;

				case EXCEL_CURRENCY_EUR:
					$itemCurrency = 2;
					break;

				default:
					$itemCurrency = 0;
			}

			if($itemNew == "Да")
				$itemNew = 1;
			else
				$itemNew = 0;

			if($itemDiscount == "Да")
				$itemDiscount = 1;
			else
				$itemDiscount = 0;

			$this->updateItem($itemName, $itemIdentifier, $menuID, $itemPub,
								$itemIntroText, $itemFullText,
								$itemPrice, $itemPriceOld, $itemCurrency,
								$itemPhoto, $itemGallery, $itemNew, $itemDiscount);

			// Характеристики
			foreach($this->arrayCharacteristicsColumns as $charID => $columnNumber)
			{
				$strChar = $array[$i][$columnNumber];
				if(strlen($strChar) > 0)
				{
					$arrayChar = explode(';', $strChar);

					foreach ($arrayChar as $iter)
					{
						if(strlen($iter) > 0 && count($this->getCharacteristic($itemIdentifier, $charID, $iter) == 0))
							$this->addCharacteristic($itemIdentifier, $charID, $iter);
					}
				}
			}
		}
	}

	private function createSection($_name, $_parentID = 0)
	{
		global $db;

		$arrayOut = array('id' => 0, 'sectionID' => 0);

		if(strlen($_name) == 0)
			return NULL;

		$parentID = 0;

		// поиск родителя
		if($_parentID > 0)
		{
			$arrayParent = $this->getSectionByID($_parentID);

			if(count($arrayParent) > 0)
				$parentID = $_parentID;
		}

		$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_section (pub, parent, title) VALUES (1, 0, :title)');
		$SQL_PREPARE->execute(array('title' => $_name));

		$arrayOut['sectionID'] = $db->lastInsertId();

		$SQL_PREPARE = $db->prepare('INSERT INTO menu (menu_type, name, description, pub, parent, ordering, component, main, p1, id_com)
										VALUES (:menu_type, :name, "раздел интернет-магазина", 1, :parent, :ordering, "shop", 0, "section", :id_com)');
		$SQL_PREPARE->execute(array(
								'menu_type' => "left",
								'name' => $_name,
								'parent' => $parentID,
								'ordering' => $arrayOut['sectionID'],
								'id_com' => $arrayOut['sectionID']
							));

		$arrayOut['id'] = $db->lastInsertId();

		// Так же вставляем пункт в массив
		$this->arraySections[] = array(
				'id' => $arrayOut['id'],
				'parent' => $parentID,
				'name' => $_name,
				'id_com' => $arrayOut['sectionID']
			);

		return $arrayOut;
	}

	private function getSectionByID($_id)
	{
		foreach($this->arraySections as $iter)
		{
			if($iter['id'] == $_id)
				return $iter;
		}
	}

	private function getSectionByName($_name)
	{
		$arrayOut = array();

		foreach($this->arraySections as $iter)
		{
			if($iter['name'] == $_name)
				$arrayOut[] = $iter;
		}

		return $arrayOut;
	}

	private function updateItem($_name, $_identifier, $_menuID, $_pub, $_introText, $_fullText, $_price, $_priceOld, $_currency, $_photo, $_gallery, $_new, $_discount)
	{
		global $db;

		$_name = (string)$_name;
		$_identifier = (string)$_identifier;
		$_menuID = (int)$_menuID;
		$_pub = (int)$_pub;
		$_introText = (string)$_introText;
		$_fullText = (string)$_fullText;
		$_price = (int)$_price;
		$_priceOld = (int)$_priceOld;
		$_currency = (int)$_currency;
		$_photo = (string)$_photo;
		$_gallery = (string)$_gallery;
		$_new = (int)$_new;
		$_discount = (int)$_discount;

		$arraySection = $this->getSectionByID($_menuID);

		if(count($arraySection) == 0)
			return false;

		if(strlen($_name) == 0 || strlen($_identifier) == 0)
			return false;

		$sectionID = $arraySection['id_com'];

		if(count($this->getItemByIdentifier($_identifier)) > 0)
		{
			$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET section = :section, pub = :pub, parent = 1, title = :title,
											intro_text = :intro_text, full_text = :full_text, price = :price, price_old = :price_old, currency = :currency,
											photo = :photo, photo_big = :photo_big, photo_more = :photo_more,
											new = :new, discount = :discount, cdate = NOW()
										WHERE identifier = :identifier');

			$SQL_PREPARE->execute(
				array(
					'section' => $sectionID,
					'pub' => $_pub,
					'title' => $_name,
					'intro_text' => $_introText,
					'full_text' => $_fullText,
					'price' => $_price,
					'price_old' => $_priceOld,
					'currency' => $_currency,
					'photo' => $_photo,
					'photo_big' => $_photo,
					'photo_more' => $_gallery,
					'new' => $_new,
					'discount' => $_discount,
					'identifier' => $_identifier
				)
			);

			$item = $this->getItemByIdentifier($_identifier);

			$arrayPhoto = array();
			$arrayPhoto[] = $item['photo'];
			$arrayPhoto[] = $item['photo_big'];

			$gallery = explode(';', $item['photo_more']);

			foreach($gallery as &$iter)
			{
				if(strlen($iter) > 0)
				{
					$arrayPhoto[] = $iter;
					$arrayPhoto[] = str_replace('.jpg', '_.jpg', $iter);;
				}
			}

			foreach($arrayPhoto as $iter)
			{
				if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter))
					unlink($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter);
			}
		}
		else
		{
			$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_item
											(identifier, section, pub, parent, title, intro_text, full_text, price, price_old, currency, quantity, photo, photo_big, photo_more, new, discount, cdate) VALUES
											(:identifier, :section, :pub, :parent, :title, :intro_text, :full_text, :price, :price_old, :currency, :quantity, :photo, :photo_big, :photo_more, :new, :discount, NOW())'
										);

			$SQL_PREPARE->execute(
				array(
					'identifier' => $_identifier,
					'section' => $sectionID,
					'pub' => $_pub,
					'parent' => 1,
					'title' => $_name,
					'intro_text' => $_introText,
					'full_text' => $_fullText,
					'price' => $_price,
					'price_old' => $_priceOld,
					'currency' => $_currency,
					'photo' => $_photo,
					'photo_big' => $_photo,
					'photo_more' => $_gallery,
					'quantity' => 1,
					'new' =>  $_new,
					'discount' => $_discount
				)
			);

			$id = $db->lastInsertId();

			$this->arrayItems[] = array(
				'id' => $id,
				'pub' => $_pub,
				'identifier' => $_identifier,
				'section' => $_menuID,
				'title' => $_name,
				'intro_text' => $_introText,
				'full_text' => $_fullText,
				'price' => $_price,
				'price_old' => $_priceOld,
				'currency' => $_currency,
				'photo' => $_photo,
				'photo_big' => $_photo,
				'photo_more' => $_gallery,
				'new' => $_new,
				'discount' => $_discount
			);
		}

		return true;
	}

	private function deleteItemByIdentifier($_identifier)
	{
		global $db;

		$SQL_PREPARE = $db->prepare('DELETE FROM com_shop_item WHERE identifier = :identifier');

		$SQL_PREPARE->execute(
			array(
				'identifier' => $_identifier
			)
		);

		foreach($this->arrayItems as $key => &$iter)
		{
			if($iter['identifier'] == $_identifier)
			{
				unset($this->arrayItems[$key]);
				break;
			}
		}
	}

	private function getItemByID($_id)
	{
		foreach($this->arrayItems as &$iter)
		{
			if($iter['id'] == $_identifier)
				return $iter;
		}

		return array();
	}

	private function getItemByIdentifier($_identifier)
	{
		foreach($this->arrayItems as &$iter)
		{
			if($iter['identifier'] == $_identifier)
				return $iter;
		}

		return array();
	}

	private function createPath($_arrayPath)
	{
		$parentID = 0;

		while(count($_arrayPath) > 0)
		{
			$sectionExist = false;
			$sectionName = array_shift($_arrayPath);
			$sectionArray = $this->getSectionByName($sectionName);

			foreach($sectionArray as $iter)
			{
				if(($iter['parent'] == 0 && $parentID == 0) || $iter['parent'] == $parentID)
				{
					$sectionExist = true;
					$parentID = $iter['id'];
					break;
				}
			}

			if(!$sectionExist)
			{
				$array = $this->createSection($sectionName, $parentID);
				$parentID = $array['id'];
			}
		}

		return $parentID;
	}

	private function getCharacteristicName($_name, $_unit, $_type)
	{
		foreach($this->arrayCharacteristicsName as $iter)
		{
			if($iter['name'] == $_name && $iter['unit'] == $_unit && $iter['type'] == $_type)
				return $iter;
		}

		return array();
	}

	private function getCharacteristicNameByID($_id)
	{
		foreach($this->arrayCharacteristicsName as $iter)
		{
			if($iter['id'] == $_id)
				return $iter;
		}

		return array();
	}

	private function createCharacteristicName($_name, $_unit, $_type)
	{
		global $db;

		$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_char_name (name, unit, type) VALUES (:name, :unit, :type)');
		$SQL_PREPARE->execute(
			array(
				'name' => $_name,
				'unit' => $_unit,
				'type' => $_type
			)
		);

		$id = $db->lastInsertId();

		$this->arrayCharacteristicsName[] = array(
				'id' => $id,
				'name' => $_name,
				'unit' => $_unit,
				'type' => $_type,
				'ordering' => 0
			);

		return end($this->arrayCharacteristicsName);
	}

	private function getCharacteristic($_itemID, $_charID, $_value)
	{
		foreach($this->arrayCharacteristics as $iter)
		{
			if($iter['item_id'] == $_itemID && $iter['name_id'] == $_charID && $iter['value'] == $_value)
				return $iter;
		}

		return array();
	}

	private function addCharacteristic($_identifier, $_charID, $_value)
	{
		global $db;

		$arrayItem = $this->getItemByIdentifier($_identifier);

		if(count($arrayItem) == 0)
			return 0;

		if(count($this->getCharacteristicNameByID($_charID)) == 0)
			return 0;

		$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_char (item_id, name_id, value, status) VALUES (:item_id, :name_id, :value, 1)');
		$SQL_PREPARE->execute(
			array(
				'item_id' => $arrayItem['id'],
				'name_id' => $_charID,
				'value' => $_value
			)
		);

		$id = $db->lastInsertId();

		$this->arrayCharacteristics[] = array(
				'id' => $id,
				'item_id' => $arrayItem['id'],
				'name_id' => $_charID,
				'value' => $_value,
				'ordering' => 0,
				'status' => 1
			);

		return end($this->arrayCharacteristics);
	}

	private function loadSections()
	{
		global $db;
		$SQL_PREPARE = $db->query('SELECT id, parent, name, id_com FROM menu WHERE component = "shop" AND pub = 1');
		$this->arraySections = $SQL_PREPARE->fetchAll();
	}

	private function loadItems()
	{
		global $db;
		$SQL_PREPARE = $db->query('SELECT
									i.id, i.pub, i.identifier, m.id AS section,
									i.title, i.intro_text, i.full_text,
									i.price, i.price_old, i.currency,
									i.photo, i.photo_big, i.photo_more,
									i.new, i.discount
								FROM com_shop_item AS i
								JOIN menu AS m
                                WHERE i.section = m.id_com
								ORDER BY i.section DESC');
		$this->arrayItems = $SQL_PREPARE->fetchAll();
	}

	private function loadCharacteristics()
	{
		global $db;

		$SQL_PREPARE = $db->query('SELECT * FROM com_shop_char_name');
		$this->arrayCharacteristicsName = $SQL_PREPARE->fetchAll();

		$SQL_PREPARE = $db->query('SELECT * FROM com_shop_char');
		$this->arrayCharacteristics = $SQL_PREPARE->fetchAll();
	}

	private function loadColumns() // считает позиции колонок
	{
		for($x = 0;;$x++)
		{
			$value = $this->getCell($x, 0);

			if(strlen($value) == 0)
			{
				break;
			}
			else if($value == EXCEL_NAME_COLUMN_SECTION)
			{
				$this->maxDepthSections++;
			}
			else
			{
				$arrayValue = explode(';', $value);

				if($arrayValue[0] == EXCEL_NAME_COLUMN_CHARACTERISTIC) // Загрузка характеристик
				{
					$arrayChar = array();
					$name = (string)$arrayValue[1];
					$unit = (string)$arrayValue[2];
					$type = (string)$arrayValue[3];

					if(strlen($name) == 0)
						continue;

					if($type == 'число')
						$type = 'number';
					else
						$type = 'string';

					$arrayChar = $this->getCharacteristicName($name, $unit, $type);

					if(count($arrayChar) == 0)
						$arrayChar = $this->createCharacteristicName($name, $unit, $type);

					if($arrayChar['id'] > 0)
						$this->arrayCharacteristicsColumns[$arrayChar['id']] = $x;
				}
				else
				{
					foreach($this->arrayColumns as &$iter)
					{
						if($value == $iter['name'])
						{
							$iter['number'] = $x;
						}
					}
				}
			}
		}
	}

	protected $arraySections = array();
	protected $arrayItems = array();
	protected $maxDepthSections = 0;

	protected $arrayColumns = array(
		'identifier' => array('name' => 'Идентификатор', 'number' => 0),
		'title' => array('name' => 'Наименование', 'number' => 0),
		'intro_text' => array('name' => 'Вводный текст', 'number' => 0),
		'full_text' => array('name' => 'Детальное описание', 'number' => 0),
		'price' => array('name' => 'Цена', 'number' => 0),
		'price_old' => array('name' => 'Цена со скидкой', 'number' => 0),
		'discount' => array('name' => 'Распродажа', 'number' => 0),
		'currency' => array('name' => 'Валюта', 'number' => 0),
		'new' => array('name' => 'Новинка', 'number' => 0),
		'image' => array('name' => 'Изображение', 'number' => 0),
		'gallery' => array('name' => 'Галерея', 'number' => 0),
		'action' => array('name' => 'Действие', 'number' => 0)
	);

	protected $arrayCharacteristicsColumns = array();
	protected $arrayCharacteristicsName = array();
	protected $arrayCharacteristics = array();
};
