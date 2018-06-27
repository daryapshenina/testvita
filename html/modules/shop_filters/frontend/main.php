<?php
namespace Modules\ShopFilter;
defined('AUTH') or die('Restricted access');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if($d[0] !== 'shop' && $d[1] !== 'section')
	return;

$out = '';
$sectionID = intval($d[2]);
$action = explode('?', $_SERVER['REQUEST_URI'])[0];;

$SQL_PREPARE = $db->prepare('
	SELECT f.id, f.section_id, f.char_id, c.name, c.unit, c.type, f.value_1, f.value_2, f.ordering
	FROM com_shop_filter AS f
	JOIN com_shop_char_name AS c ON c.id = f.char_id
	WHERE f.section_id = :section_id
	ORDER BY f.ordering
');

$SQL_PREPARE->execute(
	array(
		'section_id' => $sectionID
	)
);

$filters = $SQL_PREPARE->fetchAll();

if(count($filters) === 0)
	return;

/**/

$shopFilter = [];

$priceFrom = '';
$priceTo = '';

$new = '';
$discount = '';

/**/

if(isset($_SESSION['shop_filter']) && isset($_SESSION['shop_filter'][$sectionID]))
	$shopFilter = $_SESSION['shop_filter'][$sectionID];

if(isset($shopFilter['ot']) && isset($shopFilter['ot']['price']))
	$priceFrom = (int)$shopFilter['ot']['price'];

if(isset($shopFilter['do']) && isset($shopFilter['do']['price']))
	$priceTo = (int)$shopFilter['do']['price'];

if($priceFrom == 0)
	$priceFrom = '';

if($priceTo == 0)
	$priceTo = '';

/**/

if(isset($shopFilter['new']))
	$new = ' checked';

if(isset($shopFilter['discount']))
	$discount = ' checked';

/**/

foreach($filters as $iter)
{
	$out .= '<div class="mod-shop-filters-section">
		<div class="mod-shop-filters-title">' . $iter['name'] . '</div>';

	if($iter['type'] === 'string')
	{
		$chars = explode(';', $iter['value_1']);
		$list = '';

		foreach($chars as $i => &$value)
		{
			$id = 'mod-shop-filters-checkbox-'.$iter['char_id'].'-'.$i;
			$value = trim($value);
			$checked = '';

			if(isset($shopFilter['char_s']) && isset($shopFilter['char_s'][$iter['char_id']]))
			{
				$filterValue = $shopFilter['char_s'][$iter['char_id']];

				if(!is_array($filterValue))
					$filterValue = array($filterValue);

				foreach($filterValue as &$char)
					if($char === $value)
						$checked = ' checked="checked"';
			}

			$list .= '
				<div>
					<table>
						<tr>
							<td>
								<input id="'.$id.'" class="input_1" name="char_s['.$iter['char_id'].'][]" value="'.$value.'" type="checkbox"'.$checked.'><label for="'.$id.'"></label>
							</td>
							<td>
								<label for="'.$id.'">'.$value.'</label>
							</td>
						</tr>
					</table>
				</div>
			';
		}

		$out .= '<div class="mod-shop-filters-scroll">'.$list.'</div>';
	}

	else if($iter['type'] === 'number')
	{
		$value_1 = $iter['value_1'];
		$value_2 = $iter['value_2'];

		if(isset($shopFilter['char_n1']) && isset($shopFilter['char_n1'][$iter['char_id']]))
			$value_1 = $shopFilter['char_n1'][$iter['char_id']];

		if(isset($shopFilter['char_n2']) && isset($shopFilter['char_n2'][$iter['char_id']]))
			$value_2 = $shopFilter['char_n2'][$iter['char_id']];

		if($value_1 == 0)
			$value_1 = '';

		if($value_2 == 0)
			$value_2 = '';

		$out .= '
			<div>
				<table>
					<tr>
						<td>
							от
						</td>
						<td>
							<input class="input_1" type="text" name="char_n1['.$iter['char_id'].']" value="' . $value_1 . '">
						</td>
						<td>
							до
						</td>
						<td>
							<input class="input_1" type="text" name="char_n2['.$iter['char_id'].']" value="' . $value_2 . '">
						</td>
						<td>
							' . $iter['unit'] . '
						</td>
					</tr>
				</table>
			</div>
		';
	}

	$out .= '</div>';
}

/**/

echo '
<div class="mod-main mod-shop-filters">
	<div class="mod-content">
		<form method="post" action="' . $action . '">
			' . $out . '
			<div class="mod-shop-filters-section">
				<div class="mod-shop-filters-title">Цена</div>
				<div>
					<table>
						<tr>
							<td>
								от
							</td>
							<td>
								<input class="input_1" type="text" name="filter_price_ot" value="' . $priceFrom . '">
							</td>
							<td>
								до
							</td>
							<td>
								<input class="input_1" type="text" name="filter_price_do" value="' . $priceTo . '">
							</td>
							<td>
								' . \ShopSettings::instance()->getValue('currency') . '
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="mod-shop-filters-section">
				<div class="mod-shop-filters-title">Доп. параметры</div>
				<div>
					<div style="padding-bottom:3px;">
						<table>
							<tr>
								<td>
									<input id="mod-shop-filters-checkbox-new" class="input_1" type="checkbox" name="new" value="1"' . $new . '><label for="mod-shop-filters-checkbox-new"></lable>
								</td>
								<td>
									<label for="mod-shop-filters-checkbox-new">новинки</lable>
								</td>
							</tr>
						</table>
					</div>
					<div>
						<table>
							<tr>
								<td>
									<input id="mod-shop-filters-checkbox-discount" class="input_1" type="checkbox" name="discount" value="1"' . $discount . '><label for="mod-shop-filters-checkbox-discount"></lable>
								</td>
								<td>
									<label for="mod-shop-filters-checkbox-discount">со скидкой</lable>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="mod-shop-filters-buttons">
				<input value="Искать" name="shop_filter_set" type="submit" class="mod-shop-filters-search">
				<input value="Сбросить" name="shop_filter_reset" type="submit" class="mod-shop-filters-reset">
			</div>
		</form>
	</div>
</div>
';
