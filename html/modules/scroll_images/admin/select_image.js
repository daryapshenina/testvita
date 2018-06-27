DAN_ready(function(){
	image_add = document.getElementById("image_add");
	image_add.onclick = changeImage;

	var numberImage = 0;

	// Индикатор ширины
	var wid = document.getElementById("width");
	wid.onmousemove = function(){
		document.getElementById("width_out").innerHTML = wid.value + " пикселей";				
	}
	
	// Индикатор скорости
	var speed = document.getElementById("speed");
	speed.onmousemove = function(){
		document.getElementById("speed_out").innerHTML = speed.value + " сек.";				
	}
	
	// Индикатор паузы
	var pause = document.getElementById("pause");
	pause.onmousemove = function(){
		document.getElementById("pause_out").innerHTML = pause.value + " сек.";				
	}
	
	// инициализируем функцию drag_drop
	drag_drop("image_list", "drag_drop");
	
	// настройки контекстного меню
	contextmenu_item_photo = [["#img_delete", "contextmenu_delete", "Удалить"]];
	
	// инициализируем контекстное меню
	contextmenu("drag_drop", contextmenu_item_photo);


	// Вызывает окно файлового менеджера
	function changeImage(num)
	{
		numberImage = num;
		window.open('/administrator/plugins/browser/dan_browser.php?dir_current=scroll_images&CKEditor=editor1&CKEditorFuncNum=2&langCode=ru');
	}


	// Открывается по завершении вызова файлового менеджера
	 CKEDITOR.tools = 
	 {
		callFunction:function(funcNum, url, message) 
		{
			document.getElementById("image_list").innerHTML += '<img class="drag_drop" src="'+url+'" alt="">';
			
			// инициализируем заново функцию drag_drop - т.к. появился новый узел на котором следует отслеживать событие
			drag_drop("image_list", "drag_drop");

			// инициализируем занесение изображений в скрытое поле даже без вызова функции drag_drop
			drag_ordering("image_list", "drag_drop");

			// инициализируем контекстное меню
			contextmenu("drag_drop", contextmenu_item_photo);
		}
	 }
});


// Вызывается функцией drag_drop после завершения выполнения
function drag_ordering(container_id, class_name)
{
	if (container_id == 'image_list')
	{
		var input_arr = '';	
		var dt_node = document.getElementById(container_id);
		for (var childItem in dt_node.childNodes) 
		{	
			var img = dt_node.childNodes[childItem];
			if (img.className == class_name)
			{
				// img_name = fun_img_name(img);
				img_name = img.src;
				input_arr = input_arr + img_name + ';';
			}
		}
		
		document.getElementById('images_order').value = input_arr;		
	}
	else {return false;}
}


// выделяет имя из пути src
function fun_img_name(img)
{
	img_arr = img.src.split('/');
	img_name = img_arr[img_arr.length-1];
	return img_name;
}


// Удаляет изображение
function img_delete(objContext) 
{
	document.getElementById('image_list').removeChild(objContext);
	
	// инициализируем лист изображений в скртом поле
	drag_ordering("image_list", "drag_drop");	
}