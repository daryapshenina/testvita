DAN_ready(function()
{
	document.oncontextmenu = e_contextmenu;
});

function e_contextmenu(e)
{
	objContext = get_element(e);
	var order_id = objContext.getAttribute("data-id");

	// защита от повторного контекстного окна
	if (document.getElementById('contextmenu') == null)
	{
		// отмена действий браузера - кроссбраузерный код
		event = e || window.e;
		
		var c_menu = document.createElement('a');
		c_menu.id = 'contextmenu';
		c_menu.setAttribute('data-id', order_id);
		
		var body_child_0 = document.body.children[0];
		document.body.insertBefore(c_menu, body_child_0);
	
		c_menu.style.top = (event.pageY - 10) + 'px';
		c_menu.style.left = (event.pageX - 10) +'px';
		
		c_menu.innerHTML = 'Удалить';
		
		if (event.preventDefault) {event.preventDefault();} 
		else {event.returnValue = false;} // вариант IE<9:
	}
	
	document.onclick = e_contextmenu_action;	
}


function e_contextmenu_action(e)
{
	node = document.getElementById('contextmenu');
	
	var objCnt = e.target || e.srcElement;
	
	if (node)
	{
		if (node == objCnt)
		{
			var id = objCnt.getAttribute("data-id");
			delete_ajax(objContext, id);
		}
		
		document.body.removeChild(node);
	}
}


function delete_ajax(objContext, id)
{
	if (id == '' || id == 'null')
	{
		document.getElementById('drag_trg').removeChild(objContext);			
	} 
	else
	{
		//req.open("POST", "/admin/com/shop/item/delete_ajax", true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send("id=" + id);

		req.onreadystatechange = function()
		{			
			if (req.readyState == 4) 
			{			
				if (req.status == 200)
				{
					document.getElementById('drag_trg').removeChild(objContext);
				}
			}
		}
	}		
}