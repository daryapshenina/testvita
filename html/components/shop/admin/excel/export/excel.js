var excel = {};

// TASK
excel.TASK_EXPORT_ITEMS = 0;
excel.TASK_EXPORT_IMAGES = 1;

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
		case excel.TASK_EXPORT_ITEMS:
			excel.export_step_0();
			break;

		case excel.TASK_EXPORT_IMAGES:
			excel.export_image_step_0();
			break;

		default:
			excel.log('Запуск неудачен. Не выбрано задание.');
			break;
	}
}

/*
	Получаем кол-во товаров.
*/

excel.export_step_0 = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);

			if(isNaN(responce))
			{
				excel.log('Произошла ошибка. Выгрузка невозможна.')
				return;
			}

			if(responce == 0)
			{
				excel.log('В интернет - магазине нету товаров. Выгрузка невозможна.')
				return;
			}

			excel.log('Начинается обработка.');
			excel.startCounter(responce);
			excel.export_step_1(0);
			return;
		}
	}

	req.open('POST', '/admin/com/shop/excel/export/step_0', true);
	req.send(null);
}

/*
	Передача индекса для записи данных товаров в файл.
*/

excel.export_step_1 = function(_index)
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);

			if(responce > 0)
			{
				excel.updateCounter(responce);
				excel.export_step_1(responce);
			}
			else
			{
				excel.log('Экспорт товаров завершен. <a href="/temp/excel/price_export.xlsx">Здесь</a> Вы можете скачать прайс - лист товаров.');
				excel.stopCounter();
			}

			return;
		}

		if(req.readyState == 4)
		{
			excel.log('Во время экспорта прайс-листа произошла ошибка на сервере (' + req.status + '). Обратитесь к администратору.', excel.MESSAGE_TYPE_ERROR[0]);
			return;
		}
	}

	var form = new FormData();
	form.append('index', _index);

	req.open('POST', '/admin/com/shop/excel/export/step_1', true);
	req.send(form);
}

/*
	Выгрузка изображений
*/

excel.export_image_step_0 = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);
			excel.startCounter(responce);
			excel.export_image_step_1(0);
			return;
		}
	}

	req.open('POST', '/admin/com/shop/excel/export/image_step_0', true);
	req.send(null);
}

excel.export_image_step_1 = function(_index)
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);

			if(responce > 0)
			{
				excel.updateCounter(responce);
				excel.export_image_step_1(responce);
			}
			else
			{
				excel.log('Экспорт изображений завершен. <a href="/temp/excel/photos.zip">Здесь</a> находится архив фотографий.');
				excel.stopCounter();
			}

			return;
		}

		if(req.readyState == 4)
		{
			excel.log('Во время экспорта изображений произошла ошибка на сервере (' + req.status + '). Обратитесь к администратору.', excel.MESSAGE_TYPE_ERROR[0]);
			return;
		}
	}

	var form = new FormData();
	form.append('index', _index);

	req.open('POST', '/admin/com/shop/excel/export/image_step_1', true);
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
