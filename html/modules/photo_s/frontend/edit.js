function e_mod_photo_s(e, obj, id)
{	
	var out = '<div class="edit_mode_title">Изображение с описанием</div>';
	var obj_a = e_get_a (e, obj);
console.log(obj_a);
	if (obj_a != false)
	{
		var a = obj_a.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';
	}
	
	out += '<a href="/admin/modules/photo_s/' + id + '" class="edit_mode_link e_menu_edit">Редактировать в админке</a>';
	out += '<a href="/admin/modules/copy/' + id + '/frontend" class="edit_mode_link e_menu_copy">Копировать</a>';
	out += '<a href="/admin/modules/delete/' + id + '/frontend" class="edit_mode_link e_menu_delete">Удалить</a>';	

	e_menu.style.width = '250px';	
	e_menu.innerHTML = out;		
}