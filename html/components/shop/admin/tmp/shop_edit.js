DAN_ready(function()
{
	document.getElementById('char_add').onclick = char_add;
	drag_drop("drag_trg", "drag_drop");
		
	var class_name = "drag_drop";
	contextmenu_shop = [["#delete_filter_ajax", "contextmenu_delete", "Удалить"]];
	contextmenu(class_name, contextmenu_shop);
});


/* ------- Действие при смене типа меню ------- */
function menu_type_select(menu_id) 
{
	if (document.getElementById("menu_type_left").selected == true)
	{
		menu_type_ajax("left",menu_id);
	}
	if (document.getElementById("menu_type_top").selected == true)
	{
		menu_type_ajax("top",menu_id);			
	}	 
}


function url_ajax()
{
	sef = document.getElementById("sef").value;
	
	var req = getXmlHttp();
	req.onreadystatechange = function() 
	{
		if (req.readyState == 4) 
		{
			if(req.status == 200) 
			{
				document.getElementById("url_status").innerHTML = req.responseText;
			}
		}
	
	}
	req.open('GET', 'http://' + document.domain + '/administrator/url/ajax.php?sef=' + sef, true);
	req.send(null);
	document.getElementById("url_status").innerHTML = '<div align="left"><img src="http://' + document.domain + '/administrator/tmp/images/loading.gif" /></div>';
}


function char_add()
{
	var content = '<div class="modal_container">';	
	content += '<div class="modal_title">Выберите характеристику, по которой будет фильтроваться поиск:</div>';
	content += select;
	content += '</div>';
	
	DAN_modal('150', '400', '', content);
}


function char_insert()
{
	drag_trg = document.getElementById('drag_trg');

	var sel = document.getElementById('fs');
	var si = sel.selectedIndex; // selectedIndex в select
	var type = sel.options[si].getAttribute("data-type");
	var unit = sel.options[si].getAttribute("data-unit");
	var name_id = sel.options[si].value;	
	var name = sel.options[si].innerHTML;	

	closedit_modeal();
	
	// проверка уже существующей таблицы
	for (var childItem in drag_trg.childNodes) 
	{
		var node = drag_trg.childNodes[childItem];
		
		if (node.tagName == "TABLE" && node.getAttribute("data-name-id") == name_id)
		{
			alert('Характеристика уже добавлена ранее');
			return false;
		}
	}	

	var tab_inner = '<tr>';
	tab_inner += '<td class="filter_dnd"><div class="drag_move" title="Перетащите, что бы изменить порядок следования">&#9016;</div></td>';		
	tab_inner += '<td class="filter_char">' + name + '<input type="hidden" name="name_id[]" value="' + name_id + '"></td>';
	tab_inner += '<td class="filter_unit">' + unit + '</td>';

	if (type == 'number')
	{
		tab_inner += '<td class="filter_type">число</td>';
		tab_inner += '<td><input onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" type="text" name="value_1[]" class="input filer_input_number"> <input onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" type="text" name="value_2[]" class="input filer_input_number"></td>';
	}

	if (type == 'string')
	{
		tab_inner += '<td class="filter_type">строка</td>';	
		tab_inner += '<td><input onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" type="text" name="value_1[]" class="input filer_input_string"> <input type="hidden" name="value_2[]" class="input filer_input_string"></td>';
	}
	
	tab_inner += '</tr>';	

	var tab = document.createElement("table");
	tab.className = "drag_drop";
	tab.setAttribute('draggable', 'true');
	tab.setAttribute('data-name-id', name_id);
	tab.innerHTML = tab_inner;

	var count = document.getElementById('drag_trg').childElementCount;

	//drag_trg.insertBefore(tab, document.getElementById("drag_drop_end"));
	document.getElementById('drag_trg').appendChild(tab);
	
	// инициализируем заново функцию drag_drop - т.к. появился новый узел на котором следует отслеживать событие
	drag_drop("drag_trg", "drag_drop");
	// инициализируем контекстное меню
	contextmenu("drag_drop", contextmenu_shop);
}


function closedit_modeal()
{
	document.body.removeChild(dan_framework_body_lightbox_0);
	document.body.removeChild(dan_framework_lightbox_black);
}


function drag_ordering(container_id, class_name, data_ordering){return false;}


function delete_filter_ajax(objContext)
{
	var id = objContext.getAttribute("data-id");
	
	if (id == '' || id == 'null')
	{
		document.getElementById('drag_trg').removeChild(objContext);		
	} 
	else
	{
		var req = getXmlHttp();
		req.open("POST", "/admin/com/shop/section/delete_filter_ajax", true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send("id=" + id);

		req.onreadystatechange = function()
		{			
			if (req.readyState == 4) 
			{			
				if (req.status == 200)
				{
					document.getElementById('drag_trg').removeChild(objContext);
				}
			}
		}
	}		
}