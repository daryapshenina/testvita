function e_com_article_section(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';
	
	e_menu.style.width = e_menu.w +'px';
	
	var out = '<div class="edit_mode_title">Статьи / новости (раздел)</div>';
	out += '<a href="/admin/com/article/sectionedit/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	
	e_menu.style.width = e_menu.w +'px';	
	e_menu.innerHTML = out;
}

function e_com_article_item(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';

	var out = '<div class="edit_mode_title">Статья</div>';

	var obj_a = e_get_a (e, obj);
	
	if (obj_a != false)
	{
		var a = obj.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Перейти по ссылке</a>';		
	}
	
	out += '<a href="/admin/com/article/itemedit/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	out += '<a href="/admin/com/article/itemadd" class="edit_mode_link e_menu_add">Добавить статью</a>';
	out += '<a href="/admin/com/article/itemunpub/' + id + '" class="edit_mode_link e_menu_unpub">Скрыть статью</a>';	
	out += '<a href="/admin/com/article/itemdelete/' + id + '" class="edit_mode_link e_menu_delete">Удалить статью</a>';
	
	e_menu.style.width = e_menu.w +'px';
	e_menu.innerHTML = out;
}