var yml = {};
/* --- Run --- */

yml.run = function()
{
	yml.log('Запуск.');
	yml.step_0();
};



/* ---Step 0 --- */

yml.step_0 = function()
{
	yml.log('Загрузка файла');

	var file = document.getElementById("yml_file");
	var req = getXmlHttp();

	if(file.files.length === 0)
		return yml.log('Произошла ошибка. Файл не выбран!', true);

	req.onreadystatechange = function()
	{
		if(req.readyState !== 4)
			return;

		if(req.status !== 200)
			return yml.log('Произошла ошибка при загрузке файла. Статус: ' + req.status, true);

		var responce = req.responseText;

		if(responce === "error")
			return yml.log('Произошла ошибка при загрузке файла!', true);

		var itemsCount = parseInt(responce) || 0;

		if(itemsCount <= 0)
			return yml.log('Произошла ошибка при загрузке файла', true);

		document.getElementById('yml_counter_sum').innerHTML = itemsCount;

		yml.log('Начинается обработка');
		yml.startCounter(itemsCount);		
		yml.step_1(0, itemsCount);
	};

	var root = document.getElementById('root').value;

	var form = new FormData();
	form.append('file', file.files[0]);
	form.append('root', root);

	req.open('POST', '/admin/com/shop/yml/import/step_0', true);
	req.send(form);
}



/* --- Step 1 --- */

yml.step_1 = function(_index, _maxIndex)
{
	if(document.getElementById('new').checked == true)	var new_check = 1; else var new_check = 0;
	if(document.getElementById('sale').checked == true)	var sale_check = 1; else var sale_check = 0;
	if(document.getElementById('delete_old').checked == true) var delete_old = true; else var delete_old = false;	

	var req = getXmlHttp();

	req.onreadystatechange = function()
	{
		if(req.readyState !== 4)
			return;

		if(req.status !== 200)
			return yml.log('Произошла ошибка при импорте товаров. Статус: ' + req.status, true);

		var responce = req.responseText;

		if(responce === "error")
			return yml.log('Произошла ошибка при импорте товаров!', true);

		responce = parseInt(responce) || 0;

		document.getElementById('yml_counter_current').innerHTML = responce;

		if(responce < _maxIndex)
		{
			yml.updateCounter(responce);
			yml.step_1(responce, _maxIndex);
		}
		else
		{
			yml.stopCounter();

			if(!delete_old)
			{
				yml.log('Импорт товаров завершен.');
			}
			else
			{
				yml.log('Удаляем старые товары.');
				yml.step_2();
			}
		}
	}

	var root = document.getElementById('root').value;
	var price_input_tab = document.getElementById('prite_input_tab');
	var price_input = price_input_tab.getElementsByClassName('input price_type');


	var form = new FormData();
	form.append('index', _index);
	form.append('root', root);
	form.append('new', new_check);
	form.append('sale', sale_check);

	for(var i = 0; i < price_input.length; i++)
	{
		form.append(price_input[i].name, price_input[i].value);
	}	

	req.open('POST', '/admin/com/shop/yml/import/step_1', true);
	req.send(form);
}



/* --- Step 2 --- */

yml.step_2 = function()
{
	var req = getXmlHttp();

	req.onreadystatechange = function()
	{	
		if(req.readyState !== 4) return;

		if(req.status !== 200) return yml.log('Произошла ошибка при импорте товаров. Статус: ' + req.status, true);

		var responce = req.responseText;

		if(responce === "error") return yml.log('Произошла ошибка при импорте товаров!', true);
		if(responce == 'success')yml.log('Импорт товаров завершен.');
	}

	var root = document.getElementById('root').value;

	var form = new FormData();
	form.append('root', root);
	req.open('POST', '/admin/com/shop/yml/import/step_2', true);
	req.send(form);
}



/* --- Log --- */

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



/* --- Counter --- */

yml.startCounter = function(_maxNumber)
{
	var counterMain = document.getElementById("yml_counter_main");
	var buttonStart = document.getElementById("yml_button_start");
	var root = document.getElementById('root');

	if(counterMain === null || buttonStart === null || root === null)
		return yml.log('Произошла ошибка в startCounter. Выгрузка невозможна.', true);

	counterMain.innerHTML = '<progress id="yml_counter" value="0" max="'+_maxNumber+'"></progress>';
	buttonStart.style.display = 'none';
	root.disabled = true;
};

yml.stopCounter = function()
{
	var counterMain = document.getElementById("yml_counter_main");
	var buttonStart = document.getElementById("yml_button_start");
	var root = document.getElementById('root');

	if(counterMain === null || buttonStart === null || root === null)
		return yml.log('Произошла ошибка в stopCounter. Выгрузка невозможна.', true);

	counterMain.innerHTML = '';
	document.getElementById('yml_counter_current').innerHTML = '';
	document.getElementById('yml_counter_sum').innerHTML = '';		
	buttonStart.style.display = 'inline-block';
	root.disabled = false;
};

yml.updateCounter = function(_value)
{
	_value = parseInt(_value) || 0;

	var counter = document.getElementById("yml_counter");

	if(counter !== null)
		counter.value = _value;
};
