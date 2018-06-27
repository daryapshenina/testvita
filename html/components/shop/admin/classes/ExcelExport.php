<?php

include_once './classes/Excel.php';

const EXCEL_CURRENCY_RUB = 0;
const EXCEL_CURRENCY_USD = 1;
const EXCEL_CURRENCY_EUR = 2;

class ExcelExport extends Excel
{
	public function __construct()
	{
		parent::__construct();

		$this->sheet->setTitle('Товары с сайта');
		$this->sheet->getDefaultRowDimension()->setRowHeight(18); // высота строк
		$this->fixRow(1);

		$this->arraySections = array();
		$this->arrayItems = array();
		$this->arrayCharacteristics = array();

		$this->loadSections();
		$this->loadItems();
		$this->loadCharacteristics();
		$this->calculateNumbersOfColumns();
	}

	public function run()
	{
		for($i = 0;$i < $this->maxDepthSections;$i++)
		{
			$this->createCell($i, 1, $this->arrayColumns['section']['name'], 20, '0084D1', 'ffffff');
		}

		$this->createCell($this->arrayColumns['identifier']['number'], 1, $this->arrayColumns['identifier']['name'], 20, '6C3800', 'ffffff');
		$this->createCell($this->arrayColumns['title']['number'], 1, $this->arrayColumns['title']['name'], 30, '6D6D6D', 'ffffff');
		$this->createCell($this->arrayColumns['intro_text']['number'], 1, $this->arrayColumns['intro_text']['name'], 20, '6D6D6D', 'ffffff');
		$this->createCell($this->arrayColumns['full_text']['number'], 1, $this->arrayColumns['full_text']['name'], 20, '6D6D6D', 'ffffff');
		$this->createCell($this->arrayColumns['price']['number'], 1, $this->arrayColumns['price']['name'], 10, 'CE0000', 'ffffff');
		$this->createCell($this->arrayColumns['price_old']['number'], 1, $this->arrayColumns['price_old']['name'], 15, 'CE0000', 'ffffff');
		$this->createCell($this->arrayColumns['discount']['number'], 1, $this->arrayColumns['discount']['name'], 15, 'CE0000', 'ffffff');
		$this->createCell($this->arrayColumns['currency']['number'], 1, $this->arrayColumns['currency']['name'], 10, 'CE0000', 'ffffff');
		$this->createCell($this->arrayColumns['new']['number'], 1, $this->arrayColumns['new']['name'], 10, '00801B', 'ffffff');
		$this->createCell($this->arrayColumns['image']['number'], 1, $this->arrayColumns['image']['name'], 20, '97008B', 'ffffff');
		$this->createCell($this->arrayColumns['gallery']['number'], 1, $this->arrayColumns['gallery']['name'], 20, '97008B', 'ffffff');
		$this->createCell($this->arrayColumns['action']['number'], 1, $this->arrayColumns['action']['name'], 15, '5B5B5B', 'ffffff');

		$charNumCol = $this->arrayColumns['characteristics']['number'];
		foreach($this->arrayCharacteristics as $iter)
		{
			$title = $this->arrayColumns['characteristics']['name'].';'.$iter['name'].';'.$iter['unit'].';'.$iter['type'];
			$this->createCell($charNumCol, 1, $title, 30, '730061', 'ffffff');
			$charNumCol++;
		}

		$y = 2;
		foreach($this->arrayItems as $itemID => $iter)
		{
			$arraySections = $this->getParentSections($iter['section']);

			if(count($arraySections) <= 0)
			{
				continue;
			}

			for($x = 0;$x < count($arraySections);$x++)
			{
				$this->createCell($x, $y, $arraySections[$x]);
			}

			if(strlen($iter['identifier']) <= 0)
			{
				$iter['identifier'] = $this->updateIdentifier($itemID);
			}

			$action = '';
			if(!$iter['pub'])
			{
				$action = 'скрыт';
			}

			if($iter['price'] == 0)
			{
				$iter['price'] = '';
			}

			if($iter['price_old'] == 0)
			{
				$iter['price_old'] = '';
			}

			switch($iter['currency'])
			{
				case EXCEL_CURRENCY_USD:
					$iter['currency'] = 'usd';
					break;

				case EXCEL_CURRENCY_EUR:
					$iter['currency'] = 'eur';
					break;

				default:
					$iter['currency'] = '';
			}

			if($iter['new'])
			{
				$iter['new'] = 'Да';
			}
			else
			{
				$iter['new'] = '';
			}

			if($iter['discount'])
			{
				$iter['discount'] = 'Да';
			}
			else
			{
				$iter['discount'] = '';
			}

			$this->createCell($this->arrayColumns['identifier']['number'], $y, $iter['identifier']);
			$this->createCell($this->arrayColumns['title']['number'], $y, $iter['title']);
			$this->createCell($this->arrayColumns['intro_text']['number'], $y, $iter['intro_text']);
			$this->createCell($this->arrayColumns['full_text']['number'], $y, $iter['full_text']);
			$this->createCell($this->arrayColumns['price']['number'], $y, $iter['price']);
			$this->createCell($this->arrayColumns['price_old']['number'], $y, $iter['price_old']);
			$this->createCell($this->arrayColumns['discount']['number'], $y, $iter['discount']);
			$this->createCell($this->arrayColumns['currency']['number'], $y, $iter['currency']);
			$this->createCell($this->arrayColumns['new']['number'], $y, $iter['new']);
			$this->createCell($this->arrayColumns['image']['number'], $y, $iter['photo_big']);
			$this->createCell($this->arrayColumns['gallery']['number'], $y, $iter['photo_more']);
			$this->createCell($this->arrayColumns['action']['number'], $y, $action);

			$charNumCol = $this->arrayColumns['characteristics']['number'];
			foreach($this->arrayCharacteristics as $charID => $iter)
			{
				$value = $this->getStringCharacteristic($charID, $itemID);
				$this->createCell($charNumCol, $y, $value);
				$charNumCol++;
			}

			$y++;
		}
	}

	private function loadSections()
	{
		global $db;

		$arraySections = $db->query('SELECT id, pub, parent, name FROM menu WHERE component = "shop"');

		foreach ($arraySections as $iter)
		{
			$this->arraySections[$iter['id']] = array(
				'pub' => $iter['pub'],
				'parent' => $iter['parent'],
				'name' => $iter['name']
			);
		}
	}

	private function loadItems()
	{
		global $db;

		$arrayItems = $db->query('SELECT i.id, i.pub, i.identifier, m.id AS section,
									i.title, i.intro_text, i.full_text,
									i.price, i.price_old, i.currency,
									i.photo_big, i.photo_more,
									i.new, i.discount
								FROM com_shop_item AS i
								JOIN menu AS m
                                WHERE i.section = m.id_com
								ORDER BY i.section DESC');

		foreach ($arrayItems as $iter)
		{
			$this->arrayItems[$iter['id']] = array(
				'pub' => $iter['pub'], 'identifier' => $iter['identifier'], 'section' => $iter['section'],
				'title' => $iter['title'], 'intro_text' => $iter['intro_text'], 'full_text' => $iter['full_text'],
				'price' => $iter['price'], 'price_old' => $iter['price_old'], 'currency' => $iter['currency'],
				'photo_big' => $iter['photo_big'], 'photo_more' => $iter['photo_more'],
				'new' => $iter['new'], 'discount' => $iter['discount']
			);
		}
	}

	private function loadCharacteristics()
	{
		global $db;

		$arrayCharacteristics = $db->query('SELECT id, name, unit, type FROM com_shop_char_name ORDER BY ordering');

		foreach ($arrayCharacteristics as $iter)
		{
			$SQL_PREPARE = $db->prepare('SELECT item_id, value FROM com_shop_char WHERE name_id = :name_id');
			$SQL_PREPARE->execute(array('name_id' => $iter['id']));
			$arrayItemsCharacteristics = $SQL_PREPARE->fetchAll();

			switch($iter['type'])
			{
				case 'number':
					$iter['type'] = 'число';
					break;

				case 'string':
					$iter['type'] = 'строка';
					break;
			}

			$this->arrayCharacteristics[$iter['id']] = array(
				'name' => $iter['name'],
				'unit' => $iter['unit'],
				'type' => $iter['type'],
				'array' => $arrayItemsCharacteristics
			);
		}
	}

	private function getStringCharacteristic($_charID, $_itemID)
	{
		$output = "";

		if(!array_key_exists($_charID, $this->arrayCharacteristics))
			return $output;

		foreach($this->arrayCharacteristics[$_charID]['array'] as $iter)
		{
			if($iter['item_id'] == $_itemID)
				$output .= $iter['value'].';';
		}

		return $output;
	}

	private function getParentSections($_id, &$_array = array())
	{
		if(!array_key_exists($_id, $this->arraySections))
			return array_splice($_array, 0);

		if(!$this->arraySections[$_id]['pub'])
			return array_splice($_array, 0);

		array_unshift($_array, $this->arraySections[$_id]['name']);

		if($this->arraySections[$_id]['parent'] > 0)
			$this->getParentSections($this->arraySections[$_id]['parent'], $_array);

		return $_array;
	}

	private function getMaxDepthSections($_id, &$_level = 1) // рекурсия высчитывает максимальную вложенность категорий
	{
		if(!array_key_exists($_id, $this->arraySections))
			return 0;

		$_level++;

		if($_level > $this->maxDepthSections)
			$this->maxDepthSections = $_level;

		if($this->arraySections[$_id]['parent'] > 0)
			$this->getMaxDepthSections($this->arraySections[$_id]['parent'], $_level);
	}

	private function calculateNumbersOfColumns() // считает позиции колонок
	{
		foreach($this->arraySections as $key => $iter)
		{
			if($iter['parent'] > 0)
			{
				$this->getMaxDepthSections($iter['parent']);
			}
		}

		$i = $this->maxDepthSections - 1;
		foreach($this->arrayColumns as &$iter)
		{
			$iter['number'] = $i++;
		}
	}

	private function generateIdentifier()
	{
		return substr((md5((date("F j, Y, g:i a").rand(0, 1000000)))), 0, 15);
	}

	private function updateIdentifier($_id)
	{
		global $db;

		$identifier = $this->generateIdentifier();

		$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET identifier = :identifier WHERE id = :id');
		$SQL_PREPARE->execute(
			array(
				'id' => $_id,
				'identifier' => $identifier
			)
		);

		return $identifier;
	}

	protected $arraySections;
	protected $arrayItems;
	protected $arrayCharacteristics;
	protected $maxDepthSections = 1;

	protected $arrayColumns = array(
		'section' => array('name' => 'Раздел', 'number' => 0),
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
		'action' => array('name' => 'Действие', 'number' => 0),
		'characteristics' => array('name' => 'Характеристика', 'number' => 0)
	);
};
