DAN_ready(function()
{
	drag_drop("drag_trg", "drag_drop");
		
	var class_name = "drag_drop";
	contextmenu_shop = [
		["#delete_char_ajax", "contextmenu_delete", "Удалить"],
		["admin/com/shop/chars/edit", "contextmenu_edit", "Редактировать"]
	];
	contextmenu(class_name, contextmenu_shop);
	
	

});


var req = getXmlHttp();


function delete_char_ajax(objContext)
{
	var id = objContext.getAttribute("data-id");

	req.open("POST", "/admin/com/shop/chars/delete_ajax", true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send("id=" + id);

	req.onreadystatechange = function()
	{			
		if (req.readyState == 4) 
		{			
			if (req.status == 200)
			{
				/* console.log(objContext + ' ----- ' + id); */
				document.getElementById('drag_trg').removeChild(objContext);
			}
		}
	}
}


function drag_ordering(container_id, class_name, data_ordering)
{
	req.open("POST", "/admin/com/shop/chars/ordering_ajax", true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send("data="+data_ordering);	

	req.onreadystatechange = function() 
	{			
		if (req.readyState == 4) 
		{
			if (req.status == 200) 
			{
				// var data = eval("(" + req.responseText + ")");
			}
		}
	}	
}
	