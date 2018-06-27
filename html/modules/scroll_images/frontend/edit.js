function e_mod_scroll_images(e, obj, id)
{
	var out = '<div class="edit_mode_title">Скроллер</div>';
	out += '<a href="/admin/modules/scroll_images/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.style.width = '200px';	
	e_menu.innerHTML = out;
}

