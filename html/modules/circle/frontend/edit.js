function e_mod_circle(e, obj, id)
{	
	var out = '<div class="edit_mode_title">Круг</div>';

	var z = e_mod_circle_get_class(e, obj);	
	var obj_a = e_get_a (e, obj);

	if (obj_a != false)
	{
		var a = obj_a.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}	
	
	if(z)
	{
		var obj_class = z[0];
		var obj_param = z[1];
		if(obj_class == 'mod_circle_text_2'){out += '<span onclick="e_mod_circle_quick(' + id + ',\'' + obj_class + '\');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';}
	}

	out += '<a href="/admin/modules/circle/' + id + '" class="edit_mode_link e_menu_edit">Редактировать в админке</a>';
	out += '<a href="/admin/modules/copy/' + id + '/frontend" class="edit_mode_link e_menu_copy">Копировать</a>';
	out += '<a href="/admin/modules/delete/' + id + '/frontend" class="edit_mode_link e_menu_delete">Удалить</a>';	

	e_menu.style.width = '250px';	
	e_menu.innerHTML = out;		
}


// Получаем класс контейнера для того, что бы понять - что мы редактируем
function e_mod_circle_get_class(e, _obj)
{
	var objParent = e.target || e.srcElement;

	while(objParent)
	{
		if (objParent.classList.contains('mod_circle_wrap')){return Array('mod_circle_wrap');}
		if (objParent.classList.contains('mod_circle_text_2')){return Array('mod_circle_text_2');}
		if (objParent.classList.contains('mod_circle_container')){return false;}
		objParent = objParent.parentNode;
	}
	return false;
}


// Действие при быстром редактировании
function e_mod_circle_quick(_id, _obj_class)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}
	
	var obj_edit = e_mod_circle_get_obj(_id,_obj_class); // получаем объект на котором отслеживаем клик

	// Если текст - подключаем визуальный редактор
	if(_obj_class == 'mod_circle_text_2')
	{
		obj_edit.id = 'editable_area';
		obj_edit.contentEditable = 'true';

		// Подключаем визуальный редактор
		CKEDITOR.disableAutoInline = true;

		e_.editor = CKEDITOR.inline("editable_area",{startupFocus: true});	
		e_.editable = true;
		e_.editor_data_old = CKEDITOR.instances.editable_area.getData();
		
		// При потере фокуса
		e_.editor.on("blur", function() {	
			var type = obj_edit.e_type;
			var data = CKEDITOR.instances.editable_area.getData();
			e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

			e_mod_circle_save(_id, type, data);
		});			
	}
}


// получаем объект на котором отслеживаем клик
function e_mod_circle_get_obj(_id, _obj_class, _param = '')
{
	var edit_mode_arr = document.getElementsByClassName("edit_mode");

	for(i=0; i<edit_mode_arr.length; i++)
	{
		if(edit_mode_arr[i].getAttribute('data-id') == _id && edit_mode_arr[i].getAttribute('data-type') == 'mod_circle')
		{
			if(_obj_class == 'mod_circle_text_2')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_circle_text_2')[0];
				obj_edit.e_type = 'text_2';
				return obj_edit;				
			}		
		}	
	}

	return false;
}


// Сохранение данных
function e_mod_circle_save(_id, _type, _data)
{
	if(_data == e_.editor_data_old) return;

	var e_save_status = document.getElementById("e_save_status");
	var req = getXmlHttp();
	var form = new FormData();
	form.append('id', _id);
	form.append('type', _type);
	form.append('data', _data);

	req.open('POST', '/admin/modules/circle/frontend_update', true);
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