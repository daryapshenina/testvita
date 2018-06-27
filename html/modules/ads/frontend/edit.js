function e_mod_ads(e, obj, id)
{
	EDIT.obj = obj;

	var out = '<div class="edit_mode_title">Модуль объявлений</div>';
	var obj_a = e_get_a (e, obj);

	if(obj.className == 'mod_title')
	{
		out += '<span onclick="e_mod_ads_title(' + id + ');" class="edit_mode_link e_menu_edit">Редактировать быстро</span>';
	}
	
	if (obj_a != false)
	{
		var a = obj_a.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';
	}
	
	out += '<a href="/admin/modules/ads/' + id + '" class="edit_mode_link e_menu_edit">Редактировать модуль</a>';
	out += '<a href="/admin/modules/copy/' + id + '/frontend" class="edit_mode_link e_menu_copy">Копировать</a>';
	out += '<a href="/admin/modules/delete/' + id + '/frontend" class="edit_mode_link e_menu_delete">Удалить</a>';	

	e_menu.style.width = '250px';	
	e_menu.innerHTML = out;		
}

// Тайтл объявления
function e_mod_ads_title(_id)
{
	// Удаляем меню редактирования		
	node = document.getElementById('e_menu');		
	if (node){document.body.removeChild(node);}

	EDIT.obj.id = 'editable_area';
	EDIT.obj.contentEditable = 'true';

	EDIT.obj.data_old = EDIT.obj.innerHTML;

	EDIT.obj.focus();

	// Выделение
	var range = document.createRange(); // Метод только у document
	range.setStart(EDIT.obj, 1);
	range.setEnd(EDIT.obj, 1);

	var s = window.getSelection();
	if(s.rangeCount > 0) s.removeAllRanges();
	
	//range.selectNode(EDIT.obj);
	s.addRange(range);

	// При потере фокуса	
	EDIT.obj.onblur = function(){
		var data = EDIT.obj.innerHTML;
		if(data != EDIT.obj.data_old) e_mod_ads_save(_id, 'mod_title', data);
		EDIT.destroy();
	}
}


// Сохранение данных
function e_mod_ads_save(_id, _type, _data)
{
	var e_save_status = document.getElementById("e_save_status");
	var req = getXmlHttp();
	var form = new FormData();
	form.append('id', _id);
	form.append('type', _type);
	form.append('data', _data);

	req.open('POST', '/admin/modules/ads/frontend_update', true);
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