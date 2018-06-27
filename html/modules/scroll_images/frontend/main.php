<?php
// Скроллер
defined('AUTH') or die('Restricted access');

if(floatval($m['p3']) < 0.2){$m['p3'] = 0.2;}

$image_arr = explode(';', $m['content']);
$images_out = '';


foreach ($image_arr as $src)
{
	// Добавляем изображения в переменную
	if($src != '')
	{
		$size = @getimagesize($src);
		$k = @($size[1]/$size[0]); // соотношение высоты к ширине
		$h = intval($m['p2'] * $k); // высота малого изображения
		$images_out .= '<img class="scroll_image" onclick="DAN_modal(\''.$size[1].'\', \''.$size[0].'\', \'\', \'<img src='.$src.' />\')" src='.$src.' alt="" />';
	}
}


// Скрипты // =========================================================
echo '<style type="text/css">
	#scroll_frame {
		transition:'.$m['p3'].'s;
	}
</style>';

echo '
	<script>
		document.addEventListener("DOMContentLoaded", function(e){

		// Настройки
		var img_min = '.$m['p2'].';
		var img_num = '.$m['p1'].';
		var img_padding = 5;
		
		// Пауза
		var scroll_inderval = '.($m['p4'] + $m['p3']).' * 1000;					
		
		var scroll_frame = document.getElementById("scroll_frame");
	
		// Стрелки
		var scroll_prev = document.getElementById("scroll_prev");
		var scroll_next = document.getElementById("scroll_next");				
		
		// Запуск
		function scroll_image_start()
		{
			if(typeof scroll_timer != "undefined"){clearInterval(scroll_timer);}

			var scroll_main = document.getElementById("scroll_main");
			var images = document.getElementsByClassName("scroll_image");
			var images_length = images.length; 

			scroll_main.style.display = "block";
			scroll_main.w = scroll_main.offsetWidth;

			// Находим сколько картинок поместится.
			var n = parseInt(scroll_main.w / (img_min + img_padding * 2));	
			if(n < img_num){var num = n;}else{var num = img_num;}

			// Ширина отдельного изображения
			var img_w = (scroll_main.w / num) - 2 * img_padding;				

			// Ширина фрейма
			scroll_frame.w = (img_w + 2 * img_padding) * images_length + 1;
			scroll_frame.style.width = scroll_frame.w + "px";					

			// Высота скроллера по первой картинке
			if(images_length == 0)
			{
				// если нет изображений
				var k = 1;
			}
			else
			{
				var k = images[0].naturalHeight/images[0].naturalWidth;						
			}

			scroll_main.h = parseInt(k * img_w);
			scroll_main.style.height = scroll_main.h + "px";

			// Высота отдельного изображения = высоте модуля = высоте первой картинки
			var img_h = scroll_main.h;

			// Переустанавливаем ширину картинок
			for (var i = 0; i < images_length; i++)
			{
				images[i].style.width = img_w + "px";
				images[i].style.height = img_h + "px";
				images[i].style.paddingLeft = img_padding + "px"; // Общий паддинг не ставим, нам надо отсечь те части изображения, которые не помещаются по высоте.
				images[i].style.paddingRight = img_padding + "px";						
			}

			// Не запускаем если ширина фрейма меньше ширины свитка
			if(scroll_frame.w > scroll_main.w)
			{
				// запускаем таймер
				i = 0;
				scroll_timer = setInterval(function(){
					i++;				
					if (i > (images_length - num)){i = 0;}
					scroll_frame.style.marginLeft = -((img_w + 2 * img_padding) * i) + "px";							
				}, scroll_inderval);
			}

			scroll_next.onclick = function()
			{
				i++;						
				clearInterval(scroll_timer);					
				if (i > (images_length - num))
				{
					i = images_length - num;
				}
				scroll_frame.style.marginLeft = -((img_w + 2 * img_padding) * i) + "px";
			};					

			scroll_prev.onclick = function()
			{
				i--;						
				clearInterval(scroll_timer);					
				if (i < 0)
				{
					i = 0;
				}
				scroll_frame.style.marginLeft = -((img_w + 2 * img_padding) * i) + "px";
			};					

			window.addEventListener("resize", scroll_image_start);				
		}				
		
		// Запускаем анимацию
		scroll_image_start();
	});
	</script>
';	

// Вывод // =========================================================
// верх модуля

// Заголовок модуля
if ($m['titlepub'] == "1"){$title_out = '<div class="mod-title">'.$m['title'].'</div>';} else {$title_out = '';}

$out = '<div class="mod-main'.$m['module_csssuf'].'" style="width:100%;">
	'.$title_out.'
	<div id="scroll_main">
		<div id="scroll_prev"></div>
		<div id="scroll_next"></div>
		<div id="scroll_frame">'.$images_out.'</div>
	</div>
</div>
';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_scroll_images" data-id="'.$m['id'].'">'.$out.'</div>';}
else {echo $out;}		
		
?>
