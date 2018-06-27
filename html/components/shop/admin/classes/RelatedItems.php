<?php
namespace Shop\RelatedItems;
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";
include_once __DIR__.'/Items.php';

function getRelatedItemsById($_id)
{
	global $db;

	$_id = (int)$_id;
	$result = Array();

	$SQL_PREPARE = $db->prepare('SELECT related_id
		FROM com_shop_related_item
		WHERE item_id = :id
		ORDER BY ordering');

	$SQL_PREPARE->execute(
		array(
			'id' => $_id
		)
	);

	foreach($SQL_PREPARE->fetchAll() as $iter)
	{
		$id = $iter['related_id'];
		$item = \Shop\Items\getItemById($id);

		if(count($item) > 0)
		{
			$identifier = $item[0]['identifier'];

			array_push($result, [
					'id' => $id,
					'identifier' => $identifier
				]);
		}
	}

	return $result;
}

function getRelatedItemsByIdentifier($_identifier)
{
	global $db;

	$_identifier = (string)$_identifier;
	$item = \Shop\Items\getItemByIdentifier($_identifier);

	if(count($item) === 0)
		return Array();

	$id = $item[0]['id'];
	return getRelatedItemsById($id);
}

function addRelatedItemById($_id, $_relatedId, $_ordering)
{
	global $db;

	$_id = (int)$_id;
	$_relatedId = (int)$_relatedId;
	$_ordering = (int)$_ordering;

	$item = \Shop\Items\getItemById($_id);
	$relatedItem = \Shop\Items\getItemById($_relatedId);

	if(count($item) === 0 || count($relatedItem) === 0)
		return;

	$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_related_item (item_id, related_id, ordering)
									VALUES (:item_id, :related_id, :ordering)');

	$SQL_PREPARE->execute(
		array(
			'item_id' => $_id,
			'related_id' => $_relatedId,
			'ordering' => $_ordering,
		)
	);
}

function addRelatedItemByIdentifier($_identifier, $_relatedIdentifier, $_ordering)
{
	$_identifier = (string)$_identifier;
	$_relatedIdentifier = (string)$_relatedIdentifier;
	$_ordering = (int)$_ordering;

	if(strlen($_identifier) === 0 || strlen($_relatedIdentifier) === 0)
		return;

	$item = \Shop\Items\getItemByIdentifier($_identifier);
	$relatedItem = \Shop\Items\getItemByIdentifier($_relatedIdentifier);

	if(count($item) === 0 || count($relatedItem) === 0)
		return;

	addRelatedItemById($item[0]['id'], $relatedItem[0]['id'], $_ordering);
}

function deleteAllRelatedItemsById($_id)
{
	global $db;

	$_id = (int)$_id;

	$SQL_PREPARE = $db->prepare('DELETE FROM com_shop_related_item WHERE item_id = :item_id');

	$SQL_PREPARE->execute(
		array(
			'item_id' => $_id
		)
	);
}