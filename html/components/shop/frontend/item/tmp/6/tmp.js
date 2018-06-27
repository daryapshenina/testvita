function items_tmp_char()
{
	var sel = document.getElementById("char_sel");
	var item_id = document.getElementById("item_id").value;
	var si = sel.selectedIndex; // selectedIndex в select
	var char_value = sel.options[si].value;	
	var char_name = sel.name.substring(5,sel.name.length - 1); // char[Цвет] => Цвет
	
	item_id_next = items_group(item_id, char_name, char_value);
	
	// Если товар сменился - у нового товара устанавливаем selected
	if(item_id_next != item_id)
	{
		sel.options[si].selected = "";
		
		for(s in sel.options)
		{
			var sel = document.getElementById("char_sel"); // нужно переопределять, т.к DOM узел перестраивается.		

			if (char_value == sel.options[s].value)
			{
				sel.options[s].selected = true;
				break;
			}
		}
	}
}


DAN_ready(function(event)
{
	function item_resize()
	{
		var item_main = document.getElementById("item_main");
		if(item_main.offsetWidth < 750)
		{
			document.getElementById("item_photo_container").style.display = "block";
			document.getElementById("item_photo_container").style.width = "100%";
			document.getElementById("item_shortdesc_container").style.display = "block";
		}
		else
		{
			document.getElementById("item_photo_container").style.display = "table-cell";
			document.getElementById("item_photo_container").style.width = "50%";
			document.getElementById("item_shortdesc_container").style.display = "table-cell";
		}
	}

	window.addEventListener("resize", item_resize);
	item_resize();
});


function item_quantity(shop_item_id, n)
{
	var q = document.getElementById('shop_item_num_' + shop_item_id);
	q.value = parseInt(q.value) + n;
	if(q.value < 1){q.value = 1;}
}


function companions(_id, _title, _photo, _price, _currency)
{
	var item_shortdesc_container = document.getElementById('item_shortdesc_container');
	
	companion_close();

	node = document.createElement("div");
	node.id = 'companion';
	node.classList.add('item_photo_container_border');
	node.innerHTML = '<div class="item_photo_container_content"><div onclick="companion_close()" id="companion_close"></div><img id="shop_item_img_' + _id + '" class="item_photo_big" src="/components/shop/photo/' + _photo + '" alt="Обои" itemprop="image"><div class="item_companions_container"><a class="item_companions_container_text_a" href="/shop/item/' + _id + '">' + _title + '</a><div><span class="item_companions_price">' + _price + '<span class="item_rub">' + _currency + '</span></span><input onclick="shop_buy_fly(' + _id + ');" type="submit" value="В корзину" class="button_cart" name="shopbutton"></div></div></div>';
	item_shortdesc_container.appendChild(node);
}


function companion_close()
{
	var companion_old = document.getElementById('companion');
	if(companion_old){item_shortdesc_container.removeChild(companion_old);}
}