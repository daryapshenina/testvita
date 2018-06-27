function basket_delete_ajax()
{
	var arr = document.getElementsByClassName("order_items_delete");
	
	for (var i = 0; i < arr.length; i++)
	{
		arr[i].addEventListener("click", function(e)
		{
			var element = this;

			delete_ajax(element);
		}, true);
	}		
	
	
	function delete_ajax(_element)
	{
		var id = _element.getAttribute("data-id");
		var parent_td = _element.parentNode;

		var req = getXmlHttp()
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
			{
				if(req.status == 200)
				{
					var parent_tr = parent_td.parentNode;
					var parent_table = parent_tr.parentNode;
					parent_table.removeChild(parent_tr);
					raschet();
				}
			}
		}
		req.open('GET', 'http://' + document.domain + '/shop/basket/delete/' + id, true);
		req.send(null);
		
		parent_td.removeChild(_element);
		parent_td.innerHTML = '<img src="http://' + document.domain + '/administrator/tmp/images/loading.gif">';	
	}
}
