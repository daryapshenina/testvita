DAN_ready(dan_lightbox);

function dan_lightbox()
{
	// Кроссбраузерно getElementsByClassName
	if (!document.getElementsByClassName) {
		document.getElementsByClassName = function (class_name) {

			// Получим коллекцию элементов тега body:
			var elements = document.body.getElementsByTagName("*"),
				length   = elements.length,
				out = [], i;

			// Пройдёмся по ним... увы циклом:
			for (i = 0; i < length; i += 1)
			{
				// Поместим в результирующий массив элементы, содержащие требуемый класс:
				if (elements[i].className.indexOf(class_name) !== -1) {out.push(elements[i]);}
			}
			return out;
		};
	}

	var dan_show = document.getElementsByClassName('show');
	var dan_show_length = dan_show.length; // так быстрее работает в цикле
	var play_start = 0; // шоу остановлено
	var dan_lbox = new Array(); // виртуальный массив лайтбокса - ещё не выведенные изображения	
	var dan_lightbox = new Array(); // массив лайтбокса - уже выведенные изображения

	// Создаём изображения (нужно для предварительной загрузки, особенно изображений по ссылке.)
	images();
	
	for (var i = 0; i < dan_show_length; i++)
	{

		var img_count = 0;
		dan_lbox[i].onload = function(){

			for (var i = 0; i < dan_show_length; i++)
			{
				dan_show[i].onclick = function()
				{
					// защита от повторного создания модального окна
					if (document.getElementById('dan_lightbox') == null)
					{
						// Запускаем чёрное окно
						black();

						// Создаём белое окно
						white();

						// Получаем текущее большое изображение
						img_big = images_this(this, true);

						// Создаём навигацию
						navigation();

						var img_start = '';
						var img_end = images_this(this, false);

						resize(img_start, img_end);

					} // защита от повторного создания модального окна

					// запрещаем переход по ссылке
					return false;
				}
			}
		}
	}



	// ####### ФУНКЦИИ ######################################################################
	// Ожидание загрузки
	function waiting (w)
	{
		if (w == 1)
		{
			if(!document.getElementById('dan_lightbox_waiting'))
			{
				dan_lightbox_waiting = document.createElement('img');
				dan_lightbox_waiting.id = 'dan_lightbox_waiting';
				dan_lightbox_waiting.style.zIndex = 1010;

				var dan_body_child_0 = document.body.children[0];
				document.body.insertBefore(dan_lightbox_waiting, dan_body_child_0);
			}
		}
		else
		{
			if (typeof(dan_lightbox_waiting) !== "undefined"){document.body.removeChild(dan_lightbox_waiting);}
		}
	}


	// Черное окно
	function black ()
	{
		// Чёрный слой
		var dan_lightbox_black = document.createElement('div');
		dan_lightbox_black.id = 'dan_lightbox_black';
		dan_lightbox_black.title = 'Закрыть окно';

		dan_lightbox_black.style.backgroundColor = '#000000';
		dan_lightbox_black.style.opacity = 0;
		dan_lightbox_black.style.filter = 'alpha(opacity = 0)';

		var dan_body_child_0 = document.body.children[0];
		document.body.insertBefore(dan_lightbox_black, dan_body_child_0);

		// прозрачность
		var op = 0;

		// Каждые 20 миллисекунд запускаем функцию
		timer_black = setInterval(function() {
			if (op <= 50)
			{
				dan_lightbox_opacity = op/100;
				document.getElementById('dan_lightbox_black').style.opacity = dan_lightbox_opacity;
				op = op + 5;
			}
			else
			{
				document.getElementById('dan_lightbox_black').style.opacity = 0.5;
				clearTimeout(timer_black);
			}
		}, 20);

		dan_lightbox_black.onclick = close;
	}


	// Белое окно
	function white(width, height)
	{
		// Белое окно
		var dan_lightbox_white = document.createElement('div');
		dan_lightbox_white.id = 'dan_lightbox_white';

		var dan_body_child_0 = document.body.children[0];
		document.body.insertBefore(dan_lightbox_white, dan_body_child_0);
	}


	// Создаём изображения
	function images (img_this)
	{
		for (var i = 0; i < dan_show_length; i++)
		{
			// создаём изображения
			dan_lbox[i] = document.createElement('img');

			if (dan_show[i].tagName == 'A'){dan_lbox[i].src = dan_show[i];}
			if (dan_show[i].tagName == 'IMG'){dan_lbox[i].src = dan_show[i].src;}
		}
	}


	// Текущее изображение + признак того, что надо создать изображения в белом поле
	function images_this (img_this, add_to_white)
	{
		// получаем src объекта, по которому кликнули
 		if (img_this.tagName == 'A'){img_this_src = img_this;}
		if (img_this.tagName == 'IMG'){img_this_src = img_this.src;}

		//находим, каким элементом массива стоит "this"
		for (var i = 0; i < dan_show_length; i++)
		{
			// Добавляем изображение в белое поле
			if (add_to_white)
			{			
				// создаём изображения
				var img_out = document.createElement('img');
				img_out.src = dan_lbox[i].src;

				// изменим размер изображения
				img_out = dan_img_size(img_out);

				img_out.className = 'dan_lightbox_images';
				img_out.style.display = 'none';
				img_out.style.opacity = 0;
				img_out.style.filter = 'alpha(opacity = 0)';

				// добавляем изображение в узел
				dan_lightbox[i] = dan_lightbox_white.appendChild(img_out);				
			}

			// находим большую фотографию для объекта, по которому кликнули
			if (dan_lightbox[i].src == img_this_src){img_return = dan_lightbox[i];}
		}

		return img_return;
	}


	// Функция устанавливает размер изображений с учётом ширины окна
	function dan_img_size (img_this)
	{
		var displayHeight = document.documentElement.clientHeight;
		var displayWidth = document.documentElement.clientWidth;

		// конечные значения - размер нашего изображения
		var dan_lightbox_end_h = img_this.naturalHeight;
		var dan_lightbox_end_w = img_this.naturalWidth;
		
		
		// рассчитываем пропорции
		var k_H = dan_lightbox_end_h/displayHeight;
		var k_W = dan_lightbox_end_w/displayWidth;
		
		if (k_W > k_H)
		{
			if (k_W > 0.8)
			{
				dan_lightbox_end_h = parseInt(0.8 * dan_lightbox_end_h * (displayWidth / dan_lightbox_end_w));
				dan_lightbox_end_w = parseInt(0.8 * displayWidth);
			}			
		}
		else
		{
			if (k_H > 0.8)
			{
				dan_lightbox_end_w = parseInt(0.8 * dan_lightbox_end_w * (displayHeight / dan_lightbox_end_h));
				dan_lightbox_end_h = parseInt(0.8 * displayHeight);
			}
		}		

		// Размер изображения
		img_this.style.height = dan_lightbox_end_h + 'px';
		img_this.style.width = dan_lightbox_end_w + 'px';

		return img_this;
	}


	// Создаём элементы навигации
	function navigation ()
	{
		var nav_prev = document.createElement('div');
		nav_prev.id = 'dan_lightbox_nav_prev';
		dan_lightbox_white.appendChild(nav_prev);
		nav_prev.onclick = prev;

		var nav_next = document.createElement('div');
		nav_next.id = 'dan_lightbox_nav_next';
		dan_lightbox_white.appendChild(nav_next);
		nav_next.onclick = next;

		var nav_play = document.createElement('div');
		nav_play.id = 'dan_lightbox_nav_play';
		dan_lightbox_white.appendChild(nav_play);
		nav_play.onclick = play;

		if (dan_show_length >  1)
		{
			document.getElementById('dan_lightbox_nav_next').style.display = 'block';
			document.getElementById('dan_lightbox_nav_prev').style.display = 'block';
			document.getElementById('dan_lightbox_nav_play').style.display = 'block';
		}

		// Кнопка "Закрыть"
		var dan_lightbox_close = document.createElement('div');
		dan_lightbox_close.id = 'dan_lightbox_close';
		dan_lightbox_close.title = 'Закрыть окно';
		dan_lightbox_white.appendChild(dan_lightbox_close);
		dan_lightbox_close.onclick = close;
		document.getElementById('dan_lightbox_close').style.opacity = 1;
	}


	// ======= ИЗМЕНЕНИЕ РАЗМЕРА ====================================================
	// Увеличение высоты окна
	function resize(img_start, img_end)
	{
		stop_animation(img_start, img_end);
		if (!img_end.complete){
	
			waiting(1);

			img_end.onload = function(){				
				waiting(0);
				dan_img_size(img_end);
				resize_height(img_start, img_end);
			}
		}
		else
		{
			if(img_end.style.width == '0px'){dan_img_size(img_end);}
			resize_height(img_start, img_end);
		}



		function resize_height(img_start, img_end)
		{
			if (img_start == '' || img_start == 'undefined')
			{
				var height_start = 100;
			}
			else
			{
				// Скрываем старое изображение
				img_start.style.display = 'none';
				img_start.style.opacity = 0;

				height_start = parseInt(img_start.style.height);

				// на случай незагруженного
				if (height_start == 0)
				{
					dan_img_size(img_start);
					height_start = parseInt(dan_img_size(img_start).style.height);
				}
			}

			// делаем следующее изображение 'inline'
			// почему здесь - что бы не было задержки при анимации (двойной клик меняет изображение, а по свойству 'inline' определяем текущее изображение
			img_end.style.display = 'inline';

			var height_end = parseInt(img_end.style.height);

			var height_step = parseInt((height_end - height_start)/5);
		
			if (height_start != height_end)
			{
				// --- Изменение высоты
				var i = 0;	// i - счётчик циклов

				timer_resize_height = setInterval(function(){
					i++;
					height_start = height_start + height_step;

					document.getElementById('dan_lightbox_white').style.height = height_start + 'px';
					document.getElementById('dan_lightbox_white').style.marginTop = '-' + parseInt(height_start / 2 + 20) + 'px';

					// Остановка таймера
					if (i > 4)
					{
						document.getElementById('dan_lightbox_white').style.height = height_end + 'px';
						document.getElementById('dan_lightbox_white').style.marginTop = '-' + parseInt(height_end / 2 + 20) + 'px';
						stop_animation();
						resize_width(img_end);
					}

				}, 20);
			}
			else{resize_width(img_end);}	
		}



		function resize_width(img_end)
		{
			stop_animation();

			if (img_start == '' || img_start == 'undefined')
			{
				var width_start = 100;
			}
			else
			{
				width_start = parseInt(img_start.style.width);

				// на случай незагруженного
				if (width_start == 0)
				{
					width_start = parseInt(dan_img_size(img_start).style.width);
				}
			}

			var width_end = parseInt(dan_img_size(img_end).style.width);

			// Если всё-таки размер нулевой
			if (width_start == 0){width_start = 100;}
			if (width_end == 0){width_end = 100;}

			var width_step = parseInt((width_end - width_start)/5);

			if (width_start != width_end)
			{
				// --- Изменение ширины
				var j = 0;
				timer_resize_width = setInterval(function(){

					j++;
					width_start = width_start + width_step;

					document.getElementById('dan_lightbox_white').style.width = width_start + 'px';
					document.getElementById('dan_lightbox_white').style.marginLeft = '-' + parseInt(width_start / 2 + 20) + 'px';

					// Остановка таймера
					if (j > 4)
					{
						document.getElementById('dan_lightbox_white').style.width = width_end + 'px';
						document.getElementById('dan_lightbox_white').style.marginLeft = '-' + parseInt(width_end / 2 + 20) + 'px';
						stop_animation();

						opacity(img_end);
					}

				}, 20);
			}
			else {opacity(img_end);}
		}

	}


	// Прозрачность изображения и элементов навигации
	function opacity(img_end)
	{
		stop_animation();

		// Прозрачность
		var op = 0;

		timer_opacity = setInterval(function(){
			op = op + 0.1;

			img_end.style.opacity = op;

			if (op > 1)
			{
				img_end.style.opacity = 1;

				stop_animation();
			}

		}, 20);
	}


	// удаляем модальное окно
	function close ()
	{
		stop_play();
		stop_animation();
		if (typeof(dan_lightbox_white) !== "undefined"){document.body.removeChild(dan_lightbox_white);}
		if (typeof(dan_lightbox_waiting) !== "undefined" && document.getElementById('dan_lightbox_waiting') != null){document.body.removeChild(dan_lightbox_waiting);}
		document.body.removeChild(dan_lightbox_black);
	}


	// навигация - следующий элемент
	function next()
	{
		stop_play();
		stop_animation();

		m = num();
		var n = m + 1;
		// переходим на первое изображение
		if (m > dan_show_length - 1){m = 0;}
		if (n > dan_show_length - 1){n = 0;}

		var img_start = dan_lightbox[m];
		var img_end = dan_lightbox[n];

		resize(img_start, img_end);
	}


	// навигация - следующий элемент
	function prev()
	{
		stop_play();
		stop_animation();

		m = num();
		var n = m - 1;
		// переходим на последнее изображение
		if (m < 0){m = dan_show_length - 1;}
		if (n < 0){n = dan_show_length - 1;}

		var img_start = dan_lightbox[m];
		var img_end = dan_lightbox[n];

		resize(img_start, img_end);
	}


	// навигация - play
	function play()
	{
		// триггер
		if (play_start == 0)
		{
			// переключаем состояние триггера
			play_start = 1;
			document.getElementById('dan_lightbox_nav_play').style.backgroundPosition = '0px -40px';

			m = num();
			var n = m + 1;
			// переходим на первое изображение
			if (n > dan_show_length - 1){n = 0;}

			var img_start = dan_lightbox[m];
			var img_end = dan_lightbox[n];

			// запускае сразу по нажатию кнопки
			resize(img_start, img_end);
			// следующее изображений
			m++;
			if (m > dan_show_length - 1){m = 0;}

			// запускаем таймер
			timer_play = setInterval(function(){
				n = m + 1;

				// переходим на первое изображение
				if (n > dan_show_length - 1){n = 0;}
				
				
				if (!img_end.complete){
					
					// останавливаем плеер
					stop_play();
					stop_animation();
					
					waiting(1);

					img_end.onload = function(){
						waiting(0);
						play(); // перезапускаем плеер
						
						return false;
					}
				}

				img_start = dan_lightbox[m];
				img_end = dan_lightbox[n];

				resize(img_start, img_end);

				m++;
				if (m > dan_show_length - 1){m = 0;}
			}, 3000);
		}
		else
		{
			// переключаем состояние триггера
			play_start = 0;
			document.getElementById('dan_lightbox_nav_play').style.backgroundPosition = '0px 0px';

			// останавливаем play по триггеру
			stop_play();
			stop_animation();
		}
	}


	// Возвращает порядковый номер большого отображаемого изображения
	function num()
	{
		for (var k = 0; k < dan_show_length; k++)
		{
			// активный элемент тот, у которого свойство display = inline
			if (dan_lightbox[k].style.display == 'inline')
			{
				return k;
			}
		}
	}


	function stop_animation()
	{
		// Тормозим все таймеры, если они запущены
		if(typeof(timer_resize_height) !== "undefined"){clearTimeout(timer_resize_height);}
		if(typeof(timer_resize_width) !== "undefined"){clearTimeout(timer_resize_width);}
		if(typeof(timer_opacity) !== "undefined"){clearTimeout(timer_opacity);}
	}


	function stop_play()
	{
		if(typeof(timer_play) !== "undefined")
		{
			// переключаем состояние триггера
			play_start = 0;
			document.getElementById('dan_lightbox_nav_play').style.backgroundPosition = '0px 0px';

			clearTimeout(timer_play)
		}
	}

}