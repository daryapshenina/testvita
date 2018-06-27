function e_com_shop_section(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';
	
	var out = '<div class="edit_mode_title">Раздел магазина</div>';
	out += '<a href="/admin/com/shop/section/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	out += '<a href="/admin/com/shop/settings/' + id + '" class="edit_mode_link e_menu_settings">Настройки магазина</a>';
	
	e_menu.style.width = e_menu.w +'px';
	e_menu.innerHTML = out;
}

function e_com_shop_item(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';

	var out = '<div class="edit_mode_title">Товар</div>';

	var obj = e.target || e.srcElement;
	
	if (obj.tagName == 'A')
	{
		var a = obj.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Карточка товара</a>';
	}
	
	if ((obj.tagName == 'IMG' || obj.tagName == 'DIV') && obj.parentNode.tagName == 'A')
	{
		var a = obj.parentNode.getAttribute("href");
		out += '<a href="' + a + '" class="edit_mode_link e_menu_link">Карточка товара</a>';
	}
	
	out += '<a href="/admin/com/shop/item/edit/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	out += '<a href="/admin/com/shop/item/add" class="edit_mode_link e_menu_add">Добавить товар</a>';
	out += '<a href="/admin/com/shop/item/unpub/' + id + '" class="edit_mode_link e_menu_unpub">Скрыть товар</a>';	
	out += '<a href="/admin/com/shop/item/delete/' + id + '" class="edit_mode_link e_menu_delete">Удалить товар</a>';
	
	e_menu.style.width = e_menu.w +'px';
	e_menu.innerHTML = out;
}

function e_com_shop_section_filter(e, obj, id)
{
	// эти значения нужны для расчётов - не стирать!
	e_menu.w = '200';

	var out = '<div class="edit_mode_title">Раздел - фильтры</div>';
	out += '<a href="/admin/com/shop/section/' + id + '" class="edit_mode_link e_menu_edit">Редактировать</a>';
	out += '<a href="/admin/com/shop/settings/' + id + '" class="edit_mode_link e_menu_settings">Настройки магазина</a>';
	
	e_menu.style.width = e_menu.w +'px';	
	e_menu.innerHTML = out;
}