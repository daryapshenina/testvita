function e_com_photo(e, obj, id)
{
	var z = e_com_photo_get_class(e, obj);
	
	var out_optional = '';
	if(z)
	{
		var obj_class = z[0];
		var obj_param = z[1];
		
		if (obj_class == 'photo_title'){out_optional = '<span onclick="e_com_photo_title_quick(' + id + ');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';}
		if (obj_class == 'photo_text_top'){out_optional = '<span onclick="e_com_photo_text_quick(' + id + ', \'text_top\');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';}
		if (obj_class == 'photo_text_bottom'){out_optional = '<span onclick="e_com_photo_text_quick(' + id + ', \'text_bottom\');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';}
		if (obj_class == 'photo_item')
		{
			out_optional = '<a href="/admin/com/photo/item/edit/' + obj_param + '" class="edit_mode_link e_menu_edit">Редактировать изображение</a>';
			out_optional += '<a href="/admin/com/photo/item/delete/' + obj_param + '/frontend" class="edit_mode_link e_menu_delete">Удалить изображение</a>';
		}
	}	

	var out = '<div class="edit_mode_title">Редактировать фотогалерею</div>';
	out += '<a href="/admin/com/photo/section/edit/' + id + '" class="edit_mode_link e_menu_edit">Редактировать раздел</a>';
	out += out_optional;
	out += '<a href="/admin/com/photo/item/add/' + id + '" class="edit_mode_link e_menu_add">Добавить изображение</a>';	
	out += '<a href="/admin/com/photo/settings/' + id + '" class="edit_mode_link e_menu_settings">Настройки фотогалереи</a>';

	e_menu.w = '250';// эти значения нужны для расчётов - не стирать!	
	e_menu.style.width = e_menu.w +'px';
	e_menu.innerHTML = out;
}


// Получаем класс контейнера для того, что бы понять - что мы редактируем
function e_com_photo_get_class(e, _obj)
{
	var objParent = e.target || e.srcElement;

	while(objParent)
	{
		if (objParent.classList.contains('photo_title')){return Array('photo_title');}
		if (objParent.classList.contains('photo_text_top')){return Array('photo_text_top');}
		if (objParent.classList.contains('photo_text_bottom')){return Array('photo_text_bottom');}
		if (objParent.classList.contains('photo_item')){return Array('photo_item', objParent.getAttribute('data-id'));}
		if (objParent.classList.contains('photo_container')){return false;}
		objParent = objParent.parentNode;
	}
	return false;
}


// Быстрое редактирование заголовка раздела
function e_com_photo_title_quick(_id)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}
	
	var obj_edit = document.getElementsByClassName('photo_title')[0];

	obj_edit.id = 'editable_area';
	obj_edit.contentEditable = 'true';	

	e_.editable = true;
	e_.editor_data_old = obj_edit.innerHTML;		
	obj_edit.focus();
	
	// При потере фокуса	
	obj_edit.onblur = function()
	{
		var type = 'title';
		var data = obj_edit.innerHTML;
		e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

		e_com_photo_save(_id, type, data);			
	}	
}


// Быстрое редактирование верхнего / нижнего текста раздела
function e_com_photo_text_quick(_id, _type)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}
	
	var obj_edit = document.getElementsByClassName('photo_' + _type)[0];
	obj_edit.id = 'editable_area';
	obj_edit.contentEditable = 'true';

	// Подключаем визуальный редактор
	CKEDITOR.disableAutoInline = true;

	e_.editor = CKEDITOR.inline("editable_area",{startupFocus: true});	
	e_.editable = true;
	e_.editor_data_old = CKEDITOR.instances.editable_area.getData();
	
	// При потере фокуса	
	obj_edit.onblur = function()
	{
		var data = obj_edit.innerHTML;
		e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

		e_com_photo_save(_id, _type, data);			
	}	
}


// Сохранение данных
function e_com_photo_save(_id, _type, _data)
{
	if(_data == e_.editor_data_old) return;

	var e_save_status = document.getElementById("e_save_status");
	var req = getXmlHttp();
	var form = new FormData();
	form.append('id', _id);
	form.append('type', _type);
	form.append('data', _data);

	req.open('POST', 'http://'+document.domain+'/admin/com/photo/frontend_update', true);
	req.send(form);
	req.onreadystatechange = function()
	{
		if(req.readyState == 4 && req.status == 200)
		{
			answer = req.responseText;
			if(answer == 'ok')
			{
				e_save_status.className = 'e_save_ok';
				setTimeout(function(){e_save_status.className = 'e_save_default';}, 1000);
			}
		}
	}
}