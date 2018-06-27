function e_mod_form(e, obj, id)
{
	var out = '<div class="edit_mode_title">Форма обратной связи</div>';

	var z = e_mod_form_get_class(e, obj);

	if(z)
	{
		var obj_class = z[0];
		var obj_param = z[1];

		out += '<span onclick="e_mod_form_quick(' + id + ',\'' + obj_class + '\',' + ' \'' + obj_param + '\');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';
	}

	out += '<a href="/admin/modules/form/' + id + '" class="edit_mode_link e_menu_edit">Редактировать в админке</a>';

	e_menu.style.width = '250px';
	e_menu.innerHTML = out;
}


// Получаем класс контейнера для того, что бы понять - что мы редактируем
function e_mod_form_get_class(e, _obj, _this)
{
	var objParent = e.target || e.srcElement;

	while(objParent)
	{
		if (objParent.classList.contains('mod_title')){return Array('mod_title', '');}
		if (objParent.classList.contains('mod_form_text')){return Array('mod_form_text', '');}
		if (objParent.classList.contains('mod_form_text_2')){return Array('mod_form_text_2', '');}
		if (objParent.classList.contains('mod_form_input')){return Array('mod_form_input', objParent.name);}
		if (objParent.classList.contains('mod_form_input_2')){return Array('mod_form_input_2', objParent.name);}
		if (objParent.classList.contains('mod_form_textarea')){return Array('mod_form_textarea', '');}
		if (objParent.classList.contains('mod_form_textarea_2')){return Array('mod_form_textarea_2', '');}
		if (objParent.classList.contains('mod_form_main') || objParent.classList.contains('mod_form_main_2') || objParent.classList.contains('mod_form_main_3')){return false;}
		objParent = objParent.parentNode;
	}
	return false;
}


// Действие при быстром редактировании
function e_mod_form_quick(_id, _obj_class, _param)
{
	// Удаляем меню редактирования
	node = document.getElementById('e_menu');
	if (node){document.body.removeChild(node);}

	var obj_edit = e_mod_form_get_obj(_id,_obj_class, _param); // получаем объект на котором отслеживаем клик

	// Редактируем без визуального редактора
	if(_obj_class == 'mod_title')
	{
		obj_edit.id = 'editable_area';
		obj_edit.contentEditable = 'true';

		e_.editable = true;
		e_.editor_data_old = obj_edit.innerHTML;
		obj_edit.focus();

		// При потере фокуса
		obj_edit.onblur = function(){
			var type = obj_edit.e_type;
			var data = obj_edit.innerHTML;
			e_editor_destroy(); // уничтожаем редактор и снимаем все признаки редактирования

			e_mod_form_save(_id, type, data);
		}
	}


	// Если текст - подключаем визуальный редактор
	if(_obj_class == 'mod_form_text' || _obj_class == 'mod_form_text_2')
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

			e_mod_form_save(_id, type, data);
		});
	}


	if(_obj_class == 'mod_form_input' || _obj_class == 'mod_form_input_2')
	{

		e_.editor_data_old = obj_edit.placeholder;
		obj_edit.value = obj_edit.placeholder;
		obj_edit.focus();

		obj_edit.onblur = function()
		{
			var type = obj_edit.e_type;
			var data = obj_edit.value;
			e_editor_destroy();

			obj_edit.placeholder = data;
			obj_edit.value = '';
			e_mod_form_save(_id, type, data);
			obj_edit.onblur = null;
		}
	}


	if(_obj_class == 'mod_form_textarea' || _obj_class == 'mod_form_textarea_3')
	{

		e_.editor_data_old = obj_edit.placeholder;
		obj_edit.focus();
		obj_edit.value = obj_edit.placeholder;

		obj_edit.onblur = function()
		{
			var type = obj_edit.e_type;
			var data = obj_edit.value;
			e_editor_destroy();

			obj_edit.placeholder = data;
			obj_edit.value = '';

			e_mod_form_save(_id, type, data);
			obj_edit.onblur = null;
		}
	}
}


// получаем объект на котором отслеживаем клик
function e_mod_form_get_obj(_id, _obj_class, _param)
{
	var edit_mode_arr = document.getElementsByClassName("edit_mode");

	for(i=0; i<edit_mode_arr.length; i++)
	{
		if(edit_mode_arr[i].getAttribute('data-id') == _id && edit_mode_arr[i].getAttribute('data-type') == 'mod_form')
		{
			if(_obj_class == 'mod_title')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_title')[0];
				obj_edit.e_type = 'title';
				return obj_edit;
			}


			if(_obj_class == 'mod_form_text')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_text')[0];
				obj_edit.e_type = 'content';
				return obj_edit;
			}

			if(_obj_class == 'mod_form_text_2')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_text_2')[0];
				obj_edit.e_type = 'content';
				return obj_edit;
			}


			if(_obj_class == 'mod_form_input')
			{
				if(_param == 'field_1')
				{
					var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_input')[0];
					obj_edit.e_type = 'field_1';
					return obj_edit;
				}

				if(_param == 'field_3')
				{
					var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_input')[1];
					obj_edit.e_type = 'field_3';
					return obj_edit;
				}

			}

			if(_obj_class == 'mod_form_input_2')
			{
				if(_param == 'field_1')
				{
					var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_input_2')[0];
					obj_edit.e_type = 'field_1';
					return obj_edit;
				}

				if(_param == 'field_3')
				{
					var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_input_2')[1];
					obj_edit.e_type = 'field_3';
					return obj_edit;
				}

			}

			if(_obj_class == 'mod_form_textarea')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_textarea')[0];
				obj_edit.e_type = 'field_2';
				return obj_edit;
			}

			if(_obj_class == 'mod_form_textarea_2')
			{
				var obj_edit = edit_mode_arr[i].getElementsByClassName('mod_form_textarea_2')[0];
				obj_edit.e_type = 'field_2';
				return obj_edit;
			}
		}
	}

	return false;
}


// Сохранение данных
function e_mod_form_save(_id, _type, _data)
{
	if(_data == e_.editor_data_old) return;

	var e_save_status = document.getElementById("e_save_status");
	var req = getXmlHttp();
	var form = new FormData();
	form.append('id', _id);
	form.append('type', _type);
	form.append('data', _data);

	req.open('POST', '/admin/modules/form/frontend_update', true);
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