function e_mod_shop(e, obj, id)
{
	var out = '<div class="edit_mode_title">Модуль товаров</div>';

	var obj_a = e_get_a (e, obj);
	
	if (obj_a != false)
	{
		var a = obj_a.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}	
	
	out += '<a href="/admin/modules/shop/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.style.width = '200px';	
	e_menu.innerHTML = out;
}

