var csv = {};

// Type Load
csv.TYPE_LOAD_ITEMS = 0;
csv.TYPE_LOAD_ITEMS_WITHOUT_IMAGES = 1;
csv.TYPE_LOAD_CHARS = 2;
csv.TYPE_LOAD_IMAGES = 3;
csv.TYPE_CLEAR_CHARS = 4;
csv.TYPE_CLEAR_SHOP = 5;

// Message color
csv.LOG_TYPE_N = 0;
csv.LOG_TYPE_E = 1;

csv.start = function()
{
	switch(csv.getTypeOfLoad())
	{
		case csv.TYPE_LOAD_ITEMS_WITHOUT_IMAGES:
		case csv.TYPE_LOAD_ITEMS:
		{
			var inputFiles = document.getElementById("files").files;

			if(inputFiles.length == 0)
			{
				csv.addMessage("Не выбрано ни одного файла, загрузка прервана", csv.LOG_TYPE_E);
				return;
			}

			csv.readFile(inputFiles[0], csv.loadItems);
		} break;

		case csv.TYPE_LOAD_CHARS:
		{
			var inputFiles = document.getElementById("files").files;

			if(inputFiles.length == 0)
			{
				csv.addMessage("Не выбрано ни одного файла, загрузка прервана", csv.LOG_TYPE_E);
				return;
			}

			csv.readFile(inputFiles[0], csv.loadChars);
		} break;

		case csv.TYPE_LOAD_IMAGES:
		{
			var inputFiles = document.getElementById("files").files;

			if(inputFiles.length == 0)
			{
				csv.addMessage("Не выбрано ни одного изображения, загрузка прервана", csv.LOG_TYPE_E);
				return;
			}

			csv.loadImages(inputFiles);
		} break;

		case csv.TYPE_CLEAR_CHARS:
		{
			csv.clearChars();
		} break;

		case csv.TYPE_CLEAR_SHOP:
		{
			csv.clearShop();
		} break;
	}
}

csv.readFile = function(_file, _callback)
{
	var reader = new FileReader();
	csv.addMessage("Чтение файла", csv.LOG_TYPE_N);

	reader.onload = function(event)
	{
		_callback(event.target.result.split('\n'));
	};

	reader.onerror = function(event)
	{
		csv.addMessage("Файл не может быть прочитан! код " + event.target.error.code, csv.LOG_TYPE_E);
	};

	reader.readAsText(_file, 'CP1251');
}


/* 0 - Определение товар\раздел
 * 1 - Уровень
 * 2 - Идентификатор
 * 3 - Идентификатор родителя
 * 4 - Артикул
 * 5 - Артикул завода
 * 6 - Название
 * 7 - Цена
 * 8 - Фото
 * 9 - Доп. информация
 * 10- Количество
 */

csv.loadItems = function(_arrayItems)
{
	if(_arrayItems.length == 0)
	{
		csv.addMessage("Загрузка товаров закончена", csv.LOG_TYPE_N);
		csv.counterOff();
		return;
	}

	if(!csv.counterIsActive())
	{
		csv.counterOn(0, _arrayItems.length);
		csv.addMessage("Загрузка товаров начата", csv.LOG_TYPE_N);
	}

	csv.counterIncrement();

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4)
		{
			if(req.status == 200)
			{
				try
				{
					var responce = req.responseText;

					if(responce.length > 0)
						csv.addMessage(responce, csv.LOG_TYPE_E);

					csv.loadItems(_arrayItems);
				}
				catch(e)
				{
					console.log("CSV: critical error. "+req.responseText)
				}
			}
		}
	}

	var itemArray = _arrayItems.shift();
	var item = itemArray.split('#');

	for(var i = 0;i < 11;i++)
	{
		if(typeof(item[i]) == 'undefined')
			item[i] = '';
	}

	if(csv.getTypeOfLoad() == csv.TYPE_LOAD_ITEMS_WITHOUT_IMAGES)
		item[8] = 'null';

	var form = new FormData();
	form.append("type", item[0]);
	form.append("identifier", item[2]);
	form.append("identifier_parent", item[3]);
	form.append("artikul", item[4]);
	form.append("title", item[6]);
	form.append("price", item[7]);
	form.append("photo", item[8]);
	form.append("quantity", item[10]);

	req.open('POST', '/admin/com/shop/csv_spec/items', true);
	req.send(form);
}

/* 0 - ID товарв
 * 1 - Название характеристики
 * 2 - Значение характеристики
*/
csv.loadChars = function(_arrayChars)
{
	if(_arrayChars.length == 0)
	{
		csv.addMessage("Загрузка характеристик закончена", csv.LOG_TYPE_N);
		csv.counterOff();
		return;
	}

	if(!csv.counterIsActive())
	{
		csv.counterOn(0, _arrayChars.length);
		csv.addMessage("Загрузка характеристик начата", csv.LOG_TYPE_N);
	}

	csv.counterIncrement();

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4)
		{
			if(req.status == 200)
			{
				try
				{
					var responce = req.responseText;

					if(responce.length > 0)
						csv.addMessage(responce, csv.LOG_TYPE_E);

					csv.loadChars(_arrayChars);
				}
				catch(e)
				{
					console.log("CSV: critical error. "+req.responseText);
				}
			}
		}
	}

	var charsArray = _arrayChars.shift();
	var chars = charsArray.split('#');

	for(var i = 0;i < 3;i++)
	{
		if(typeof(chars[i]) == 'undefined')
			chars[i] = '';
	}

	var form = new FormData();
	form.append("itemIdentifier", chars[0]);
	form.append("charName", chars[1]);
	form.append("charValue", chars[2]);

	req.open('POST', '/admin/com/shop/csv_spec/chars', true);
	req.send(form);
}

csv.loadImages = function(_arrayImages, _counter)
{
	if(typeof(_counter) === 'undefined')
		_counter = 0;

	if(_counter >= _arrayImages.length)
	{
		csv.addMessage("Загрузка изображений закончена", csv.LOG_TYPE_N);
		csv.counterOff();
		return;
	}

	if(!csv.counterIsActive())
	{
		csv.counterOn(0, _arrayImages.length);
		csv.addMessage("Загрузка изображений начата", csv.LOG_TYPE_N);
	}

	csv.counterIncrement();

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4)
		{
			if(req.status == 200)
			{
				try
				{
					var responce = req.responseText;

					if(responce.length > 0)
						csv.addMessage(responce, csv.LOG_TYPE_E);

					csv.loadImages(_arrayImages, ++_counter);
				}
				catch(e)
				{
					console.log("CSV: critical error. "+req.responseText)
				}
			}
		}
	}

	var images = _arrayImages[_counter];

	var form = new FormData();
	form.append("image", images);

	req.open('POST', '/admin/com/shop/csv_spec/images', true);
	req.send(form);
}

csv.clearChars = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4)
		{
			if(req.status == 200)
			{
				csv.addMessage("Все характеристики товаров удалены", csv.LOG_TYPE_N);
			}
		}
	}

	req.open('POST', '/admin/com/shop/csv_spec/clear_chars', true);
	req.send(null);
}

csv.clearShop = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4)
		{
			if(req.status == 200)
			{
				csv.addMessage("Все товары и категории интернет - магазина удалены", csv.LOG_TYPE_N);
			}
		}
	}

	req.open('POST', '/admin/com/shop/csv_spec/clear', true);
	req.send(null);
}

csv.getTypeOfLoad = function()
{
	var type = document.getElementById("csv_type");

	if(type != null)
		return parseInt(type.value);

	return -999;
}

csv.addMessage = function(_message, _type)
{
	var log = document.getElementById("csv_log_main");
	var cssClass = "csv_message_";

	switch(_type)
	{
		case csv.LOG_TYPE_E:
			cssClass += "e";
			break;

		default:
			cssClass += "n";
			break;
	}

	if(log != null)
	{
		var node = document.createElement("div");
		node.className = cssClass;
		node.innerHTML = _message;
		log.insertBefore(node, log.firstChild);
	}
}

csv.counterOn = function(_startNumber, _maxNumber)
{
	var counterMain = document.getElementById("csv_counter_main");
	var buttonStart = document.getElementById("csv_start");

	if(counterMain != null)
		counterMain.innerHTML = '<progress id="csv_counter" value="'+_startNumber+'" max="'+_maxNumber+'"></progress>';

	if(buttonStart != null)
		buttonStart.style.display = 'none';
}

csv.counterOff = function()
{
	var counterMain = document.getElementById("csv_counter_main");
	var buttonStart = document.getElementById("csv_start");

	if(counterMain != null)
		counterMain.innerHTML = '';

	if(buttonStart != null)
		buttonStart.style.display = 'inline-block';
}

csv.counterIsActive = function()
{
	var counterMain = document.getElementById("csv_counter_main");

	if(counterMain != null && counterMain.innerHTML.length > 0)
		return true;

	return false;
}

csv.counterIncrement = function()
{
	var counter = document.getElementById("csv_counter");

	if(counter != null)
		counter.value++;
}
