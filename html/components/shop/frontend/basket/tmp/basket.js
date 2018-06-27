function raschet()
{
	var com_summa = document.getElementById('com_summa');
	var mod_quantity = document.getElementById('mod_cart_quantity');
	var mod_summa = document.getElementById('mod_cart_summa');	

	if (com_summa)
	{
		var quantity_arr = document.getElementsByClassName('com_basket_quantity');
		var price_arr = document.getElementsByClassName('com_basket_price');
		var sum_arr = document.getElementsByClassName('com_basket_sum');
		var c_summa = 0;
		
		for (var i = 0; i < quantity_arr.length; i++)
		{
			var c_sum = quantity_arr[i].value * price_arr[i].value;
			c_summa += c_sum;

			c_sum = Math.round(c_sum*100)/100;
			if(c_sum == 0) c_sum = '';		
			sum_arr[i].innerHTML = c_sum;		
		}
		
		var c_summa = Math.round(c_summa*100)/100;	
		com_summa.innerHTML = c_summa;			
	}
}


function basket_delete_ajax(_type, _id)
{
	var com_tr = document.getElementById('com_tr_' + _id);

	var req = getXmlHttp()
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
		{
			if(req.status == 200)
			{
				if(com_tr)
				{
					var com_parent_table = com_tr.parentNode;
					com_parent_table.removeChild(com_tr);
				}

				var mod_cart = document.getElementById('mod_cart');				
				
				if(mod_cart)
				{
					var parent_mod_cart = mod_cart.parentNode;
					var m_cart = req.responseText;
					parent_mod_cart.innerHTML =  m_cart;

					// удаляем старую popup корзину
					var popup_close = document.getElementById("dan_framework_popupClose");

					if(popup_close) popup_close.onclick();

					// ставим новую					
					if(_type == "mod") document.getElementById("mod_cart").onclick();
				}				
				
				raschet();				
			}
		}
	}
	req.open('GET', '/shop/basket/delete/' + _id, true);
	req.send(null);
	
	var loading_img = '<img src="/administrator/tmp/images/loading.gif">';
	
	if(com_tr){document.getElementById('com_td_' + _id).innerHTML = loading_img;}
}