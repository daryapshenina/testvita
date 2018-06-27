<?php
// DAN 2015
// getArray($_itemID) - получить массив хaрaктеристик
// getTemplate($_itemID) - подключить шаблон вывода
// addChar($_itemID, $_itemID, $_value, $_ordering = 0, $_status = 1) - вставить характеристики
// updateChar($_id, $_itemID, $_nameID, $_value, $_ordering = 0, $_status = 1) - обновить характеристики
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/classes/classShopSettings.php";

class Chars
{
   function __construct($_itemID = 0, $_groupIdentifier = 0)
   {
	   global $shopSettings;
		if($_groupIdentifier != '0')
			$this->charGrouping = $shopSettings->grouping;

		$this->charsArrayDB($_itemID, $_groupIdentifier);
   }


	/*
		Возвращает массив характеристик
	*/
	public function getArray()
	{
		return $this->charsArray;
	}


	/*
		Возвращает шаблон характеристик
	*/
	public function getTemplate($_itemID)
	{
		return $this->template($_itemID);
	}


	/*
		Вставить название характеристики
		Возвращает ID вставленной характеристики
	*/
	static public function addName($_name, $_unit = '', $_type = 'string', $_ordering = 0)
	{
		global $db;

		$_name = (string)$_name;
		$_unit = (string)$_unit;
		$_type = (string)$_type;
		$_ordering = (int)$_ordering;

		if(strlen($_name) == 0)
			return;

		if($_type != 'string')
			$_type = 'number';

		$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_char_name (identifier, name, unit, type, ordering) VALUES (:identifier, :name, :unit, :type, :ordering)');
		$SQL_PREPARE->execute(
			array(
				'name' => $_name,
				'unit' => $_unit,
				'type' => $_type,
				'ordering' => $_ordering,
				'identifier' => ''
			)
		);

		return $db->lastInsertId();
	}


	/*
		Обновить название характеристики
	*/
	static public function updateName($_id, $_name, $_unit = null, $_type = null, $_ordering = null)
	{
		global $db;

		$_id = (int)$_id;
		$_name = (string)$_name;
		$_unit = (string)$_unit;
		$_type = (string)$_type;
		$_ordering = (int)$_ordering;

		$char = Chars::getName($_id);

		if(count($char) == 0)
			return;

		if(strlen($_name) == 0 || $_name == null)
			$_name = $char[0]['name'];

		if($_unit == null)
			$_unit = $char[0]['unit'];

		if($_type == null)
			$_type = $char[0]['type'];

		if($_ordering == null)
			$_ordering = $char[0]['ordering'];

		if($_type != 'string')
			$_type = 'number';

		$SQL_PREPARE = $db->prepare('UPDATE com_shop_char_name SET name = :name, unit = :unit, type = :type, ordering = :ordering WHERE id = :id');

		$SQL_PREPARE->execute(
			array(
				'id' => $_id,
				'name' => $_name,
				'unit' => $_unit,
				'type' => $_type,
				'ordering' => $_ordering
			)
		);
	}


	/*
		Получить название характеристики
	*/
	static public function getName($_id)
	{
		global $db;

		$_id = (int)$_id;

		$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_char_name WHERE id = :id');
		$SQL_PREPARE->execute(
			array(
				'id' => $_id
			)
		);

		return $SQL_PREPARE->fetchAll();
	}


	/*
		Возвращает ID характеристики, иначе значение ниже нуля
	*/
	static public function getNameID($_name, $_unit, $_type)
	{
		global $db;

		$_name = (string)$_name;
		$_unit = (string)$_unit;
		$_type = (string)$_type;

		$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_char_name WHERE name = :name AND unit = :unit AND type = :type LIMIT 1');
		$SQL_PREPARE->execute(
			array(
				'name' => $_name,
				'unit' => $_unit,
				'type' => $_type
			)
		);

		$array = $SQL_PREPARE->fetchAll();

		if(count($array) > 0)
			return $array[0]['id'];

		return -1;
	}


	/*
		Вставляем характеристику
		Возвращает ID вставленной характеристики
	*/
	static public function addChar($_itemID, $_nameID, $_value, $_ordering = 0, $_status = 1)
	{
		global $db;

		$_value = Chars::checkValue($_nameID, $_value); // проверка значений характеристики

		if(strlen($_value) == 0)
			return;

		$stmt_insert = $db->prepare('INSERT INTO com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering, status = :status ');
		$stmt_insert->execute(
			array(
				'item_id' => $_itemID,
				'name_id' => $_nameID,
				'value' => $_value,
				'ordering' => $_ordering,
				'status' => $_status
				)
			);

		return $db->lastInsertId();
	}


	/*
		Обновляем характеристику
	*/
	static public function updateChar($_id, $_itemID, $_nameID, $_value, $_ordering = 0, $_status = 1)
	{
		global $db;

		$_value = Chars::checkValue($_nameID, $_value); // проверка значений характеристики

		$stmt_update = $db->prepare('UPDATE com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering, status = :status WHERE id = :id');
		$stmt_update->execute(
			array(
				'id' => $_id,
				'item_id' => $_itemID,
				'name_id' => $_nameID,
				'value' => $_value,
				'ordering' => $_ordering,
				'status' => $_status
				)
			);
	}


	/*
		Получить характеристику
	*/
	static public function getChar($_id)
	{
		global $db;

		$_id = (int)$_id;

		$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_char WHERE id = :id');
		$SQL_PREPARE->execute(
			array(
				'id' => $_id
			)
		);

		return $SQL_PREPARE->fetchAll();
	}


	/*
		Существует ли характеристика. true or false.
	*/
	static public function isCharExist($_itemID, $_nameID, $_value)
	{
		global $db;

		$_itemID = (string)$_itemID;
		$_nameID = (string)$_nameID;
		$_value = (string)$_value;

		$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_char WHERE item_id = :item_id AND name_id = :name_id AND value = :value');
		$SQL_PREPARE->execute(
			array(
				'item_id' => $_itemID,
				'name_id' => $_nameID,
				'value' => $_value
			)
		);

		if(count($SQL_PREPARE->fetchAll()) > 0)
			return true;

		return false;
	}


	/*
		Удалить характеристику
	*/
	static public function deleteChar($_itemID, $_nameID)
	{
		global $db;

		$_itemID = (int)$_itemID;
		$_nameID = (int)$_nameID;

		$SQL_PREPARE = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id AND name_id = :name_id');
		$SQL_PREPARE->execute(
			array(
				'item_id' => $_itemID,
				'name_id' => $_nameID,
			)
		);
	}


	/*
		Возвращает шаблон вывода характеристик
	*/
	protected function template($_itemID)
	{
		$out = '';

		foreach($this->getArray($_itemID) as $row) // цикл по строкам
		{
			foreach($row as $char => $_value) // цикл по ячейкам
			{
				$out .= $char.' => '.$_value.'; ';
			}

			$out .= '<br>';
		}

		return $out;
	}


	/*
		возвращает тип характеристики
	*/
	static private function getCharType($_nameID)
	{
		global $db;

		$stmt_char_name = $db->prepare('SELECT type FROM com_shop_char_name WHERE id =:id LIMIT 1');
		$stmt_char_name->execute(array('id' => $_nameID));
		$row_char_name = $stmt_char_name->fetch();

		return $row_char_name['type'];
	}


	/*
		Помещает массив характеристик из БД в private $charsArray
	*/
	private function charsArrayDB($_itemID, $group_identifier = 0)
	{
		global $db;

		// Если нет группировки
		if($this->charGrouping != 1)
		{
			$stmt_chars = $db->prepare('
			SELECT c.id, c.item_id, c.name_id, c.value, c.ordering, c.status, n.name, n.unit, n.type
			FROM com_shop_char c
			JOIN com_shop_char_name n ON n.`id` = c.name_id
			WHERE c.item_id = :item_id ORDER BY c.ordering
			');

			$stmt_chars->execute(array('item_id' => $_itemID));

			while ($row = $stmt_chars->fetch())
			{
				$this->charsArray[] = $row;
			}
		}
		else // Есть группировка - выбираем товары с одинаковым 'group_identifier'
		{
			$stmt_chars = $db->prepare('
			SELECT c.id, c.item_id, c.name_id, c.value, c.ordering, c.status, n.name, n.unit, n.type
			FROM com_shop_char c
			JOIN com_shop_char_name n ON n.`id` = c.name_id
			WHERE c.item_id IN (SELECT id FROM com_shop_item WHERE group_identifier = :group_identifier) ORDER BY c.ordering
			');

			$stmt_chars->execute(array('group_identifier' => $group_identifier));

			while ($row = $stmt_chars->fetch())
			{
				$this->charsArray[] = $row;
			}
		}

	}


	/*
		Проверка значений характеристик
	*/
	static private function checkValue($_nameID, $_value)
	{
		$type =Chars::getCharType($_nameID); // тип характеристики
		if ($type == 'number'){$_value = str_replace(',', '.', $_value);}
		$s = array("'", '"');	// заменить кавычки
		$_value = str_replace($s, '`', $_value);
		$pattern = "/[^(a-z0-9а-яё\_\-\+\.\,\;\:\ \%\(\)\`\/\\)]/iu";
		$replacement = "";
		$_value= preg_replace($pattern, $replacement, $_value);
		$_value = trim($_value);

		return $_value;
	}

	private $charGrouping = 0;
	private $charsArray = array(); // Двухмерный массив характеристик строка по id / значение ячейки
}
