// отправляем данные на IMAGE_RESIZE
function img_files(_file)
{
	IMAGE_RESIZE.options.thumbnail = true;
	IMAGE_RESIZE.options.aspectRatio = '1:1';

	IMAGE_RESIZE.win(_file, function(){
		var thumbnail = document.getElementById("thumbnail");
		
		document.getElementById('scale').value = IMAGE_RESIZE.obj.scale;
		document.getElementById('x1').value = IMAGE_RESIZE.obj.x1;
		document.getElementById('x2').value = IMAGE_RESIZE.obj.x2;
		document.getElementById('y1').value = IMAGE_RESIZE.obj.y1;
		document.getElementById('y2').value = IMAGE_RESIZE.obj.y2;
		document.getElementById('thumbnail').src = IMAGE_RESIZE.obj.src;

		// Создаём уменьшенную копию		
		var scale_x = (IMAGE_RESIZE.obj.x2 - IMAGE_RESIZE.obj.x1) / 200;
		var scale_y = (IMAGE_RESIZE.obj.y2 - IMAGE_RESIZE.obj.y1) / 200;

		thumbnail.style.width = thumbnail.naturalWidth / scale_x + 'px';
		thumbnail.style.height = thumbnail.naturalHeight / scale_y + 'px';		
		thumbnail.style.marginLeft = '-' + (IMAGE_RESIZE.obj.x1 / scale_x) + 'px';
		thumbnail.style.marginTop = '-' + (IMAGE_RESIZE.obj.y1 / scale_y) + 'px';
	});
}


// Генерация пароля
function makeRand(max){return Math.floor(Math.random() * max);}
function generatePass(){
        var length = 10;
        var result = '';
        var symbols = new Array(
                                'q','w','e','r','t','y','u','i','o','p',
                                'a','s','d','f','g','h','j','k','l',
                                'z','x','c','v','b','n','m',
                                'Q','W','E','R','T','Y','U','I','O','P',
                                'A','S','D','F','G','H','J','K','L',
                                'Z','X','C','V','B','N','M',
                                1,2,3,4,5,6,7,8,9,0
        );
        for (i = 0; i < length; i++){result += symbols[makeRand(symbols.length)];}
        var psw = document.getElementById('account_password');
		psw.type = 'text';
		psw.value = result;
}