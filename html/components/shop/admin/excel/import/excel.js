var excel = {};

// TASK
excel.TASK_LOAD_ITEMS = 0;
excel.TASK_LOAD_ITEMS_WITHOUT_IMAGE = 1;
excel.TASK_LOAD_IMAGES = 2;
excel.TASK_DELETE_ALL_IMAGES = 3;
excel.TASK_DELETE_ALL_CHARS = 4;
excel.TASK_DELETE_ALL = 5;

// TYPE MESSAGE
excel.MESSAGE_TYPE_NOTICE = [0, 'excel_log_notice'];
excel.MESSAGE_TYPE_ERROR = [1, 'excel_log_error'];

excel.run = function()
{
	var task = document.getElementById('excel_task');

	if(task == null)
	{
		excel.log('Критическая ошибка', excel.MESSAGE_TYPE_ERROR[0]);
		return;
	}

	excel.log('Запуск.');

	switch(parseInt(task.value))
	{
		case excel.TASK_LOAD_ITEMS:
		case excel.TASK_LOAD_ITEMS_WITHOUT_IMAGE:
			excel.loadPrice(excel.taskLoadItems);
			break;

		case excel.TASK_LOAD_IMAGES:
			excel.taskLoadImagesStep0();
			break;

		case excel.TASK_DELETE_ALL_IMAGES:
			excel.taskDeleteAllImages();
			break;

		case excel.TASK_DELETE_ALL_CHARS:
			excel.taskDeleteAllChars();
			break;

		case excel.TASK_DELETE_ALL:
			excel.taskDeleteAll();
			break;

		default:
			excel.log('Запуск неудачен. Не выбрано задание.');
			break;
	}
}

excel.taskLoadItems = function(_index, _maxIndex)
{
	if(_index <= 0)
	{
		excel.log('Начинается обработка товаров.');
		excel.startCounter(_maxIndex);
		_index = 1;
	}

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = req.responseText;

			if(responce > 0)
			{
				_index = parseInt(responce);
				excel.updateCounter(_index);
				excel.taskLoadItems(_index, _maxIndex);
				return;
			}
			else
			{
				excel.loadRelatedItems(0, _maxIndex);
				return;
			}
		}
	};

	var form = new FormData();
	form.append('index', _index);

	var task = parseInt(document.getElementById('excel_task').value);

	if(task == excel.TASK_LOAD_ITEMS_WITHOUT_IMAGE)
		form.append('with_image', '0');
	else
		form.append('with_image', '1');

	req.open('POST', '/admin/com/shop/excel/import/load_items', true);
	req.send(form);
};

excel.loadRelatedItems = function(_index, _maxIndex)
{
	if(_index === 0)
	{
		excel.log('Обработка сопутствующих товаров.');
		excel.startCounter(_maxIndex);
		_index = 1;
	}

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = Math.round(req.responseText) || -1;

			if(responce > 0)
			{
				_index = responce;
				excel.updateCounter(_index);
				excel.loadRelatedItems(_index, _maxIndex);
				return;
			}
			else
			{
				excel.stopCounter();
				excel.log('Загрузка полностью завершена.');
			}
		}
	};

	var form = new FormData();
	form.append('index', _index);

	req.open('POST', '/admin/com/shop/excel/import/load_related_items', true);
	req.send(form);
};

/*
	step_0 - проверки и получение кол-ва товаров.

	step_1 - обращение к серверу для получения имен изображений для товара _offset.
		Возвращает status, id, images.
		status 0 - загрузка закончена, 1 - продолжить

	step_2 - отправка серверу изображений. После отправки переход к шагу 1.
*/

excel.taskLoadImagesStep0 = function()
{
	var files = document.getElementById('excel_files');

	if(files == null || files.files.length == 0)
	{
		excel.log('Не выбрано ни одного изображения.', excel.MESSAGE_TYPE_ERROR[0]);
		return;
	}

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);

			if(responce > 0)
			{
				excel.log("Загрузка изображений запущена");
				excel.startCounter(responce);
				excel.taskLoadImagesStep1(0);
			}
			else
			{
				excel.log("Загрузка изображений прервана, в интернет - магазине нету товаров", excel.MESSAGE_TYPE_ERROR[0]);
			}
		}
	}

	req.open('POST', '/admin/com/shop/excel/import/load_images_step_0', true);
	req.send(null);
}

excel.taskLoadImagesStep1 = function(_offset)
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			_offset++;
			excel.updateCounter(_offset);

			var responce = req.responseText;
			var answer = JSON.parse(responce);

			if(answer.status == 0)
			{
				excel.log('Загрузка изображений закончена.');
				excel.stopCounter();
				return;
			}

			if(answer.id > 0 && answer.images.length > 0)
				excel.taskLoadImagesStep2(_offset, answer.id, answer.images);
			else
				excel.taskLoadImagesStep1(_offset);
		}
	}

	var form = new FormData();
	form.append("offset", _offset);

	req.open('POST', '/admin/com/shop/excel/import/load_images_step_1', true);
	req.send(form);
}

excel.taskLoadImagesStep2 = function(_offset, _id, _images)
{
	var files = document.getElementById("excel_files").files;
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var answer = req.responseText;
			if(answer.length)
				excel.log(answer);

			excel.taskLoadImagesStep1(_offset);
		}
	}

	var form = new FormData();
	form.append("id", _id);

	for(var i = 0;i < _images.length;i++)
	{
		for(var f = 0;f < files.length;f++)
		{
			if(_images[i] == files[f]['name'])
			{
				form.append("images[]", files[f]);
				break;
			}
		}
	}

	req.open('POST', '/admin/com/shop/excel/import/load_images_step_2', true);
	req.send(form);
}

excel.taskDeleteAllImages = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
			excel.log('Все изображения удалены.');
	}

	req.open('POST', '/admin/com/shop/excel/import/delete_all_images', true);
	req.send(null);
}

excel.taskDeleteAllChars = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
			excel.log('Все характеристики удалены.');
	}

	req.open('POST', '/admin/com/shop/excel/import/delete_all_chars', true);
	req.send(null);
}

excel.taskDeleteAll = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
			excel.log('Интернет - магазин очищен.');
	}

	req.open('POST', '/admin/com/shop/excel/import/delete_all', true);
	req.send(null);
}

/*
	При успешной загрузке возвращает кол-во товаров. if <= 0 == error.
*/

excel.loadPrice = function(_callback)
{
	var req = getXmlHttp();
	var files = document.getElementById('excel_files');

	if(files == null || files.files.length == 0)
	{
		excel.log('Не выбраны файлы.', excel.MESSAGE_TYPE_ERROR[0]);
		return;
	}

	if(files.files.length > 1)
		excel.log('Выбрано больше одного файла. Будет загружен и обработан только первый.', excel.MESSAGE_TYPE_ERROR[0]);

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);

			if(responce <= 0)
			{
				excel.log("Прайс - лист не был загружен.", excel.MESSAGE_TYPE_ERROR[0]);
				return;
			}

			excel.log('Прайс-лист загружен успешно.');
			_callback(0, responce);
		}
	}

	var form = new FormData();
	form.append('file', files.files[0]);

	req.open('POST', '/admin/com/shop/excel/import/load_price', true);
	req.send(form);
}

/*
	Лог. Текст сообщения и тип.
*/

excel.log = function(_message, _type)
{
	if(typeof(_type) === 'undefined')
		_type = excel.MESSAGE_TYPE_NOTICE;

	var log = document.getElementById('excel_log');
	var cssClass = excel.MESSAGE_TYPE_NOTICE[1];

	if(log == null)
		return;

	switch(_type)
	{
		case excel.MESSAGE_TYPE_ERROR[0]:
			cssClass = excel.MESSAGE_TYPE_ERROR[1];
			break;
	}

	var node = document.createElement("div");
	node.className = cssClass;
	node.innerHTML = _message;
	log.insertBefore(node, log.firstChild);
}

/*
	Счетчик.
*/

excel.startCounter = function(_maxNumber)
{
	var counterMain = document.getElementById("excel_counter_main");
	var buttonStart = document.getElementById("excel_button_start");

	if(counterMain != null)
		counterMain.innerHTML = '<progress id="excel_counter" value="0" max="'+_maxNumber+'"></progress>';

	if(buttonStart != null)
		buttonStart.style.display = 'none';
}

excel.stopCounter = function()
{
	var counterMain = document.getElementById("excel_counter_main");
	var buttonStart = document.getElementById("excel_button_start");

	if(counterMain != null)
		counterMain.innerHTML = '';

	if(buttonStart != null)
		buttonStart.style.display = 'inline-block';
}

excel.isActiveCounter = function()
{
	var counter = document.getElementById("excel_counter");

	if(counter != null)
		return true;

	return false;
}

excel.updateCounter = function(_value)
{
	var counter = document.getElementById("excel_counter");
	_value = parseInt(_value);

	if(counter != null)
		counter.value = _value;
}
