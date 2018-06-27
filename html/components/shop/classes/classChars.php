<?php
// DAN 2015
// getCharsArray($item_id) - получить массив хaрaктеристик
// getCharsTemplate($item_id) - подключить шаблон вывода
// insertChars($item_id, $name_id, $value, $ordering = 0, $status = 1) - вставить характеристики
// updateChars($id, $item_id, $name_id, $value, $ordering = 0, $status = 1) - обновить характеристики
defined('AUTH') or die('Restricted access');

class classChars
{
   function __construct($item_id = 0, $group_identifier = 0)
   {
		global $shopSettings;

		if($group_identifier == '0'){$this->charGrouping = 0;} // Если пустой индентификатор группы - значит нет группировки, даже если в настройках стоит группировка
		else{$this->charGrouping = $shopSettings->getValue('grouping');}

		$this->charsArrayDB($item_id, $group_identifier);
   }


	// Возвращает массив характеристик
	public function getCharsArray()
	{
		return $this->charsArray;
	}


	// Возвращает шаблон характеристик
	public function getCharsTemplate($item_id)
	{
		return $this->CharsTemplate($item_id);
	}


	// Вставляем характеристики
	public function insertChars($item_id, $name_id, $value, $ordering = 0, $status = 1)
	{
		global $db;

		$value = $this->checkValue($name_id, $value); // проверка значений характеристики

		if(strlen($value) == 0)
			return;

		$stmt_insert = $db->prepare('INSERT INTO com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering, status = :status ');
		$stmt_insert->execute(array('item_id' => $item_id, 'name_id' => $name_id, 'value' => $value, 'ordering' => $ordering, 'status' => $status));
	}


	// Обновляем характеристики
	public function updateChars($id, $item_id, $name_id, $value, $ordering = 0, $status = 1)
	{
		global $db;

		$value = $this->checkValue($name_id, $value); // проверка значений характеристики

		$stmt_update = $db->prepare('UPDATE com_shop_char SET item_id = :item_id, name_id = :name_id, value = :value, ordering = :ordering, status = :status WHERE id = :id');
		$stmt_update->execute(array('id' => $id, 'item_id' => $item_id, 'name_id' => $name_id, 'value' => $value, 'ordering' => $ordering, 'status' => $status));
	}


	// Возвращает шаблон вывода характеристик
	protected function CharsTemplate($item_id)
	{
		$out = '';

		foreach($this->getCharsArray($item_id) as $row) // цикл по строкам
		{
			foreach($row as $char => $value) // цикл по ячейкам
			{
				$out .= $char.' => '.$value.'; ';
			}

			$out .= '<br>';
		}

		return $out;
	}


	// возвращает тип характеристики
	private function getCharType($name_id)
	{
		global $db;

		$stmt_char_name = $db->prepare('SELECT type FROM com_shop_char_name WHERE id =:id LIMIT 1');
		$stmt_char_name->execute(array('id' => $name_id));
		$row_char_name = $stmt_char_name->fetch();

		return $row_char_name['type'];
	}


	// Помещает массив характеристик из БД в private $charsArray
	private function charsArrayDB($item_id, $group_identifier = 0)
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

			$stmt_chars->execute(array('item_id' => $item_id));

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


	// Проверка значений характеристик
	private function checkValue($name_id, $value)
	{
		$type = $this->getCharType($name_id); // тип характеристики
		if ($type == 'number'){$value = str_replace(',', '.', $value);}
		$s = array("'", '"');	// заменить кавычки
		$value = str_replace($s, '`', $value);
		$pattern = "/[^(a-z0-9а-я\_\-\.\;\ \(\)\`\/\\)]/iu";
		$replacement = "";
		$value= preg_replace($pattern, $replacement, $value);
		$value = trim($value);

		return $value;
	}

	// Двухмерный массив характеристик строка по id / значение ячейки
	private $charGrouping = 0;
	private $charsArray = array();
}


?>