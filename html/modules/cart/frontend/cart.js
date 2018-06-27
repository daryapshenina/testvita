function mod_cart_raschet()
{
	var quantity_arr = document.getElementsByClassName('mod_cart_quantity');
	var price_arr = document.getElementsByClassName('mod_cart_price');
	var summa = 0;	
	
	for (var i = 0; i < quantity_arr.length; i++)
	{
		var sum = quantity_arr[i].value * price_arr[i].value;
		summa += sum;

		sum = Math.round(sum*100)/100;
		if(sum == 0) sum = '';
	}
	
	summa = Math.round(summa*100)/100;

	document.getElementById('summa').innerHTML = summa;
}



function mod_cart_delete_ajax()
{
	var arr = document.getElementsByClassName("mod_cart_delete");
console.log("++++++++++");	
console.log(arr);
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
					mod_cart_raschet();
				}
			}
		}
		req.open('GET', 'http://' + document.domain + '/shop/basket/delete/' + id, true);
		req.send(null);
		
		parent_td.removeChild(_element);
		parent_td.innerHTML = '<img src="http://' + document.domain + '/administrator/tmp/images/loading.gif">';	
	}
}