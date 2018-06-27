function e_mod_route(e, obj, id)
{
	var out = '<div class="edit_mode_title">Модуль "Поиск маршрута"</div>';
	out += '<a href="/admin/modules/route/' + id + '" class="edit_mode_link e_menu_edit">Редактировать в админке</a>';
	e_menu.style.width = '250px';
	e_menu.innerHTML = out;
}
