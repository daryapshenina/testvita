function e_mod_jumptotop(e, obj, id)
{
	var out = '<div class="edit_mode_title">Перейти к началу сайта</div>';
	out += '<a href="/admin/modules/jumptotop" class="edit_mode_link e_menu_edit">Редактировать</a>';

	e_menu.style.width = '200px';
	e_menu.innerHTML = out;
}

