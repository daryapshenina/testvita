<?php
namespace Shop\Sections;
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

/*
	Создает указанный путь.
	Принимает массив вида array('Корневой раздел', 'Вложенный раздел - 1', 'Вложенный раздел - 2', 'Вложенный раздел - 3')
*/

function createPath($_arrayPath)
{
	if(getType($_arrayPath) != 'array')
		return;

	$parentID = 0;

	while(count($_arrayPath) > 0)
	{
		$sectionExist = false;
		$sectionName = array_shift($_arrayPath);
		$sectionArray = getSectionByName($sectionName);

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
			$array = createSectionByMenuID($sectionName, $parentID);
			$parentID = $array['id'];
		}
	}

	return $parentID;
}

function createSectionByID($_title, $_parentSectionID = 0, $_identifier = '', $_pub = 1, $_ordering = 0, $_description = '', $_tag_title = '', $_tag_description = '')
{

}

/*
	Создает раздел.
	В качестве категории принимает ID меню.

	Время создание добавить.
*/

function createSectionByMenuID($_title, $_parentMenuID = 0, $_identifier = '', $_pub = 1, $_ordering = 0, $_description = '', $_tag_title = '', $_tag_description = '')
{
	global $db;

	$_title = (string)$_title;
	$_parentMenuID = (int)$_parentMenuID;
	$_identifier = (string)$_identifier;
	$_pub = (int)$_pub;
	$_ordering = (int)$_ordering;
	$_description = (string)$_description;
	$_tag_title = (string)$_tag_title;
	$_tag_description = (string)$_tag_description;

	$menuType = 'left';

	if(strlen($_title) == 0)
		return NULL;

	$arrayOut = array('id' => 0, 'parent' => 0, 'name' => '', 'id_com' => 0);

	if($_parentMenuID != 0)
	{
		$arrayParent = getSectionByMenuID($_parentMenuID);

		if(count($arrayParent) == 0)
			$_parentMenuID = 0;
		else
			$menuType = $arrayParent[0]['menu_type'];
	}

	$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_section (identifier, pub, parent, ordering, title, description, tag_title, tag_description)
									VALUES (:identifier, :pub, :parent, :ordering, :title, :description, :tag_title, :tag_description)');
	$SQL_PREPARE->execute(
		array(
			'identifier' => $_identifier,
			'pub' => $_pub,
			'parent' => $_parentMenuID,
			'ordering' => $_ordering,
			'title' => $_title,
			'description' => $_description,
			'tag_title' => $_tag_title,
			'tag_description' => $_tag_description
		)
	);

	$arrayOut['parent'] = $_parentMenuID;
	$arrayOut['name'] = $_title;
	$arrayOut['id_com'] = $db->lastInsertId();

	if($_ordering == 0)
		$_ordering = $arrayOut['id_com'];

	$SQL_PREPARE = $db->prepare('INSERT INTO menu (menu_type, name, description, pub, parent, ordering, component, main, p1, id_com)
									VALUES (:menu_type, :name, :description, :pub, :parent, :ordering, :component, :main, :p1, :id_com)');
	$SQL_PREPARE->execute(
		array(
			'menu_type' => $menuType,
			'name' => $_title,
			'description' => $_description,
			'pub' => $_pub,
			'parent' => $_parentMenuID,
			'ordering' => $_ordering,
			'component' => 'shop',
			'main' => 0,
			'p1' => 'section',
			'id_com' => $arrayOut['id_com']
		)
	);

	$arrayOut['id'] = $db->lastInsertId();

	return $arrayOut;
}


function updateSectionByID()
{

}

function updateSectionByMenuID()
{

}


function deleteSectionByID($_sectionID)
{

}

function deleteSectionByMenuID($_menuID)
{

}


function getSectionByID($_sectionID)
{
	global $db;

	$_sectionID = (int)$_sectionID;

	$SQL_PREPARE = $db->prepare('SELECT id, menu_type, parent, name, id_com FROM menu WHERE component = "shop" AND p1 = "section" AND id_com = :id_com');
	$SQL_PREPARE->execute(
		array(
			'id_com' => $_sectionID
		)
	);

	return $SQL_PREPARE->fetchAll();
}

function getSectionByMenuID($_menuID)
{
	global $db;

	$_menuID = (int)$_menuID;

	$SQL_PREPARE = $db->prepare('SELECT id, menu_type, parent, name, id_com FROM menu WHERE component = "shop" AND p1 = "section" AND id = :id');
	$SQL_PREPARE->execute(
		array(
			'id' => $_menuID
		)
	);

	return $SQL_PREPARE->fetchAll();
}

function getSectionByName($_name)
{
	global $db;

	$_name = (string)$_name;

	$SQL_PREPARE = $db->prepare('SELECT id, menu_type, parent, name, id_com FROM menu WHERE component = "shop" AND p1 = "section" AND name = :name');
	$SQL_PREPARE->execute(
		array(
			'name' => $_name
		)
	);

	return $SQL_PREPARE->fetchAll();
}

/*
	Возвращаем массив указанного и родительских пунктов меню.
*/

function getSectionParentsByMenuID($_menuID, &$_array = array())
{
	global $db;

	$_menuID = (int)$_menuID;

	$SQL_PREPARE = $db->prepare('SELECT id, id_com, name, parent FROM menu WHERE component = "shop" AND p1 = "section" AND id = :id');
	$SQL_PREPARE->execute(
		array(
			'id' => $_menuID
		)
	);

	$menu = $SQL_PREPARE->fetchAll();

	if(count($menu) > 0)
	{
		array_unshift(
				$_array,
				array(
					'id' => $menu[0]['id'],
					'id_com' => $menu[0]['id_com'],
					'name' => $menu[0]['name']
				)
			);

		if($menu[0]['parent'] > 0)
			getSectionParentsByMenuID($menu[0]['parent'], $_array);
	}

	return $_array;
}

/*
	Возвращаем массив пунктов меню
*/

function getSections()
{
	return getSubSections('all');
}

/*
	Возвращаем массив подпунктов меню
*/

function getSubSections($_parent, &$_array = array())
{
	global $db;

	if($_parent === 'all')
		$_parent = 0;

	$SQL_PREPARE = $db->prepare('SELECT id, id_com, name FROM menu WHERE component = "shop" AND p1 = "section" AND parent = :parent ORDER BY ordering DESC');
	$SQL_PREPARE->execute(
		array(
			'parent' => $_parent
		)
	);

	$sections = $SQL_PREPARE->fetchAll();

	foreach($sections as $value)
	{
		array_unshift($_array, array(
					'id' => $value['id'],
					'id_com' => $value['id_com'],
					'name' => $value['name'],
					'children' => array()
				));

		$firstElement = &$_array[0];
		getSubSections($value['id'], $firstElement['children']);
	}

	return $_array;
}
