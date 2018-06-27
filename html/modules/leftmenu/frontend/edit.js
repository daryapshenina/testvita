function e_mod_leftmenu(e, obj, id)
{
	var out = '<div class="edit_mode_title">Вертикальное меню</div>';
	
	var obj_a = e_get_a (e, obj);
	
	if (obj_a != false)
	{
		var a = obj.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}	
	
	out += '<a href="/admin/modules/leftmenu/' + id + '" class="edit_mode_link e_menu_edit">Редактировать меню</a>';
	
	e_menu.style.width = '200px';
	e_menu.innerHTML = out;
}