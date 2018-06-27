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

function items_tmp_img_small(_id)
{
	document.getElementById('content').innerHTML = items_out[_id];
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
			document.getElementById("iter_shortdesc_container").style.display = "block";
		}
		else
		{
			document.getElementById("item_photo_container").style.display = "table-cell";
			document.getElementById("item_photo_container").style.width = "50%";
			document.getElementById("iter_shortdesc_container").style.display = "table-cell";
		}
	}

	window.addEventListener("resize", item_resize);
	item_resize();
});


function item_quantity(_id, _n)
{
	var q = document.getElementById("shop_item_num_" + _id);
	q.value = parseInt(q.value) + _n;
	if(q.value < 1){q.value = 1;}
}