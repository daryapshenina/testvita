function e_com_page(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';
	
	var out = '<div class="edit_mode_title">Содержимое страницы</div>';
	
	var obj_a = e_get_a (e, obj);
	
	if (obj_a != false)
	{
		var a = obj.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}

	out += '<a href="/admin/com/page/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.style.width = e_menu.w +'px';
	
	e_menu.innerHTML = out;	
}