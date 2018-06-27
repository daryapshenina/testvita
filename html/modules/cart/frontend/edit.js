function e_mod_cart(e, obj, id)
{
	var out = '<div class="edit_mode_title">Корзина</div>';
	out += '<a href="/admin/modules/cart/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.style.width = e_menu.w +'px';	
	e_menu.innerHTML = out;
}

