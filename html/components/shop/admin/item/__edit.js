DAN_ready(function()
{
	drag_drop("h");
	//document.ondragstart = e_drag;
	//document.ondragenter = e_dragenter;
	//document.getElementById('drag_trg').ondrop = e_drop;	
	//document.getElementById('drag_trg').ondragover = e_ondragover;
	document.oncontextmenu = e_contextmenu;
	document.onclick = e_contextmenu_action;
	document.getElementById('discount').onclick = e_discount;
	e_discount();	

	// получаем элемент на котором отслеживаем клик
	function get_element(e)
	{
		var objParent = e.target || e.srcElement;

		while(objParent)
		{
			if (objParent.className == "drag_drop" || objParent.className == "drag_last_element")
			{
				return objParent;
			}
			objParent = objParent.offsetParent;
		}
		return false;
	}


	function e_drag(e) 
	{
		e.dataTransfer.setData("text", e.target.id);
		objDrag = e.target;
		objDrag.style.opacity = 0.5;
	}

	
	// Сдвиг относительно предыдущего объекта 
	function e_dragenter(e)
	{
		objOver = get_element(e);

		if (objOver && objOver != objDrag) // если найден объект
		{
			if(typeof(objOverLast) != 'undefined'){objOverLast.style.marginLeft = '10px';}
			objOverLast = objOver;
			objOver.style.marginLeft = '150px';	
		}
	}

	
	function e_drop(e) 
	{
		var objParent = e.target || e.srcElement;

		e.preventDefault();
		var data = e.dataTransfer.getData("text");

		objNew = document.getElementById('drag_trg').insertBefore(objDrag,objOverLast);
		objDrag.style.opacity = 1;
		objOverLast.style.marginLeft = '10px';
		
		// запускаем обработчик порядка следования изображений
		images_order();
	}

	
	function e_ondragover(e) 
	{
		e.preventDefault();
	}

	
	function ordering_ajax(e) 
	{
		var input_arr = '';	
		var dt_node = document.getElementById('drag_trg');
		for (var childItem in dt_node.childNodes) 
		{
			var img = dt_node.childNodes[childItem];
			if (img.className == "drag_drop" && img.id != 'img_zero')
			{
				img_name = fun_img_name(img);
				input_arr = input_arr + img_name + ';';
			}
		}
		
		document.getElementById('images_order').value = input_arr;	
	}
	
	

	function e_contextmenu(e)
	{
		objContext = get_element(e);
		img_name = fun_img_name(objContext);

		// защита от повторного контекстного окна
		if (document.getElementById('contextmenu') == null)
		{
			// отмена действий браузера - кроссбраузерный код
			event = e || window.e;
			
			var c_menu = document.createElement('a');
			c_menu.id = 'contextmenu';
			c_menu.setAttribute('data-image', img_name);
			
			var body_child_0 = document.body.children[0];
			document.body.insertBefore(c_menu, body_child_0);
		
			c_menu.style.top = (event.pageY - 10) + 'px';
			c_menu.style.left = (event.pageX - 10) +'px';
			
			c_menu.innerHTML = 'Удалить';
			
			if (event.preventDefault) {event.preventDefault();} 
			else {event.returnValue = false;} // вариант IE<9:
		}
	}
	
	
	// выделяет имя из пути src
	function fun_img_name(img)
	{
		img_arr = img.src.split('/');
		img_name = img_arr[img_arr.length-1];
		return img_name;
	}


	function e_contextmenu_action(e)
	{
		node = document.getElementById('contextmenu');
		
		var objCnt = e.target || e.srcElement;
		
		if (node)
		{
			if (node == objCnt)
			{
				var img_name = objCnt.getAttribute("data-image");
				var imd_order = document.getElementById('images_order').value;
				document.getElementById('images_order').value = imd_order.replace(img_name + ';', '');
				img_delete_ajax(objContext,img_name);
			}
			
			document.body.removeChild(node);
		}
	}
	
	
	function img_delete_ajax(objContext,img_name)
	{
		var req = getXmlHttp();  
		req.open("POST", "http://" + document.domain + "/admin/com/shop/img_delete_ajax", true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		var id = document.getElementById("drag_trg").getAttribute("data-id");
		req.send("id=" + id + "&img_name=" + img_name);

		req.onreadystatechange = function()
		{			
			if (req.readyState == 4) 
			{			
				if (req.status == 200)
				{
					// console.log(objContext + ' ----- ' + img_name);
					document.getElementById('drag_trg').removeChild(objContext);
					document.getElementById("img_status").innerHTML = req.responseText;
				}
			}
		}
	}


	function e_discount()
	{
		var po = document.getElementById('price_old_display');
		
		if (document.getElementById('discount').checked == true)
		{
			po.style.display = 'inline';
		}
		else
		{
			po.style.display = 'none';		
		}
	}
});



