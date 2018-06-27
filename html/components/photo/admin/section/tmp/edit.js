DAN_ready(function()
{
	drag_drop("drag_trg", "photo_item");	
});

function drag_ordering(container_id, class_name)
{
	if (container_id == 'drag_trg')
	{
		var input_arr = '';	
		var dt_node = document.getElementById(container_id);
		for (var childItem in dt_node.childNodes) 
		{	
			var item = dt_node.childNodes[childItem];	
			
			if (item.className == class_name)
			{
				item_id = item.getAttribute("data-id");
				input_arr = input_arr + item_id + ';';
			}
		}
		
		document.getElementById('images_order').value = input_arr;
		document.getElementById('images_order_button').style.opacity = 1;
	}
	else {return false;}
}




function delete_ajax(objContext)
{
	var id = document.getElementById('drag_trg').getAttribute("data-id");
	
	img_name = fun_img_name(objContext);
	
	req.open("POST", "http://" + document.domain + "/admin/com/shop/img_delete_ajax", true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send("id=" + id + "&img_name=" + img_name);

	req.onreadystatechange = function()
	{			
		if (req.readyState == 4) 
		{			
			if (req.status == 200)
			{
				// console.log(objContext + ' ----- ' + img_name);
				document.getElementById('drag_trg').removeChild(objContext);
				document.getElementById("img_status").innerHTML = req.responseText;
				drag_ordering('drag_trg', 'drag_drop'); // формируем порядок следования
			}
		}
	}
}