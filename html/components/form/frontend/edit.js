function e_com_form(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';

	var out = '<div class="edit_mode_title">Форма обратной связи</div>';
	out += '<a href="/admin/com/form" class="edit_mode_link">Редактировать</a>';

	e_menu.style.width = e_menu.w +'px';	
	e_menu.innerHTML = out;
}

