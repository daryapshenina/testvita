DAN_ready(function()
{
	document.getElementById('discount').onclick = e_discount;
	e_discount();
	document.getElementById('char_add').onclick = char_add;	

	drag_drop("drag_trg", "drag_drop");	// для изображений
	drag_drop("char_list", "char_tab");	// для характеристик
	drag_drop("related_items", "related_item");	// для сопутствующих товаров	

	var class_name_photo = "drag_drop";
	contextmenu_item_photo = [["#img_delete_ajax", "contextmenu_delete", "Удалить"]];
	contextmenu(class_name_photo, contextmenu_item_photo);

	var class_name = "char_tab";
	contextmenu_item = [["#char_delete_ajax", "contextmenu_delete", "Удалить"]];
	contextmenu(class_name, contextmenu_item);

	var class_name = "related_item";
	contextmenu_item_related = [["#related_delete_ajax", "contextmenu_delete", "Удалить"]];
	contextmenu(class_name, contextmenu_item_related);	
		
	
	
	function get_char_list(section_id)
	{
		req.open("POST", "/admin/com/shop/item/chars", true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send("section_select="+section_id);

		req.onreadystatechange = function()
		{
			if (req.readyState == 4) 
			{
				if (req.status == 200) 
				{
					// Вставляем список характеристик
					// document.getElementById("char_list").innerHTML = req.responseText;
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


function char_add()
{
	var content = '<div class="modal_container">';	
	content += '<div class="modal_title">Выберите характеристику, по которой будет фильтроваться поиск:</div>';
	content += select;
	content += '</div>';
	
	DAN_modal('150', '400', '', content);
}


function item_list(_num)
{
	var content = '<div id="related_items_list"></div>';		
	DAN_modal('515', '750', '', content);	
	
	var req = getXmlHttp()  
	req.onreadystatechange = function() 
	{
		if (req.readyState == 4) 
		{
			if(req.status == 200) 
			{
				document.getElementById("related_items_list").innerHTML = req.responseText;
			}
		}
	
	}
	req.open('GET', '/admin/com/shop/item/related_list/' + _num, true);
	req.send(null);
	document.getElementById("related_items_list").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
}





/*
var i = 0;
function char_add_1()
{
	i++;
	var newDiv = document.createElement('table');
	newDiv.className = "admin_table_2_context";
	newDiv.setAttribute('data-id', '');
	newDiv.innerHTML = '<tr><td style="width:42px;">' + i + '</td><td style="width:292px;">' + chars_add_out + '</td><td style="width:92px;">Тип</td><td style="width:292px;"><input class="input" type="text" name="char_value_add[]"></td><td>&nbsp;</td></tr>';	
	document.getElementById("char_list").appendChild(newDiv);
	
	// инициализируем контекстное меню
	contextmenu("admin_table_2_context", contextmenu_item);		
}
*/


function char_insert()
{
	char_list = document.getElementById('char_list');

	var sel = document.getElementById('fs');
	var si = sel.selectedIndex; // selectedIndex в select
	var type = sel.options[si].getAttribute("data-type");
	var unit = sel.options[si].getAttribute("data-unit");
	var name_id = sel.options[si].value;	
	var name = sel.options[si].innerHTML;	

	closedit_modal();
	
	/*
	// проверка уже существующей таблицы
	for (var childItem in char_list.childNodes) 
	{
		var node = char_list.childNodes[childItem];
		
		if (node.tagName == "TABLE" && node.getAttribute("data-name-id") == name_id)
		{
			alert('Характеристика уже добавлена ранее');
			return false;
		}
	}
	*/

	var tab_inner = '<tr>';
	tab_inner += '<td class="char_dnd"><div class="char_move" title="Перетащите, что бы изменить порядок следования">&#9016;</div></td>';		
	tab_inner += '<td class="char_name">' + name + '<input type="hidden" name="char_id[]" value=""><input type="hidden" name="char_name_id[]" value="' + name_id + '"></td>';
	tab_inner += '<td class="char_unit">' + unit + '</td>';
	
	if (type == 'number')
	{
		tab_inner += '<td class="char_type">число</td>';
		tab_inner += '<td class="char_value"><input draggable="false" onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" class="input char_input_number" type="text" name="char_value[]"></td>';
	}

	if (type == 'string')
	{
		tab_inner += '<td class="char_type">строка</td>';	
		tab_inner += '<td><input draggable="false" onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" class="input char_input_string" type="text" name="char_value[]"></td>';
	}
	
	tab_inner += '<td>&nbsp;</td>';	
	tab_inner += '</tr>';	

	var tab = document.createElement("table");
	tab.className = "char_tab";
	tab.setAttribute('draggable', 'true');
	tab.setAttribute('data-id', '');
	tab.innerHTML = tab_inner;

	//char_list.insertBefore(tab, document.getElementById("drag_drop_end"));
	document.getElementById('char_list').appendChild(tab);
	
	// инициализируем заново функцию drag_drop - т.к. появился новый узел на котором следует отслеживать событие
	drag_drop("char_list", "char_tab");
	// инициализируем контекстное меню
	contextmenu("char_tab", contextmenu_item);
}


function closedit_modal()
{
	document.body.removeChild(dan_framework_body_lightbox_0);
	document.body.removeChild(dan_framework_lightbox_black);
}


function drag_ordering(container_id, class_name)
{
	if (container_id == 'drag_trg')
	{
		var input_arr = '';	
		var dt_node = document.getElementById(container_id);
		for (var childItem in dt_node.childNodes) 
		{	
			var img = dt_node.childNodes[childItem];
			if (img.className == class_name)
			{
				img_name = fun_img_name(img);
				input_arr = input_arr + img_name + ';';
			}
		}
		
		document.getElementById('images_order').value = input_arr;		
	}
	else if(container_id == 'related_items')
	{
		var related_arr = '';
		var related_node = document.getElementById(container_id);	
		for (var childItem in related_node.childNodes) 
		{	
			var r_item = related_node.childNodes[childItem];
			if (r_item.className == class_name)
			{
				var id = r_item.getAttribute("data-id");
				related_arr +=  id + ';';
			}
		}
		document.getElementById('related_order').value = related_arr;	
	}
	else {return false;}
}


// выделяет имя из пути src
function fun_img_name(img)
{
	img_arr = img.src.split('/');
	img_name = img_arr[img_arr.length-1];
	return img_name;
}


function char_delete_ajax(objContext)
{
	var id = objContext.getAttribute("data-id");

	if (id != '' || id != 'null')
	{
		req.open("POST", "/admin/com/shop/item/delete_char_ajax", true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send("id=" + id);

		req.onreadystatechange = function()
		{			
			if (req.readyState == 4) 
			{			
				if (req.status == 200){}
			}
		}
	}

	document.getElementById('char_list').removeChild(objContext);
}

	
function img_delete_ajax(objContext)
{
	var id = document.getElementById('drag_trg').getAttribute("data-id");
	
	img_name = fun_img_name(objContext);
	
	req.open("POST", "/admin/com/shop/img_delete_ajax", true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
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
				drag_ordering('drag_trg', 'drag_drop'); // формируем порядок следования
			}
		}
	}
}


function related_delete_ajax(objContext)
{
	var id = objContext.getAttribute("data-id");

	if (id != '' || id != 'null')
	{
		req.open("POST", "/admin/com/shop/item/related_delete", true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send("id=" + id);

		req.onreadystatechange = function()
		{			
			if (req.readyState == 4) 
			{			
				if (req.status == 200){}
			}
		}
	}

	document.getElementById('related_items').removeChild(objContext);
}

// Постраничная навигация в листе сопутствующих товаров
function related_pagination(_num)
{
	console.log(_num);
	fetch('/article/fetch/user.json')
}