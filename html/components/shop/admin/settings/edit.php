<?php
defined('AUTH') or die('Restricted access');

include_once $root.'/components/shop/classes/classShopSettings.php';

$head->addFile('/components/shop/admin/settings/settings.css');

$item_id = intval($admin_d4);

function a_com()
{
	global $root, $db, $domain, $item_id, $item_section_id, $shopSettings;

	/* --- ОБЩИЕ НАСТРОЙКИ --- */

	// Размеры большого и малого изображения
	$x_small = '<input type="number" min="50" max="500" name="x_small" size="3" value="'.$shopSettings->x_small.'" required >';
	$y_small = '<input type="number" min="50" max="500" name="y_small" size="3" value="'.$shopSettings->y_small.'" required >';

	$x_big = '<input type="number" min="400" max="2000" name="x_big" size="3" value="'.$shopSettings->x_big.'" required >';
	$y_big = '<input type="number" min="400" max="2000" name="y_big" size="3" value="'.$shopSettings->y_big.'" required >';

	// Метод ресайза
	$resize_method = array_fill(0, 3, '');
	switch($shopSettings->small_resize_method)
	{
		case 1:
			$resize_method[0] = "checked";
			break;

		case 2:
			$resize_method[1] = "checked";
			break;

		case 3:
			$resize_method[2] = "checked";
			break;
	}



	/* --- РАЗДЕЛ --- */

	// Количество товаров на странице
	$quantity = '<input type="number" min="10" max="1000"  name="quantity" size="3" value="'.$shopSettings->quantity.'" required >';

	// Шаблон вывода раздела
	$mapping_arr = array_fill(0, 13, '');
	$mapping_arr['999'] = '';
	$mapping_arr[$shopSettings->mapping] = "selected";
	$mapping_999 = '';

	if(file_exists($root."/tmp/shop/section/tmp.php")) $mapping_999 = '<option value="999" '.$mapping_arr['999'].'>Индивидуальный</option>';


	// Сортировка товара на странице
	$sorting_items = array_fill(0, 6, '');
	$sorting_items[$shopSettings->sorting_items] = "selected";

	// Выводить фильтры вверху раздела
	if($shopSettings->sub_sections == 1) $sub_sections = 'checked="checked"'; else $sub_sections = '';

	// Выводить фильтры вверху раздела
	if($shopSettings->section_filters == 1) $section_filters = 'checked="checked"'; else $section_filters = '';

	// Вывод товаров из подразделов
	if($shopSettings->output_un_section == 1) $output_un_section_cheked = 'checked="checked"'; else $output_un_section_cheked = '';

	// Вывод описания раздела
	$section_description = array_fill(0, 2, '');
	$section_description[$shopSettings->section_description] = 'checked="checked"';

	// Группировать товары
	if($shopSettings->grouping == 1) $grouping_cheked = 'checked="checked"'; else $grouping_cheked = '';



	/* --- ТОВАР --- */

	// Валюта
	$currency = $shopSettings->currency;
	if($currency == '') $currency = 'руб.';

	// Карточка товаров
	$view_item_card = array_fill(0, 9, '');
	$view_item_card[999] = '';
	$view_item_card[$shopSettings->view_item_card] = 'selected="selected"';

	// Задать вопрос по товару
	if($shopSettings->question == 1) $question_select = 'checked="checked"'; else $question_select = '';

	// Учитывать количество товаров
	$item_quantity_checked = array_fill(0, 3, '');
	$item_quantity_checked[$shopSettings->item_quantity] = 'checked="checked"';



	/* ---КОРЗИНА --- */

	// Оформление покупки
	$basket_type = array_fill(0, 2, '');
	if($shopSettings->basket_type == 0) $basket_type[0] = 'checked="checked"'; else $basket_type[1] = 'checked="checked"';


	echo '
		<h1>Настройки интернет-магазина:</h1>
		<form method="POST" action="/admin/com/shop/settings/update">
		<table class="admin_table">
			<tr>
				<th style="width:50px">&nbsp;</th>
				<th style="width:250px">Параметр</th>
				<th>Значение</th>
			</tr>
			<tr>
				<td class="td_sep" style="width:50px">&nbsp;</td>
				<td class="td_sep" style="width:200px"><b>ОБЩИЕ НАСТРОЙКИ:</b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>1</td>
				<td>Название магазина</td>
				<td><input class="input_1" type="text" name="shop_name" size="40" placeholder="Продуктовый" pattern="[a-zA-Zа-яА-Я1-90\s\.]{1,80}" title="Только буквы" value="'.$shopSettings->shop_name.'"></td>
			</tr>
			<tr>
				<td>2</td>
				<td>Название компании</td>
				<td><input class="input_1" type="text" name="company_name" size="40" placeholder="ООО Продуктовый" pattern="[a-zA-Zа-яА-Я1-90\s\.]{1,80}" title="Только буквы" value="'.$shopSettings->company_name.'"></td>
			</tr>
			<tr>
				<td>3</td>
				<td>Условия доставки</td>
				<td><input class="input_1" type="text" name="delivery" size="40" placeholder="Доставка при заказе от 1000 рублей" pattern="[a-zA-Zа-яА-Я1-90\s\.]{1,150}" title="Только буквы" value="'.$shopSettings->delivery.'"></td>
			</tr>
			<tr>
				<td>3</td>
				<td style="padding:10px;">Ключ для доступа к выгрузке в YML. Оставьте пустым, что бы система сгенерировала его автоматически.</td>
				<td style="padding:10px;">
					<input class="input_1" type="text" name="yml_key" size="40" placeholder="Только латинские буквы" title="Только буквы" value="'.$shopSettings->yml_key.'">
					Ссылка: <a href="/shop/yml/'.$shopSettings->yml_key.'" target="_blank">/shop/yml/'.$shopSettings->yml_key.'</a>
				</td>
			</tr>
			<tr>
				<td class="td_sep" style="width:50px">&nbsp;</td>
				<td class="td_sep" style="width:200px"><b>ИЗОБРАЖЕНИЯ:</b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>4</td>
				<td>Размер малого изображения</td>
				<td>по ширине: '.$x_small.' px. &nbsp;&nbsp; по высоте: '.$y_small.' px.</td>
			</tr>
			<tr>
				<td>5</td>
				<td>Размер большого изображения</td>
				<td>по ширине: '.$x_big.' px. &nbsp;&nbsp; по высоте: '.$y_big.' px.</td>
			</tr>
			<tr>
				<td>6</td>
				<td>Метод создания <br/>малого изображения:</td>
				<td>
					<span class="lineheight20">
						<input type="radio" value="1" '.$resize_method[0].' name="small_resize_method">умный ресайз <i>(вставка по большей стороне)</i> <br/>
						<input type="radio" value="2" '.$resize_method[1].' name="small_resize_method">подрезка <i>(подрезка большей стороны)</i><br/>
						<input type="radio" value="3" '.$resize_method[2].' name="small_resize_method">скукожить <i>(смять, пропорции игнорируются)</i><br/>
					</span>
				</td>
			</tr>
			<tr>
				<td class="td_sep">&nbsp;</td>
				<td class="td_sep"><b>РАЗДЕЛ:<b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>7</td>
				<td>Количество товаров на странице</td>
				<td>'.$quantity.'</td>
			</tr>
			<tr>
				<td>8</td>
				<td>Вид отображения <br/>товаров в категории:</td>
				<td>
					<select name="mapping">
						<option value="1" '.$mapping_arr['1'].'>Блоком в одну строку</option>
						<option value="2" '.$mapping_arr['2'].'>Плиткой</option>
						<option value="5" '.$mapping_arr['5'].'>Плиткой + всплывающие картинки</option>
						<option value="4" '.$mapping_arr['4'].'>Плиткой (старый стиль)</option>
						<option value="3" '.$mapping_arr['3'].'>Карточками</option>
						<option value="6" '.$mapping_arr['6'].'>Плоской плиткой</option>
						<option value="11" '.$mapping_arr['11'].'>Плоской плиткой - 2</option>
						<option value="7" '.$mapping_arr['7'].'>Одежда, обувь</option>
						<option value="8" '.$mapping_arr['8'].'>Каталог. Блоки</option>
						<option value="9" '.$mapping_arr['9'].'>Каталог. Плоской плиткой</option>
						<option value="10" '.$mapping_arr['10'].'>Landing page</option>
						<option value="12" '.$mapping_arr['12'].'>Разворачивающиеся карточки</option>
						'.$mapping_999.'
					</select>
				</td>
			</tr>
			<tr>
				<td>9</td>
				<td>Сортировка товара:</td>
				<td>
					<select name="sorting_items">
						<option value="0" '.$sorting_items[0].'>Ручная (Настраивается при добавлении или редактировании товара)</option>
						<option value="1" '.$sorting_items[1].'>По цене (по возрастанию)</option>
						<option value="2" '.$sorting_items[2].'>По цене (по убыванию)</option>
						<option value="3" '.$sorting_items[3].'>По алфавиту (по возрастанию)</option>
						<option value="4" '.$sorting_items[4].'>По алфавиту (по убыванию)</option>
						<option value="5" '.$sorting_items[5].'>По дате (Новые сверху включая недавно редактируемые)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>10</td>
				<td>Показать подразделы</td>
				<td>
					<input type="checkbox" name="sub_sections" value="1" '.$sub_sections.'>
				</td>
			</tr>	
			<tr>
				<td>11</td>
				<td>Вывод товаров из подразделов</td>
				<td>
					<input type="checkbox" name="output_un_section" value="1" '.$output_un_section_cheked.'>
				</td>
			</tr>			
			<tr>
				<td>12</td>
				<td>Выводить фильтры поиска вверху раздела</td>
				<td>
					<input type="checkbox" name="section_filters" value="1" '.$section_filters.'>
				</td>
			</tr>	
			<tr>
				<td>13</td>
				<td>Вывод описания раздела</td>
				<td>
					<span class="lineheight20">
						<input type="radio" value="0" '.$section_description[0].' name="section_description">Сверху<br/>
						<input type="radio" value="1" '.$section_description[1].' name="section_description">Снизу
					</span>
				</td>
			</tr>
			<tr>
				<td>14</td>
				<td>Группировать товары</td>
				<td>
					<input type="checkbox" name="grouping" value="1" '.$grouping_cheked.'> Товары группируются по одинаковому идентификатору группы. Не ставьте галочку если не понимаете, зачем это.
				</td>
			</tr>
			<tr>
				<td class="td_sep">&nbsp;</td>
				<td class="td_sep"><b>ТОВАР:</b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>15</td>
				<td>Валюта</td>
				<td><input type="text" name="currency" placeholder="руб." required pattern="[a-zA-Zа-яА-Я\.]{1,6}" title="Только буквы без пробелов" size="7" value="'.$currency.'"></td>
			</tr>
			<tr>
				<td>16</td>
				<td>Курс у.е.</td>
				<td><input type="text" name="ue" placeholder="у.е./руб." required="" pattern="[0-9\,\.]{1,10}" title="Только цифры без пробелов" size="7" value="'.$shopSettings->ue.'"> Стоимость в рублях пересчитывается из внутреннего курса в у.е., указанному в данном поле. В отличии от $USD и EURO - где курс берётся с сайта Центрального Банка России</td>
			</tr>			
			<tr>
				<td>17</td>
				<td>Карточка товаров</td>
				<td>
					<select name="view_item_card">
						<option value="0" '.$view_item_card[0].'>Обычный</option>
						<option value="1" '.$view_item_card[1].'>Расширенный 1</option>
						<option value="2" '.$view_item_card[2].'>Расширенный 2</option>
						<option value="8" '.$view_item_card[8].'>Расширенный 3</option>
						<option value="3" '.$view_item_card[3].'>Каталог</option>
						<option value="7" '.$view_item_card[7].'>Каталог - 2</option>
						<option value="4" '.$view_item_card[4].'>Группировка с переключением по цвету</option>
						<option value="5" '.$view_item_card[5].'>Группировка с переключением по изображению</option>
						<option value="6" '.$view_item_card[6].'>Обои, плитка (с товарами - компаньонами)</option>
						<option value="999" '.$view_item_card[999].'>Индивидуальная разработка</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>18</td>
				<td>Задать вопрос по товару</td>
				<td>
					<input type="checkbox" name="question" value="1" '.$question_select.'> Отображает ссылку на форму в которой посетитель может задать вопрос
				</td>
			</tr>
			<tr>
				<td>19</td>
				<td>Учитывать количество товаров</td>
				<td>
					<span class="lineheight20">
						<input type="radio" value="0" '.$item_quantity_checked[0].' name="item_quantity">Не учитывать количество.<br/>
						<input type="radio" value="1" '.$item_quantity_checked[1].' name="item_quantity">Учитывать. В административной части в карточке товара появляется поле <b>&quot;Количество&quot;</b>. Товар с нулевым количеством получает статус <b>&quot;Под заказ&quot;</b>.<br/>
						<input type="radio" value="2" '.$item_quantity_checked[2].' name="item_quantity">Учитывать. В административной части в карточке товара появляется поле <b>&quot;Количество&quot;</b>. При загрузке товаров из 1С - скрыть товар с нулевым количеством.<br/>
					</span>
				</td>
			</tr>
			<tr>
				<td class="td_sep">&nbsp;</td>
				<td class="td_sep"><b>КОРЗИНА:<b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>
			<tr>
				<td>20</td>
				<td>Оформление покупки</td>
				<td>
					<span class="lineheight20">
						<input type="radio" value="0" '.$basket_type[0].' name="basket_type">Обычное (перейти в корзину / продолжить покупки)<br/>
						<input type="radio" value="1" '.$basket_type[1].' name="basket_type">Летающее (товар "улетает" в корзину)
					</span>
				</td>
			</tr>
			<tr>
				<td>21</td>
				<td>Надпись на кнопке "В корзину"</td>
				<td><input type="text" name="sticker_add_to_cart" value="'.$shopSettings->sticker_add_to_cart.'"></td>
			</tr>
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;
		</form>
	';

}
?>
