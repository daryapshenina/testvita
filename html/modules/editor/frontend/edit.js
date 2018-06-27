function e_mod_editor(e, obj, id)
{	
	var out = '<div class="edit_mode_title">Редактируемый модуль</div>';
	
	var obj_a = e_get_a (e, obj);

	if (obj_a != false)
	{
		var a = obj.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}	
	
	var obj_class = e_mod_editor_get_class(e, obj);
	
	out += '<span onclick="e_mod_editor_quick(' + id + ',\'' + obj_class + '\');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';
	out += '<a href="/admin/modules/editor/' + id + '" class="edit_mode_link e_menu_edit">Редактировать в админке</a>';
	
	e_menu.style.width = '250px';
	e_menu.innerHTML = out;
}


//получаем класс контейнера для того, что бы понять - что мы редактируем - тайтл или контент 
function e_mod_editor_get_class(e, _obj)
{
	var objParent = e.target || e.srcElement;

	while(objParent)
	{
		if (objParent.classList.contains('mod-title')){return 'mod-title';}
		if (objParent.classList.contains('mod-padding') || objParent.classList.contains('edit_mode')){return 'mod-padding';} // 'edit_mode' - проверка на тот случай, если клик не 'mod-title' или 'mod-padding'
		objParent = objParent.parentNode;
	}
	return false;
}


// действие при быстром редактировании
function e_mod_editor_quick(_id,_obj_class)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}
	
	var obj_edit = e_mod_editor_get_obj(_id,_obj_class); // получаем объект на котором отслеживаем клик

	obj_edit.id = 'editable_area';
	obj_edit.contentEditable = 'true';

	// Если основное содержимое - подключаем визуальный редактор
	if(_obj_class == 'mod-padding')
	{
		// Подключаем визуальный редактор
		CKEDITOR.disableAutoInline = true;

		e_.editor = CKEDITOR.inline("editable_area",
		{
			startupFocus: true
		});
		
		e_.editable = true;
		e_.editor_data_old = CKEDITOR.instances.editable_area.getData();		

		
		// При потере фокуса
		e_.editor.on("blur", function() {	
			var type = obj_edit.type;
			var data = CKEDITOR.instances.editable_area.getData();
			e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

			e_mod_editor_save(_id, type, data);
		});			
	}
	
	// Редактируем без визуального редактора
	if(_obj_class == 'mod-title')
	{
		e_.editable = true;
		e_.editor_data_old = obj_edit.innerHTML;		
		obj_edit.focus();
		
		// При потере фокуса	
		obj_edit.onblur = function(){
			var type = obj_edit.type;
			var data = obj_edit.innerHTML;
			e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

			e_mod_editor_save(_id, type, data);			
		}
	}
}


// получаем объект на котором отслеживаем клик
function e_mod_editor_get_obj(_id,_obj_class)
{
	var edit_mode_arr = document.getElementsByClassName("edit_mode");

	for(i=0; i<edit_mode_arr.length; i++)
	{
		if(edit_mode_arr[i].getAttribute('data-id') == _id && edit_mode_arr[i].getAttribute('data-type') == 'mod_editor')
		{
			if(_obj_class == 'mod-title')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod-title')[0];
				obj_edit.type = 'title';
				return obj_edit;				
			}
			else // if(_obj_class == 'mod-content')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod-content')[0];
				obj_edit.type = 'content';
				return obj_edit;				
			}
		}	
	}

	return false;
}

// Сохранение данных
function e_mod_editor_save(_id, _type, _data)
{
	if(_data == e_.editor_data_old) return;

	var e_save_status = document.getElementById("e_save_status");
	var req = getXmlHttp();
	var form = new FormData();
	form.append('id', _id);
	form.append('type', _type);
	form.append('data', _data);

	req.open('POST', '/admin/modules/editor/frontend_update', true);
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