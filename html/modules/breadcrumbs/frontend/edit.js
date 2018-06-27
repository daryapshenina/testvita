function e_mod_breadcrumbs(id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';
	e_menu.h = '70';
	
	e_menu.style.width = e_menu.w +'px';
	e_menu.style.height = e_menu.h + 'px';
	
	var out = '<div class="edit_mode_title">Путь по сайту</div>';
	out += '<a href="/admin/modules/breadcrumbs/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.innerHTML = out;
}

