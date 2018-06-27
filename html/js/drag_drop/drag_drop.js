// перед началом перетаскивания данная функция должна быть вызвана с указанными параметрами
// container_id - id контейнера, в котором идёт перетаскивание, также необходимо сделать padding для конечного элемента - ловим ondrop для самой последней позиции
// class_name - класс перетаскиваемых элементов, следует прописать в css класс с перфиксом _over
// у перетаскиваемых элементов должен быть data-id из которого мы вытаскиваем id
// после окончания перетаскивания вызывается функция drag_ordering(id) с параметром id

function drag_drop(container_id, class_name)
{
	var drag_target = document.getElementById(container_id);
	var drag_class = document.getElementsByClassName(class_name);
	var drag_class_length = drag_class.length;
	var finish = false; // признак достижения последнего элемента списка

	for (var i = 0; i < drag_class_length ; i++)
	{
		drag_class[i].ondragstart = f_drag;
		drag_class[i].ondragenter = f_dragenter;
		drag_class[i].ondragend = f_dragend;
		drag_class[i].ondragleave = f_ondragleave;
	}

	if(drag_target)
	{
		drag_target.ondrop = f_drop;
		drag_target.ondragover = f_ondragover;
	}


	function f_drag(e)
	{
		if ((typeof(drag_stop) != 'undefined') && drag_stop == 1){return false;} // drag_stop == 1 - останавливает перенос
		drag_start = 1; // Признак начала переноса, сбрасывается в f_drop на 0. Проверяется в f_dragend - если нет сброса = выход за облать перетаскивания >>> ставим перетаскиваемы обхект в конец
		e.dataTransfer.setData("text", e.target.id);
		objDrag = e.target;
		e.target.style.cursor = 'move';
	}


	// Сдвиг относительно предыдущего объекта
	function f_dragenter(e)
	{
		objOver = get_element(e);
		
		if (objOver && objOver != objDrag) // если найден объект и он не пустой и не совпадает с нашим и это не следующий объект
		{
			if (typeof(objOverLast) != 'undefined'){objOverLast.className = class_name;}	// Переключение не происходит до тех пор, пока не будет найден новый объект.
			objOverLast = objOver;
			objOver.classList.add('drag_drop_over');
		}
	}


	// Находим последний элемент в списке
	function f_ondragleave(e)
	{
		if(objOver.nextElementSibling == null)
		{
			if(typeof(objOverLast) != 'undefined') // переключаем только один раз
			{
				// objOverLast.className = class_name;
				finish = true;
			}
			else {finish = false;}
		}
		else {finish = false;}
	}


	function f_ondragover(e)
	{
		e.preventDefault();
	}


	// получаем элемент на котором отслеживаем клик
	function get_element(e)
	{
		var objParent = e.target || e.srcElement;

		while(objParent)
		{	
			if (objParent.classList.contains(class_name))
			{
				return objParent;
			}
			objParent = objParent.parentNode;
		}
		return false;
	}


	function f_drop(e)
	{
		var objParent = e.target || e.srcElement;

		e.preventDefault();
		var data = e.dataTransfer.getData("text");
		// если есть предыдущий объект - ставим за ним, иначе - в конец

		if (typeof(objOverLast) != 'undefined')
		{		
			if(objOverLast.classList.contains('drag_drop_over')) // есть предыдущий объект
			{
				if(!finish)objNew = drag_target.insertBefore(objDrag,objOverLast);
				else drag_target.appendChild(objDrag);
			}
		}

		drag_start = 0;
	}


	// если не сброшен  f_drop
	function f_dragend(e)
	{
		if (drag_start == 1 && objOver.nextElementSibling == undefined) // если не сработало событие f_drop (вышли за пределы области перетаскивания и наш объект самый последний в контейнере.)
		{
			drag_target.appendChild(objDrag);
			drag_start = 0;
		}

		objDrag.style.cursor = 'default';
		if (typeof(objOverLast) != 'undefined'){objOverLast.className = class_name;}
		delete objOverLast;

		// формирум массив с id разделённых #
		var drag_class = document.getElementsByClassName(class_name);
		var drag_class_length = drag_class.length;

		var data_ordering = '';

		for (var i = 0; i < drag_class_length ; i++)
		{
			var data_id = drag_class[i].getAttribute("data-id");
			data_ordering += data_id + '#';
		}

		// запускаем обработчик порядка следования
		drag_ordering(container_id, class_name, data_ordering);
	}
}