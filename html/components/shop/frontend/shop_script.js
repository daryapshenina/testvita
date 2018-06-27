var crReqObj = getXmlHttp();

shop_basket_add = function(shop_item_id)
{
	// Смотрим есть ли поля с кол-вом товаров
	var objInputNum = document.getElementById('shop_item_num_'+shop_item_id);
	if(objInputNum != null)
	{
		if(isNaN(parseInt(objInputNum.value)))
		{
			var shop_item_num = 1;
		}
		else
		{
			var shop_item_num = parseInt(objInputNum.value);
		}
	}
	else
	{
		var shop_item_num = 1;
	}

	var mod_cart = document.getElementById('mod_cart');

	if(mod_cart !== null)
	{
		// Характеристики
		var chars_dom = document.getElementsByClassName('char_select');
		var char_out = '';
		for(i = 0; i < chars_dom.length; i++)	
		{
			console.log(chars_dom[i].name + ' --- ' + chars_dom[i].options[chars_dom[i].selectedIndex].value);
			char_out += '&' + chars_dom[i].name + '=' + chars_dom[i].options[chars_dom[i].selectedIndex].value;
		}

		crReqObj.open("post", "/shop/basket/add_ajax", true);
		crReqObj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		crReqObj.send('id=' + encodeURIComponent(shop_item_id) + '&quantity=' + encodeURIComponent(shop_item_num) + char_out);
		crReqObj.onreadystatechange = function()
		{
			if(crReqObj.readyState == 4)
			{
				if(crReqObj.status == 200)
				{
					var parent_mod_cart = mod_cart.parentNode;
					var m_cart = crReqObj.responseText;
					parent_mod_cart.innerHTML =  m_cart;
				}
			}
		}
	}
}



function shop_buy(shop_item_id)
{
	shop_basket_add(shop_item_id);

    var shop_box_black = document.createElement('div');
    shop_box_black.id = 'shop_box_black';
    shop_box_black.style.opacity = 0;

    var shop_box_black_children = document.body.children[0];
    document.body.insertBefore(shop_box_black, shop_box_black_children);
    dan_lightbox_resize_h = setInterval(dan_lightbox_black_opacity, 50);
    dan_lightbox_s = 0;

    function dan_lightbox_black_opacity()
    {
        if (dan_lightbox_s <= 50)
        {
            dan_lightbox_opacity = dan_lightbox_s / 100;
            shop_box_black.style.opacity = dan_lightbox_opacity;
            shop_box_black.style.filter = "alpha(opacity=60)";
            dan_lightbox_s = dan_lightbox_s + 5;
        }
        else
        {
            clearTimeout(dan_lightbox_resize_h);
            dan_lightbox_s = 0;
        }
    }
    var shop_dialogbox = document.createElement("div");
    var disp_width = document.documentElement.clientWidth / 2 - 175
    var disp_height = document.documentElement.clientHeight / 2 - 85
    shop_dialogbox.style.top = disp_height + "px";
    shop_dialogbox.style.left = disp_width + "px";
    shop_dialogbox.id = "shop_dialogbox";
    document.body.appendChild(shop_dialogbox);
    document.getElementById("shop_dialogbox").innerHTML = '<table border="0" width="350" cellpadding="0" style="border-collapse: collapse" height="170"><tr><td height="10">&nbsp;</td></tr><tr><td colspan="2" align="center" height="120"><b style="font-size:14px;">Товар добавлен в корзину</b><br /><br /><div id="shop_dialogbox_img"></div></td></tr><tr><td align="center"><input type="submit" id="shop_button_gocart" value="Перейти в корзину" /></td><td align="center"><input type="submit" id="shop_button_goshop" value="Продолжить покупки" /></td></tr></table>';

    document.getElementById('shop_button_gocart').onclick = function shop_button_gocart()
    {
        document.location.href = '/shop/basket';
    }

    document.getElementById('shop_button_goshop').onclick = function shop_button_goshop()
    {
        shop_dialogbox.parentNode.removeChild(shop_dialogbox);
        shop_box_black.parentNode.removeChild(shop_box_black);
    }

    document.getElementById('shop_button_gocart').onmouseover = function shop_button_gocart()
    {
        document.getElementById('shop_dialogbox_img').style.backgroundPosition = "top";
    }

    document.getElementById('shop_button_goshop').onmouseover = function shop_button_gocart()
    {
        document.getElementById('shop_dialogbox_img').style.backgroundPosition = "bottom";
    }
}



// находит позицию объекта, поднимаясь по дереву DOM
function getElementPosition(elem_id)
{
	var elem = document.getElementById(elem_id);

	if(elem === null)
		return null;

	var w = elem.offsetWidth;
	var h = elem.offsetHeight;

	var l = 0;
	var t = 0;

	while (elem)
	{
		l += elem.offsetLeft;
		t += elem.offsetTop;
		elem = elem.offsetParent;
	}

	return {"left":l, "top":t, "width": w, "height":h};
}



// летающая корзина
function shop_buy_fly(shop_item_id)
{
	shop_basket_add(shop_item_id);

	// если объект ещё не создан -> создаём
	if (clone_img == null)
	{
		var clone_img = new Object();
	}

	// допускаем множество одновременно летящих товаров
	var rnd = Math.floor(Math.random()*1000000);

	clone_img.rnd = rnd;

	var elem_id = 'shop_item_img_' + shop_item_id;

	clone_img.rnd = getElementPosition(elem_id);

	clone_img.rnd.node = document.getElementById('shop_item_img_'+shop_item_id).cloneNode(false);

	clone_img.rnd.node.id = 'shop_clone_img';
	clone_img.rnd.node.style.position = 'absolute';
	clone_img.rnd.node.style.zIndex = '1100';
	clone_img.rnd.node.style.top = clone_img.rnd.top + 'px';
	clone_img.rnd.node.style.left = clone_img.rnd.left + 'px';

	var tag_body = document.body.children[0];
	document.body.insertBefore(clone_img.rnd.node, tag_body);

	// корзина
	var modcart_button = getElementPosition('mod_cart');

	if(modcart_button === null)
		return;

	// расстояние между товаром и корзиной
	var delta_top = clone_img.rnd.top - modcart_button.top;
	var delta_left = clone_img.rnd.left - modcart_button.left;

	var i = 0;

	function move_item(fraction) {

		requestAnimationFrame(function move_item(fraction) {

			clone_img.rnd.node.style.top = Math.floor(clone_img.rnd.top - i*(delta_top)/50) + 'px';
			clone_img.rnd.node.style.left = Math.floor(clone_img.rnd.left - i*(delta_left)/50) + 'px';
			clone_img.rnd.node.style.opacity = (50-i)/50;
			i++;

			if (i <= 50)
			{
				requestAnimationFrame(move_item);
			}
			else
			{
				document.body.removeChild(clone_img.rnd.node);
				delete clone_img.rnd;
			}
		});
	}

	move_item();

}



function shopItemNum(id, sign)
{
	var obj = document.getElementById('shop_item_num_'+id);
	if((parseInt(obj.value) + sign) >= 999)
	{
		obj.value = 999;
	}
	else if((parseInt(obj.value) + sign) >= 1)
	{
		obj.value = parseInt(obj.value) + sign;
	}
}

function shopItemNumInput(obj)
{
	if(isNaN(parseInt(obj.value)))
	{
		obj.value = 1;
	}
	else if(parseInt(obj.value) >= 999)
	{
		obj.value = 999;
	}
	else if(parseInt(obj.value) < 1)
	{
		obj.value = 1;
	}
	else
	{
		obj.value = parseInt(obj.value);
	}
}
