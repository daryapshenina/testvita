function e_mod_authorization(e, obj, id)
{	
	var out = '<div class="edit_mode_title">Модуль авторизации</div>';

	out += '<a href="/admin/modules/authorization/' + id + '" class="edit_mode_link e_menu_edit">Редактировать в админке</a>';

	e_menu.style.width = '250px';	
	e_menu.innerHTML = out;		
}