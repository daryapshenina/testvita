<?php
defined('AUTH') or die('Restricted access');
include $_SERVER['DOCUMENT_ROOT']."/db.php";

const PATH_TO_PHOTO = '/components/shop/photo/';

const RESIZE_TYPE_SMART = 1;
const RESIZE_TYPE_CUTTING = 2;
const RESIZE_TYPE_COMPRESSION = 3;

class CSV
{
	/*
		null - если без ошибок
	*/
	static public function updateSection($_identifier, $_identifierParent, $_title)
	{
		global $db;

		if(strlen($_identifier) == 0)
			return 'Категория '.$_title.' не была добавлена из-за не указанного идентификатора';

		if(strlen($_title) == 0)
			return 'Категория '.$_title.' не была добавлена из-за пустого заголовка';

		$arraySection = CSV::getSectionByIdentifier($_identifier);
		$parentID = 0;

		// get parent ID if exist
		if(strlen($_identifierParent) > 0)
		{
			$arrayParent = CSV::getSectionByIdentifier($_identifierParent);

			if(count($arrayParent) > 0)
				$parentID = $arrayParent[0]['menuID'];
			else
				return 'Категория '.$_title.' не была добавлена так как родительская категория не существует';
		}

		// if section exist then update
		if(count($arraySection) > 0)
		{
			$SQL_PREPARE = $db->prepare('UPDATE com_shop_section SET title = :title
										WHERE identifier = :identifier');

			$SQL_PREPARE->execute(
				array(
					'title' => $_title,
					'identifier' => $_identifier
				)
			);

			$SQL_PREPARE = $db->prepare('UPDATE menu SET name = :name, parent = :parent
										WHERE id_com = :id_com');

			$SQL_PREPARE->execute(
				array(
					'name' => $_title,
					'parent' => $parentID,
					'id_com' => $arraySection[0]["sectionID"]
				)
			);

			return null;
		}

		// If not exist
		$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_section (identifier, pub, parent, title) VALUES (:identifier, 1, 0, :title)');
		$SQL_PREPARE->execute(array(
			'identifier' => $_identifier,
			'title' => $_title
		));

		$sectionID = $db->lastInsertId();

		$SQL_PREPARE = $db->prepare('INSERT INTO menu (menu_type, name, description, pub, parent, ordering, component, main, p1, id_com)
										VALUES (:menu_type, :name, "раздел интернет-магазина", 1, :parent, :ordering, "shop", 0, "section", :id_com)');
		$SQL_PREPARE->execute(array(
								'menu_type' => "left",
								'name' => $_title,
								'parent' => $parentID,
								'ordering' => $sectionID,
								'id_com' => $sectionID
							));

		return null;
	}

	static public function getSectionByIdentifier($_identifier)
	{
		global $db;

		$SQL_PREPARE = $db->prepare('SELECT
										s.id AS sectionID, m.id AS menuID, m.parent, s.identifier, s.title
									FROM com_shop_section AS s
									JOIN menu AS m
									WHERE s.id = m.id_com AND s.identifier = :identifier');
		$SQL_PREPARE->execute(
			array(
				'identifier' => $_identifier
			)
		);

		return $SQL_PREPARE->fetchAll();
	}

	/*
		null - если без ошибок
	*/
	static public function updateItem($_identifier, $_identifierSection, $_name, $_price, $_photo, $_quantity)
	{
		global $db;

		$item = CSV::getItemByIdentifier($_identifier);
		$arraySection = CSV::getSectionByIdentifier($_identifierSection);

		if(count($arraySection) == 0)
			return 'Товар '.$_name.' ('.$_identifier.') не был добавлен так как нужной категории не существует';

		if(strlen($_name) == 0)
			return 'Товар '.$_identifier.' не был добавлен так как имя не задано';

		if(strlen($_identifier) == 0)
			return 'Товар '.$_name.' не был добавлен так как идентификатор не задан';

		// if item exist
		if(count($item) > 0)
		{
			if($_photo === 'null')
			{
				$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET section = :section, title = :title, price = :price, quantity = :quantity WHERE identifier = :identifier');

				$SQL_PREPARE->execute(
					array(
						'section' => $arraySection[0]['sectionID'],
						'title' => $_name,
						'price' => $_price,
						'quantity' => $_quantity,
						'identifier' => $_identifier
					)
				);
			}
			else
			{
				$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET section = :section, title = :title, price = :price, quantity = :quantity, photo = :photo WHERE identifier = :identifier');

				$SQL_PREPARE->execute(
					array(
						'section' => $arraySection[0]['sectionID'],
						'title' => $_name,
						'price' => $_price,
						'quantity' => $_quantity,
						'photo' => $_photo,
						'identifier' => $_identifier
					)
				);

				$arrayPhoto = array();
				$arrayPhoto[] = $item[0]['photo'];
				$arrayPhoto[] = $item[0]['photo_big'];

				foreach($arrayPhoto as $iter)
				{
					if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter))
						unlink($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter);
				}
			}

			return null;
		}
		else
		{
			if($_photo === 'null')
				$_photo = '';

			$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_item
											(identifier, section, pub, title, price, quantity, photo, cdate) VALUES
											(:identifier, :section, 1, :title, :price, :quantity, :photo, NOW())'
										);

			$SQL_PREPARE->execute(
				array(
					'identifier' => $_identifier,
					'section' => $arraySection[0]['sectionID'],
					'title' => $_name,
					'price' => $_price,
					'quantity' => $_quantity,
					'photo' => $_photo
				)
			);

			return null;
		}
	}

	static public function getItemByIdentifier($_identifier)
	{
		global $db;

		$SQL_PREPARE = $db->prepare('SELECT id, identifier, section, title, price, photo, photo_big, photo_more FROM com_shop_item WHERE identifier = :identifier');
		$SQL_PREPARE->execute(
			array(
				'identifier' => $_identifier
			)
		);

		return $SQL_PREPARE->fetchAll();
	}

	static public function getCharacteristicName($_name)
	{
		global $db;

		$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_char_name WHERE name = :name');
		$SQL_PREPARE->execute(
			array(
				'name' => $_name
			)
		);

		return $SQL_PREPARE->fetchAll();
	}

	/*
		Возвращает массив с нужной характеристикой
	*/
	static public function addCharacteristicName($_name)
	{
		global $db;

		if(strlen($_name) == 0)
			return array();

		$chars = CSV::getCharacteristicName($_name);

		if(count($chars) > 0)
			return $chars;

		$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_char_name (name, unit, type) VALUES (:name, :unit, :type)');
		$SQL_PREPARE->execute(
			array(
				'name' => $_name,
				'unit' => '',
				'type' => 'string'
			)
		);

		return CSV::getCharacteristicName($_name);
	}

	static public function getItemCharacteristic($_itemID, $_charNameID, $_value)
	{
		global $db;

		$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_char WHERE item_id = :item_id AND name_id = :name_id AND value = :value');
		$SQL_PREPARE->execute(
			array(
				'item_id' => $_itemID,
				'name_id' => $_charNameID,
				'value' => $_value
			)
		);

		return $SQL_PREPARE->fetchAll();
	}

	/*
		null - если без ошибок
	*/
	static public function addItemCharacteristic($_itemIdentifier, $_charName, $_value)
	{
		global $db;

		$item = CSV::getItemByIdentifier($_itemIdentifier);
		$char = CSV::getCharacteristicName($_charName);
		$_value = (string)$_value;

		if(count($item) == 0 || count($char) == 0)
			return null;

		if(strlen($_value) == 0)
			return 'Характеристика '.$_charName.' не была добавлена для товара '.$item[0]['title'].' потому что значение характеристики '.$_value.' отсутствует';

		$charItem = CSV::getItemCharacteristic($$item[0]['id'], $char[0]['id'], $_value);

		if(count($charItem) == 0)
		{
			$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_char (item_id, name_id, value, status) VALUES (:item_id, :name_id, :value, 1)');
			$SQL_PREPARE->execute(
				array(
					'item_id' => $item[0]['id'],
					'name_id' => $char[0]['id'],
					'value' => $_value
				)
			);
		}

		return null;
	}

	/*
		null - если без ошибок
	*/
	static public function updatePhoto($_file, $_methodResize, $_sizeBig, $_sizeSmall)
	{
		global $db;

		if(!is_array($_file))
			return null;

		$SQL_PREPARE = $db->prepare('SELECT id FROM com_shop_item WHERE photo = :photo');
		$SQL_PREPARE->execute(
			array(
				'photo' => $_file['name']
			)
		);

		$item = $SQL_PREPARE->fetchAll();

		if(count($item) == 0)
			return 'Изображение '.$_file['name'].' не загруженно так как нету товара с такой фотографией';

		$name = md5(((string)time()).((string)(rand(0, 1000))));
		$namePhotoBig = $name.'_.jpg';
		$namePhotoSmall = $name.'.jpg';

		CSV::processingImage($_file, $namePhotoBig, $_methodResize, $_sizeBig);
		CSV::processingImage($_file, $namePhotoSmall, $_methodResize, $_sizeSmall);

		$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET photo = :photo, photo_big = :photo_big WHERE id = :id');

		$SQL_PREPARE->execute(
			array(
				'id' => $item[0]['id'],
				'photo' => $namePhotoSmall,
				'photo_big' => $namePhotoBig,
			)
		);

		return null;
	}

	static private function processingImage($_file, $_newName, $_methodResize, $_newSize)
	{
		if(!is_array($_file))
			return;

		$name = $_file['name'];
		$file = $_file['tmp_name'];

		switch($_methodResize)
		{
			case RESIZE_TYPE_CUTTING:
			{
				include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResizeCutting.php");
				$img = new ImageResizeCutting($file, $_SERVER['DOCUMENT_ROOT'].PATH_TO_PHOTO.$_newName, $_newSize['x'], $_newSize['y']);
				$img->run();
			} break;

			case RESIZE_TYPE_COMPRESSION:
			{
				include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResizeCompression.php");
				$img = new ImageResizeCompression($file, $_SERVER['DOCUMENT_ROOT'].PATH_TO_PHOTO.$_newName, $_newSize['x'], $_newSize['y']);
				$img->run();
			} break;

			default:
			{
				include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResizeSmart.php");
				$img = new ImageResizeSmart($file, $_SERVER['DOCUMENT_ROOT'].PATH_TO_PHOTO.$_newName, $_newSize['x'], $_newSize['y']);
				$img->run();
			}
		}
	}
}