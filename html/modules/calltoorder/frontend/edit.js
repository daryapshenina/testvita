function e_mod_calltoorder(e, obj, id)
{
	var out = '<div class="edit_mode_title">заказать звонок</div>';
	out += '<a href="/admin/modules/calltoorder/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';

	e_menu.style.width = '200px';	
	e_menu.innerHTML = out;
}

