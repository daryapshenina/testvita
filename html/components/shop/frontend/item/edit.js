function e_com_shop_item(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '220';
	
	var obj_a = e_get_a (e, obj);
	var z = e_com_shop_item_get_class(e, obj);		

	var out = '<div class="edit_mode_title">Товар</div>';	
	
	if (obj_a != false)
	{
		var a = obj.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}
	
	if(z)
	{
		var obj_class = z[0];
		var obj_param = z[1];
		if(obj_class == 'title' || obj_class == 'item_price' || obj_class == 'item_price_old' || obj_class == 'item_price_discount' ){var edit_type  = 'noeditor';}
		if(obj_class == 'item_intro_text' || obj_class == 'item_full_text'){var edit_type  = 'editor';}
		out += '<span onclick="e_com_shop_item_edit_' + edit_type + '(' + id + ',\'' + obj_class + '\');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';		
	}	

	out += '<a href="/admin/com/shop/item/edit/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	out += '<a href="/admin/com/shop/item/copy/' + id + '/frontend" class="edit_mode_link e_menu_edit">Копировать</a>';	
	out += '<a href="/admin/com/shop/item/unpub/' + id + '/frontend" class="edit_mode_link e_menu_unpub">Скрыть товар</a>';	
	out += '<a href="/admin/com/shop/item/delete/' + id + '/frontend" class="edit_mode_link e_menu_delete">Удалить товар</a>';
	
	e_menu.style.width = e_menu.w +'px';
	e_menu.innerHTML = out;
}


// Получаем класс контейнера для того, что бы понять - что мы редактируем
function e_com_shop_item_get_class(e, _obj)
{
	var objParent = e.target || e.srcElement;

	while(objParent)
	{
		if (objParent.classList.contains('title')){return Array('title');}
		if (objParent.classList.contains('item_price')){return Array('item_price');}
		if (objParent.classList.contains('item_price_old')){return Array('item_price_old');}
		if (objParent.classList.contains('item_price_discount')){return Array('item_price_discount');}
		if (objParent.classList.contains('item_intro_text')){return Array('item_intro_text');}
		if (objParent.classList.contains('item_full_text')){return Array('item_full_text');}			
		if (objParent.classList.contains('shop_item_container')){return false;}
		objParent = objParent.parentNode;
	}
	return false;
}


function e_com_shop_item_edit_noeditor (_id, _obj_class)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}

	var obj_edit_arr = document.getElementsByClassName(_obj_class);
	var obj_edit = obj_edit_arr[0];	

	obj_edit.id = 'editable_area';
	obj_edit.contentEditable = 'true';
	
	e_.editable = true;
	e_.editor_data_old = obj_edit.innerHTML;		
	obj_edit.focus();
	
	// При потере фокуса	
	obj_edit.onblur = function(){
		if(_obj_class == 'title'){var type = 'item_title';}
		else{var type = _obj_class;}
		
		var data = obj_edit.innerHTML;
		e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

		e_com_shop_item_save(_id, type, data);			
	}
}


function e_com_shop_item_edit_editor (_id, _obj_class)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}

	var obj_edit_arr = document.getElementsByClassName(_obj_class);
	var obj_edit = obj_edit_arr[0];	

	obj_edit.id = 'editable_area';
	obj_edit.contentEditable = 'true';
	
	// Подключаем визуальный редактор
	CKEDITOR.disableAutoInline = true;

	e_.editor = CKEDITOR.inline("editable_area",{startupFocus: true});	
	e_.editable = true;
	e_.editor_data_old = CKEDITOR.instances.editable_area.getData();
	
	// При потере фокуса
	e_.editor.on("blur", function() {	
		var type = _obj_class;
		var data = CKEDITOR.instances.editable_area.getData();
		e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

		e_com_shop_item_save(_id, type, data);
	});
}


function e_com_shop_item_save(_id, _type, _data)
{
	if(_data == e_.editor_data_old) return;

	var e_save_status = document.getElementById("e_save_status");
	var req = getXmlHttp();
	var form = new FormData();
	form.append('id', _id);
	form.append('type', _type);
	form.append('data', _data);

	req.open('POST', '/admin/com/shop/frontend_update', true);
	req.send(form);
	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{	
			answer = req.responseText;
console.log(answer);
			if(answer == 'ok')
			{
				e_save_status.className = 'e_save_ok';
				setTimeout(function(){e_save_status.className = 'e_save_default';}, 1000);
			}
		}
	}
}