function e_mod_exchangerates(e, obj, id)
{
	var out = '<div class="edit_mode_title">Курс валют</div>';
	out += '<a href="/admin/modules/exchangerates/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.style.width = '250px';
	e_menu.innerHTML = out;
}

