var yml = {};

/*
	Run
*/

yml.run = function()
{
	yml.log('Запуск.');
	yml.step_0();
};

/*
	Step 0
*/

yml.step_0 = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var itemsCount = parseInt(req.responseText);

			if(isNaN(itemsCount))
				return yml.log('Произошла ошибка. Выгрузка невозможна.', true);

			if(itemsCount === 0)
				return yml.log('В интернет - магазине нету товаров. Выгрузка невозможна.', true);

			yml.log('Начинается обработка.');
			yml.startCounter(itemsCount);
			yml.step_1(0);
		}
	};

	req.open('GET', '/admin/com/shop/yml/step_0', true);
	req.send(null);
};

/*
	Step 1
*/

yml.step_1 = function(_index)
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			var responce = parseInt(req.responseText);

			if(responce > 0)
			{
				yml.updateCounter(responce);
				yml.step_1(responce);
			}
			else
			{
				yml.log('Экспорт завершен. <a href="/admin/com/shop/yml/save">Здесь</a> Вы можете скачать прайс - лист.');
				yml.stopCounter();
			}

			return;
		}
	};

	var form = new FormData();
	form.append('index', _index);

	req.open('POST', '/admin/com/shop/yml/step_1', true);
	req.send(form);
};

/*
	Log
*/

yml.log = function(_message, _isError)
{
	var log = document.getElementById('yml_log');
	var cssClass = 'yml_log_notice';

	if(log === null)
		return;

	if(_isError)
		cssClass = 'yml_log_error';

	var node = document.createElement("div");
	node.className = cssClass;
	node.innerHTML = _message;
	log.insertBefore(node, log.firstChild);
};

/*
	Counter
*/

yml.startCounter = function(_maxNumber)
{
	var counterMain = document.getElementById("yml_counter_main");
	var buttonStart = document.getElementById("yml_button_start");

	if(counterMain === null || buttonStart === null)
		return yml.log('Произошла ошибка в startCounter. Выгрузка невозможна.', true);

	counterMain.innerHTML = '<progress id="yml_counter" value="0" max="'+_maxNumber+'"></progress>';
	buttonStart.style.display = 'none';
};

yml.stopCounter = function()
{
	var counterMain = document.getElementById("yml_counter_main");
	var buttonStart = document.getElementById("yml_button_start");

	if(counterMain === null || buttonStart === null)
		return yml.log('Произошла ошибка в stopCounter. Выгрузка невозможна.', true);

	counterMain.innerHTML = '';
	buttonStart.style.display = 'inline-block';
};

yml.updateCounter = function(_value)
{
	_value = parseInt(_value);

	var counter = document.getElementById("yml_counter");

	if(counter !== null)
		counter.value = _value;
};
