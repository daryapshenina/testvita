var IMAGE_RESIZE = {
	obj: false,

	options: {
		w: 200, // Ширина выделенной области
		h: 200, // Высота выделенной области
		aspectRatio: false, // Пропорции aspectRatio: '4:3',
		thumbnail: false, // Показать миниатюру
		thumbnail_w: 200, // Ширина миниатюры
		thumbnail_h: 200, // Высота миниатюры
		resizable: true, // Изменение размеров
		scale: 'auto', // Масштаб 1.0 - 3.0 | auto
		zIndex: 10100,
	},

	win: function(_file, _callback)
	{
		if (!_file[0].type.match(/image.*/))
		{
			alert("Этот тип изображения не поддерживается!");
			return false;
		}

		if (_file[0].size > 4096000)
		{
			alert("Размер изображения не более 4 Мб.");
			return false;
		}

		// защита от повторного создания модального окна
		if (document.getElementById('image_resize_container') == null)
		{
			var container = document.createElement('div');
			container.id = 'image_resize_container';
			container.style.zIndex = IMAGE_RESIZE.options.zIndex;
			document.body.insertBefore(container, document.body.children[0]);

			var panel = '<div id="image_src_title" class="image_resize_title_right">Обработка изображения:</div>';
			panel += '<div id="right_src">';
			panel += '<div id="image_src_text" class="right_container"><span class="right_text_title">Исходное изображение:</span></div>';
			panel += '<div id="image_src_scale" class="right_container"><span class="right_text">Масштаб:</span><input id="scale" class="range" type="range" name="scale" value="1" min="1" max="3" step="0.1"><span id="range_num">1 : 1</span></div>';
			panel += '<div id="image_src_size" class="right_container"><span class="right_text">Ширина:</span><span id="w_source" class="source"></span><span class="right_text">Высота:</span><span id="h_source" class="source"></span></div>';
			panel += '</div>';
			panel += '<div id="right_final">';			
			panel += '<div id="right_final_title" class="right_container"><span class="right_text_title">Конечное изображение:</span></div>';
			if(IMAGE_RESIZE.options.thumbnail) panel += '<div id="thumbnail_container"><img id="thumbnail" src=""></div>';
			panel += '<div id="right_final_size" class="right_container"><span class="right_text">Ширина:</span><input id="w" class="input number" type="number" name="w" min="100" requered><span class="right_text">Высота:</span><input id="h" class="input number" name="h" type="number" min="100" requered></div>';
			panel += '<div id="right_final_1" class="right_container"><span class="right_text">X1:</span><input id="x1" class="input number" name="x1" type="number" requered><span class="right_text">Y1:</span><input id="y1" class="input number" name="y1" type="number" requered></div>';
			panel += '<div id="right_final_2" class="right_container"><span class="right_text">X2:</span><input id="x2" class="input number" name="x2" type="number" requered><span class="right_text">Y2:</span><input id="y2" class="input number" name="y2" type="number" requered></div>';
			panel += '</div>';
			panel += '<div class="right_container_button"><input id="image_resize_submit" class="button_green" name="submit" type="button" value="Сохранить"></div>';
			panel += '<div class="right_container_button"><input id="image_resize_cancel" class="button_light" name="cancel" type="button" value="Выход"></div>';
			container.innerHTML = '<div id="image_resize_left"><div id="image_left_title">Установите нужный размер изображения</div><div id="image_resize_big"><img id="image_resize_big_image" src="" alt="Большое изображение"></div></div><div id="image_resize_right">' + panel + '</div>';
		}

		// Создаем объект FileReader и по завершении чтения файла, отображаем миниатюру
		var reader = new FileReader();
		reader.onload = function(_src)
		{
			var container_big = document.getElementById('image_resize_big');
			var img = document.getElementById('image_resize_big_image');
			img.src = _src.target.result;

			img.onload = function()
			{
				var img_c = img.getBoundingClientRect();
			
				if(IMAGE_RESIZE.options.thumbnail)
				{
					var thumbnail = document.getElementById("thumbnail");
					var thumbnail_container = document.getElementById("thumbnail_container");				
					thumbnail.src = _src.target.result;

					thumbnail_container.style.width = IMAGE_RESIZE.options.thumbnail_w + 'px'; 
					thumbnail_container.style.height = IMAGE_RESIZE.options.thumbnail_h + 'px';
				}

				var x_1 = img_c.width/2 - IMAGE_RESIZE.options.w/2;
				var x_2 = x_1 + IMAGE_RESIZE.options.w;
				var y_1 = img_c.height/2 - IMAGE_RESIZE.options.h/2;
				var y_2 = y_1 + IMAGE_RESIZE.options.h;

				IMAGE_RESIZE.obj = $('img#image_resize_big_image').imgAreaSelect({
					instance: true,
					handles: true,
					resizable: IMAGE_RESIZE.options.resizable,
					keys: true,
					parent: document.getElementById('image_resize_big'),
					aspectRatio: IMAGE_RESIZE.options.aspectRatio,
					x1: x_1,
					y1: y_1,
					x2: x_2,
					y2: y_2,
					onInit: IMAGE_RESIZE.select_end,
					onSelectChange: IMAGE_RESIZE.select_end
				});		

				IMAGE_RESIZE.obj.x1 = x_1;
				IMAGE_RESIZE.obj.x2 = x_2;
				IMAGE_RESIZE.obj.y1 = y_1;
				IMAGE_RESIZE.obj.y2 = y_2;
				IMAGE_RESIZE.obj.src = _src.target.result;

				document.getElementById('w_source').innerHTML = img.naturalWidth;
				document.getElementById('h_source').innerHTML = img.naturalHeight;

				if(IMAGE_RESIZE.options.thumbnail) IMAGE_RESIZE.set_thumbnail(); // Создаём уменьшенную копию		

				document.getElementById('w').onchange = function(){IMAGE_RESIZE.new_size('w');}
				document.getElementById('h').onchange = function(){IMAGE_RESIZE.new_size('h');}
				document.getElementById('x1').onchange = function(){IMAGE_RESIZE.new_size('x1');}
				document.getElementById('y1').onchange = function(){IMAGE_RESIZE.new_size('y1');}
				document.getElementById('x2').onchange = function(){IMAGE_RESIZE.new_size('x2');}
				document.getElementById('y2').onchange = function(){IMAGE_RESIZE.new_size('y2');}

				if(IMAGE_RESIZE.options.scale != 1){
					if(IMAGE_RESIZE.options.scale == 'auto')
					{
						if(img.naturalWidth > container_big.clientWidth)
						{
							var s = img.naturalWidth / container_big.clientWidth;
							IMAGE_RESIZE.options.scale = Math.round(s * 10) / 10;
							IMAGE_RESIZE.scale(IMAGE_RESIZE.options.scale);
							document.getElementById('scale').value = IMAGE_RESIZE.options.scale;
						}
					}
					else
					{
						IMAGE_RESIZE.scale(IMAGE_RESIZE.options.scale);
						document.getElementById('scale').value = IMAGE_RESIZE.options.scale;						
					}
				}
				document.getElementById('scale').onmousemove = function(){IMAGE_RESIZE.scale(this.value);}								
			}

			// Выход
			document.getElementById('image_resize_cancel').onclick = function()
			{
				$('img#image_resize_big_image').imgAreaSelect({remove: true});
				container.parentNode.removeChild(container);
			}
			
			// Сохранить
			document.getElementById('image_resize_submit').onclick = function()
			{
				IMAGE_RESIZE.obj.scale = document.getElementById('scale').value;
				IMAGE_RESIZE.obj.file = _file[0];				
				$('img#image_resize_big_image').imgAreaSelect({remove: true});
				container.parentNode.removeChild(container);

				_callback();
			}
		}

		reader.readAsDataURL(_file[0]);
	},


	scale: function(_scale)
	{
		var img = document.getElementById('image_resize_big_image');

		var w_input = document.getElementById('w').value;
		var h_input = document.getElementById('h').value;

		var img_w = img.naturalWidth;
		var img_h = img.naturalHeight;

		var img_w_new = img_w / _scale;
		var img_h_new = img_h / _scale;

		var x1_new = IMAGE_RESIZE.obj.x1 / _scale;
		var y1_new = IMAGE_RESIZE.obj.y1 / _scale;
		var x2_new = IMAGE_RESIZE.obj.x2 / _scale;
		var y2_new = IMAGE_RESIZE.obj.y2 / _scale;

		img.style.width = img_w_new + 'px';
		img.style.height = img_h_new + 'px';

		document.getElementById('range_num').innerHTML = '1 : ' + _scale;
		document.getElementById('w_source').innerHTML = parseInt(parseInt(img.naturalWidth) / _scale);
		document.getElementById('h_source').innerHTML = parseInt(parseInt(img.naturalHeight) / _scale);		
		document.getElementById('w').value = parseInt(x2_new - x1_new);
		document.getElementById('h').value = parseInt(y2_new - y1_new);
		document.getElementById('x1').value = parseInt(x1_new);
		document.getElementById('x2').value = parseInt(x2_new);
		document.getElementById('y1').value = parseInt(y1_new);
		document.getElementById('y2').value = parseInt(y2_new);

		IMAGE_RESIZE.obj.setOptions({imageWidth: img_w_new, imageHeight: img_h_new});
		IMAGE_RESIZE.obj.setSelection(x1_new, y1_new, x2_new, y2_new, true);
		IMAGE_RESIZE.obj.update();
		
		if(IMAGE_RESIZE.options.thumbnail) IMAGE_RESIZE.set_thumbnail();		
	},


	select_end: function(_img, _selection)
	{
		$('#x1').val(_selection.x1);
		$('#y1').val(_selection.y1);
		$('#x2').val(_selection.x2);
		$('#y2').val(_selection.y2);
		$('#w').val(_selection.width);
		$('#h').val(_selection.height);

		var scale = document.getElementById('scale').value;

		IMAGE_RESIZE.obj.x1 = parseInt(_selection.x1 * scale);
		IMAGE_RESIZE.obj.x2 = parseInt(_selection.x2 * scale);
		IMAGE_RESIZE.obj.y1 = parseInt(_selection.y1 * scale);
		IMAGE_RESIZE.obj.y2 = parseInt(_selection.y2 * scale);
	
		if(IMAGE_RESIZE.options.thumbnail) IMAGE_RESIZE.set_thumbnail();
	},


	new_size: function(_param)
	{
		var w_input = document.getElementById('w');
		var h_input = document.getElementById('h');
		var x1_input = document.getElementById('x1');
		var x2_input = document.getElementById('x2');
		var y1_input = document.getElementById('y1');
		var y2_input = document.getElementById('y2');

		var w_new = parseInt(w_input.value);
		var h_new = parseInt(h_input.value);
		var x1_new = parseInt(x1_input.value);
		var x2_new = parseInt(x2_input.value);
		var y1_new = parseInt(y1_input.value);
		var y2_new = parseInt(y2_input.value);

		var scale = document.getElementById('scale').value;

		if(_param == 'w')
		{
			var x2_new = x1_new + w_new;
			x2_input.value = x2_new;
		}

		if(_param == 'h')
		{
			var y2_new = y1_new + h_new;
			y2_input.value = y2_new;
		};

		if(_param == 'x1')
		{
			var x2_new = x1_new + w_new;
			x2_input.value = x2_new;
		}

		if(_param == 'x2')
		{
			var w_new = x2_new - x1_new;
			w_input.value = w_new;
		}

		if(_param == 'y1')
		{
			var y2_new = y1_new + h_new;
			y2_input.value = y2_new;
		}

		if(_param == 'y2')
		{
			var h_new = y2_new - y1_new;
			h_input.value = h_new;
		}


		IMAGE_RESIZE.obj.x1 = parseInt(x1_new * scale);
		IMAGE_RESIZE.obj.x2 = parseInt(x2_new * scale);
		IMAGE_RESIZE.obj.y1 = parseInt(y1_new * scale);
		IMAGE_RESIZE.obj.y2 = parseInt(y2_new * scale);

		IMAGE_RESIZE.obj.setSelection(x1_new, y1_new, x2_new, y2_new, true);
		IMAGE_RESIZE.obj.update();
		
		if(IMAGE_RESIZE.options.thumbnail) IMAGE_RESIZE.set_thumbnail();
	},
	
	
	// Установка уменьшенной версии
	set_thumbnail: function()
	{
		var img = document.getElementById('image_resize_big_image');		
		var thumbnail = document.getElementById("thumbnail");
		var img_size = img.getBoundingClientRect();

		var scale_x = (IMAGE_RESIZE.obj.x2 - IMAGE_RESIZE.obj.x1) / IMAGE_RESIZE.options.thumbnail_w;
		var scale_y = (IMAGE_RESIZE.obj.y2 - IMAGE_RESIZE.obj.y1) / IMAGE_RESIZE.options.thumbnail_h;

		thumbnail.style.width = img.naturalWidth / scale_x + 'px';
		thumbnail.style.height = img.naturalHeight / scale_y + 'px';		
		thumbnail.style.marginLeft = '-' + (IMAGE_RESIZE.obj.x1 / scale_x) + 'px';
		thumbnail.style.marginTop = '-' + (IMAGE_RESIZE.obj.y1 / scale_y) + 'px';
	}
}