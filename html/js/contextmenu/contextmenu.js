// class_name - класс, для которого вызывается контекстное меню
// contextmenu_content - двумерный массив, в 0 индексе стоит url или #функция, которая вставиться так onClick = function(objContext);
// Пример инициализации:
// class_name = "contextmenu_shopmain";
// var contextmenu_shopmain = [
// 	 ["admin/com/shop", "contextmenu_tools", "Настройки"],
//	 ["admin/com/shop/orders", "contextmenu_orders", "Заказы"],
//	 ["#function", "contextmenu_import", "Экспорт / Импорт"],			
// ];
// contextmenu(class_name, contextmenu_shopmain);


function contextmenu(class_name, contextmenu_content)
{
	var class_context = document.getElementsByClassName(class_name);
	var class_context_length = class_context.length;

	for (var i = 0; i < class_context_length; i++)
	{
		class_context[i].oncontextmenu = function(e)
		{
			// получаем узел с указанным классом по которому был вызван контекстный клик
			objContext = context_get_element(e, class_name);
			
			// защита от повторного контекстного окна
			if (document.getElementById('contextmenu') != null){contextmenu_remove(e);}
			
			if (objContext.getAttribute("data-id") != null){var data_id = objContext.getAttribute("data-id");}
			else {var data_id = '';}
		
			var c_menu = document.createElement('div');
			c_menu.id = 'contextmenu';
			
			var body_child_0 = document.body.children[0];
			document.body.insertBefore(c_menu, body_child_0);
		
			c_menu.style.top = (e.pageY - 10) + 'px';
			c_menu.style.left = (e.pageX - 10) +'px';

			contextmenu_content_length = contextmenu_content.length;
			
			var out = '';

			for (var c = 0; c < contextmenu_content_length; c++)
			{		
				if (contextmenu_content[c][0][0] == '#') // исполнить как функцию
				{
					var str = contextmenu_content[c][0];
					var f_name = str.substr(1, str.length);
					out += '<a href="#" onClick="' + f_name + '(objContext); return false;" class="' + contextmenu_content[c][1] + '">' + contextmenu_content[c][2] + '</a>';				
				} 
				else
				{
					out += '<a href="/' + contextmenu_content[c][0] + '/' + data_id + '" class="' + contextmenu_content[c][1] + '">' + contextmenu_content[c][2] + '</a>';				
				}
			}
			
			c_menu.innerHTML = out;
			
			if (e.preventDefault) {e.preventDefault();} 
			else {e.returnValue = false;} // вариант IE<9:
		} 
	}
	
	document.onclick = contextmenu_remove;
	return false;	
}


// получаем элемент на котором отслеживаем клик
function context_get_element(e, class_name)
{
	var objParent = e.target || e.srcElement;

	while(objParent)
	{
		if (objParent.classList.contains(class_name)){return objParent;}
		objParent = objParent.parentNode;
	}
	return false;
}


// Удаляем контекстное меню
function contextmenu_remove(e)
{
	node = document.getElementById('contextmenu');
	
	var objCnt = e.target || e.srcElement;
	
	if (node){document.body.removeChild(node);}
}

