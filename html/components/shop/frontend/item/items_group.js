// Файл подключается только при включённой группировке товаров
// Функция переключает товары группы по первому селектору характеристик
function items_group(item_id, char_name, char_value)
{
	// Проверяем, есть ли у теущего товара характеристика с указанным значением - если есть - ничего не делаем
	for (key in items_obj[item_id]['char'][char_name]['value'])
	{
		// Если значение выбранной характеристики совпадает с её значением у текущего товара - оставляем товар.
		if (char_value == items_obj[item_id]['char'][char_name]['value'][key])
		{
			return item_id;
		}
	}

	// Ищем массив данных для следующего товара
	for (i in items_obj) // перебираем товары
	{
		// У данного товара перебираем характеристики
		for (c in items_obj[i]['char'][char_name]['value']) // перебираем товары
		{
			if (char_value == items_obj[i]['char'][char_name]['value'][c])
			{
				item_id_next = i;
			}
		}
	}

	document.getElementById('component').innerHTML = items_out[item_id_next];

	return item_id_next;
}