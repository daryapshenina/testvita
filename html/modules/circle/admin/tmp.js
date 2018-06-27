DAN_ready(function(event)
{
	var width_fix = document.getElementById('width_fix');
	var height_fix = document.getElementById('height_fix');
	var padding_w = document.getElementById('padding_w');
	var padding_h = document.getElementById('padding_h');
	var old_width = width_fix.value;
	var font_size_input = document.getElementById('font_size_input');
	var text_color = document.getElementById('text_color');
	var bg_color = document.getElementById('bg_color');
	var editor1 = document.getElementById('editor1');
	var mod_circle_container = document.getElementsByClassName('mod_circle_container')[0];
	var mod_circle_text = document.getElementsByClassName('mod_circle_text')[0];
	var mod_circle_back = document.getElementsByClassName('mod_circle_back')[0];

	font_size_input.onmousemove = f_font_size;
	text_color.onchange = function(){mod_circle_text.style.color = text_color.value;}
	bg_color.onchange = function(){mod_circle_back.style.backgroundColor = bg_color.value;}
	width_fix.onchange = f_width;
	height_fix.onchange = f_height;
	padding_w.onchange = function(){mod_circle_container.style.paddingLeft = padding_w.value + 'px';mod_circle_container.style.paddingRight = padding_w.value + 'px';}
	padding_h.onchange = function(){mod_circle_container.style.paddingTop = padding_h.value + 'px';mod_circle_container.style.paddingBottom = padding_h.value + 'px';}

	f_font_size();

	function f_font_size()
	{
		var size = font_size_input.value;
		document.getElementById('font_size_out').innerHTML = size;
		mod_circle_text.style.fontSize = size + 'px';
	}

	function f_width()
	{
		if (width_fix.value > old_width){alert("Новый размер больше исходного, перезагрузите изображение, что бы не потерять качество картинки и нажмите CTRL + F5");}
		mod_circle_container.style.width = width_fix.value + 'px';
		mod_circle_container.style.height = width_fix.value + 'px';
		editor1.style.width = width_fix.value + 'px';
	}

	function f_height()
	{
		if (height_fix.value < width_fix.value)
		{
			alert("Высота модуля не может быть меньше ширины")
		}
		else
		{
			mod_circle_container.style.height = height_fix.value + 'px';
		}
	}
});