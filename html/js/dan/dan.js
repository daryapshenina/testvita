var DAN = {};
DAN.DATA_ADD_CLASS = 'toggleClass';

/*
	data-action - действие
	data-id - ID элемента (над которым совершается действие)
	data-for-id - для какого элемента совершается действие (ID элемента)
	data-p1-n - параметры
*/

/*
	Добавляем \ удаляем класс указанного элемента
	data-action, data-for-id, data-p1 (имя класса)
*/
DAN.toggleClass = function(_id, _className)
{
	if(_id.length === 0 || _className.length === 0)
		return;

	var arrayElements = document.body.getElementsByTagName("*");

	for(var i = 0;i < arrayElements.length;i++)
	{
		var id = arrayElements[i].getAttribute('data-id');

		if(id == _id)
			arrayElements[i].classList.toggle(_className);
	}
};

DAN.event = function(_object)
{
	if(_object === null)
		return;

	var arrayActions = this.getField(_object, 'data-action');

	for(var a = 0;a < arrayActions.length;a++)
	{
		switch(arrayActions[a])
		{
			case DAN.DATA_ADD_CLASS:
			{
				var id = this.getField(_object, 'data-for-id')[a];
				var className = this.getField(_object, 'data-p1')[a];
				DAN.toggleClass(id, className);
			} break;
		}
	}
};

DAN.getField = function(_object, _fieldName)
{
	if(_object === null)
		return;

	var attribute = _object.getAttribute(_fieldName);

	if(attribute === null)
		return;

	return attribute.split(';');
};

// _head_class - класс шапки, _body_class - класс тела; при раскрытии добавляется класс expand
DAN.accordion = function(_head_class, _body_class)
{
	var head = document.getElementsByClassName(_head_class);
	var body = document.getElementsByClassName(_body_class);

	for(var i = 0; i < head.length; i++)
	{
		head[i].onclick = function()
		{
			nodes(this);
		};
	}

	function nodes(_this)
	{
		for(var j = 0; j < head.length; j++)
		{
			if(head[j] == _this){body[j].classList.toggle("expand");}
			else{body[j].classList.remove("expand")}
		}
	}
};

DAN.jumpTo = function(_id, _duration, _offset)
{
	var timeInterval = 16.667;
	var element = document.getElementById(_id);

	_duration = _duration > 0 ? _duration : 0;
	var _offset = parseInt(_offset) || 0;

	// var scrollYLast = -1;

	if(element === null) return;

	var elementPosY = 0;

    while(element)
	{
        elementPosY += parseFloat(element.offsetTop);
        element = element.offsetParent;
    }

	var step = _duration/timeInterval;

	if (_duration == 0)
	{
		window.scrollTo(0, elementPosY);
		return;
	}

	var scrolled = window.pageYOffset || document.documentElement.scrollTop;

	var i = 0;

	function animate(fraction) {

		requestAnimationFrame(function animate(fraction) {

			var fraction = i/step;
			if (fraction > 1) fraction = 1;
			var fract = (Math.sin(fraction*Math.PI - Math.PI/2) + 1)/2;
			var scroll = scrolled + (elementPosY - scrolled + _offset)*fract;

			window.scrollTo(0, scroll);
			i++;

			if (fraction < 1) {requestAnimationFrame(animate);}
		});
	}

	animate(0);
};

DAN.jumpToContent = function()
{
	DAN_ready(function(event)
	{
		if(document.body.offsetWidth < 740)
		{
			DAN.jumpTo("content", 100);
		}
	});
};

// Анимация появления
DAN.appearance = function(_id, _classCss)
{
	var object = document.getElementById(_id);

	if(object === null)
		return;

	var callback = function() {

		var objectY = object.offsetTop;
		var scrollY = window.pageYOffset || document.documentElement.scrollTop;
		var windowBottom = scrollY + window.innerHeight;

		if(scrollY < objectY && objectY < windowBottom)
			object.classList.add(_classCss);
		else
			object.classList.remove(_classCss);

	};

	window.addEventListener("scroll", callback);
	callback();
};

/* col_auto_row и col_auto_height */
window.addEventListener("load", function() {

	var parents = [];
	var elements = [];

	var array = Array.prototype.slice.call(document.getElementsByClassName('col_auto_row'));
	var array2 = Array.prototype.slice.call(document.getElementsByClassName('col_auto_height'));

	elements = Array.prototype.concat.call(array, array2);

	Array.prototype.forEach.call(elements, function(_element) {

		if(_element.classList.contains('col_auto_height'))
		{
			var parent = null;

			parents.some(function(_parent) {
				if(_parent === _element.parentNode)
				{
					parent = _parent;
					return true;
				}
			});

			if(parent === null)
			{
				parents.push(_element.parentNode);
				parent = parents[parents.length - 1];
				parent.classList.add("row");
				parent.__childrens = [];
				parent.__maxHeight = 0;
			}

			parent.__childrens.push(_element);
		}
		else
		{
			_element.parentNode.classList.add("row");
		}

	});

	var resize = function()
	{

		parents.forEach(function(_parent) {

			_parent.__maxHeight = 0;

			_parent.__childrens.forEach(function(_element) {
				_element.children[0].style.minHeight = "";
			});

		});

		parents.forEach(function(_parent) {

			_parent.__childrens.forEach(function(_element) {

				var elementHeight = _element.children[0].offsetHeight;

				if(elementHeight > _parent.__maxHeight)
					_parent.__maxHeight = elementHeight;

			});

		});

		parents.forEach(function(_parent) {

			_parent.__maxHeight = Math.round(_parent.__maxHeight + 0.4);
			var height = _parent.__maxHeight+"px";

			_parent.__childrens.forEach(function(_element) {
				_element.children[0].style.minHeight = height;
			});

		});

	};

	resize();
	window.addEventListener("resize", resize);

});

DAN.modal = {
	block_up: false, // Не блокировать модальное окно

	// Добавить блок
	add: function(_content, _width = 600, _height = 200)
	{
		if(document.getElementById('dan_2_modal_black') !== null)
			return;

		var modal_black = document.createElement('div');
		modal_black.id = 'dan_2_modal_black';
		document.body.insertBefore(modal_black, document.body.children[0]); // черный слой

		var content = document.createElement('div');
		content.id = 'dan_2_modal_content';
		content.innerHTML = _content;

		var modal_white = document.createElement('div');
		modal_white.id = 'dan_2_modal_white';
		modal_white.style.maxWidth = _width + 'px';
		modal_white.style.minHeight = _height + 'px';
		modal_white.appendChild(content);
		modal_black.appendChild(modal_white);

		var cross = document.createElement('div');
		cross.id = 'dan_2_modal_cross';
		cross.innerHTML = '&#215;';
		modal_white.appendChild(cross);

		modal_white.onclick = function(_e)
		{
			_e.stopPropagation();
		}

		modal_black.onclick = function()
		{
			DAN.modal.del();
		}

		cross.onclick = function()
		{
			DAN.modal.del();
		}
	},

	// Удалить блок
	del: function()
	{
		if(!DAN.modal.block_up)
			document.body.removeChild(document.getElementById('dan_2_modal_black'));
	},

	// Блокирует удаление модального окна
	block: function(_b)
	{
		DAN.modal.block_up = Boolean(_b);
	}
}

/* Image scroll */

DAN_ready(function() {

	var elements = document.getElementsByClassName('image_scroll');

	Array.prototype.forEach.call(elements, function(_element) {

		var div = document.createElement('div');
		div._scroll = document.createElement('div');
		div._f = null;
		div.style.height = _element.clientHeight + 'px';
		div.style.backgroundImage = 'url("' + _element.src + '")';
		div.classList.add('image_scroll');

		var autoScroll = function(_element)
		{
			var percent = parseInt(_element.style.backgroundPositionY);

			if(percent > 0)
			{
				percent -= 5;
			}
			else
			{
				clearTimeout(_element._f);
				return;
			}

			_element.style.backgroundPositionY = percent + '%';
			_element._scroll.style.height = percent + '%';
		}

		div.addEventListener('mousemove', function(_event) {

			if(this._f !== null)
			{
				clearTimeout(this._f);
				this._f = null;
			}

			var y = _event.pageY - this.offsetTop;
			var positionY = Math.round(y / this.clientHeight * 100);

			if(positionY > 95)
				positionY = 100;

			this.style.backgroundPositionY = positionY + '%';
			this._scroll.style.height = positionY + '%';

		});

		div.addEventListener('mouseout', function() {
			if(this._f === null)
				this._f = setInterval(autoScroll, 1000 / 30, this);
		});

		div.appendChild(div._scroll);
		_element.parentElement.replaceChild(div, _element);

	});

});

/* Curtain */

window.addEventListener('load', function() {

	var curtains = document.getElementsByClassName('curtain');

	Array.prototype.forEach.call(curtains, function(_element) {

		_element._cursor = document.createElement('div');
		_element._cursor.classList.add('curtain_cursor');
		_element._cursor.innerHTML = '<span></span><span></span>';
		_element.appendChild(_element._cursor);

		var height = Number.MAX_SAFE_INTEGER;
		var width = Number.MAX_SAFE_INTEGER;

		Array.prototype.forEach.call(_element.children, function(_child) {

			if(_child.tagName !== 'IMG')
				return;

			if(_child.offsetHeight < height)
				height = _child.offsetHeight;

			if(_child.offsetWidth < width)
				width = _child.offsetWidth;

		});

		_element.style.height = height + 'px';
		_element.style.width = width + 'px';

		_element.firstChild.style.clip = 'rect(0, ' + Math.round(width / 2) + 'px, auto, 0)';
		_element._cursor.style.left = Math.round(width / 2) - 1 + 'px';

		_element.addEventListener('mousemove', function(_event) {

			var x = _event.pageX - this.offsetLeft;
			this.firstChild.style.clip = 'rect(0, ' + x + 'px, auto, 0)';
			this._cursor.style.left = x - 1 + 'px';

		});


	});

});
