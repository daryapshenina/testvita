window.onload = function() {

	function getXmlHttp(){
	  var xmlhttp;
	  try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	  } catch (e) {
		try {
		  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
		  xmlhttp = false;
		}
	  }
	  if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
		xmlhttp = new XMLHttpRequest();
	  }
	  return xmlhttp;
	}
	
	var req = getXmlHttp();
	
	// ==== / Создаем окно чата =========================================================
	var bheight = 29; // изначальный размер окна
	var startchat; // переменная для хранения таймера, тут что бы была доступна не только в функции
	
	// Создаем окно
	var chat_box = document.createElement('div');
	chat_box.style.height = bheight+"px";
	chat_box.id = "chat_box";
	chat_box.innerHTML = '<div id="chat_open"></div>';
	document.body.appendChild(chat_box);
	
	// Создаем кнопку закрыть
	var chat_close = document.createElement('div');
	chat_close.id = "chat_close";
	chat_close.style.display = "none";
	chat_close.innerHTML = '';
	chat_box.appendChild(chat_close);
	
	// Создаем див для формы
	var chat_win = document.createElement('div');
	chat_win.id = "chat_win";
	chat_win.style.display = "none";
	chat_win.innerHTML = "<div name=\"text\" id=\"chat\"><div id=\"chat-p\">Оператор: Добрый день чем могу помочь?</div></div><textarea type=\"text\" rows=\"1\" id=\"mess\" placeholder=\"Введите своё сообщение\" autofocus></textarea><div id=\"chat_log\"></div><div id=\"send\"></div>";
	chat_box.appendChild(chat_win); // Добавляем форму чата
	// =====  окно чата =============================================
	
	
	// ==== / Окно чата - события ====================================================
	// Развертывание окна
	document.getElementById('chat_open').onclick = function fboxchat ()
	{
		chat_win.style.display = "block"; // отображаем форму чата
		
		if (bheight <= 390)
		{
			bheight += 10;
			chat_box.style.height = bheight+"px";
			setTimeout(fboxchat, 10);
		}
		else
		{
			document.getElementById('chat_open').style.display = "none"; // удаляем кнопку открыть
			chat_close.style.display = "block"; // добавляем кнопку закрыть
			fstartchat();
		}
	}
	
	// Свертывание окна
	chat_close.onclick = function fboxchatclose ()
	{
		if (bheight > 30)
		{
			bheight -= 10;
			chat_box.style.height = bheight+"px";
			setTimeout(fboxchatclose, 10);
		}
		else
		{
			clearInterval(startchat); // удаляем таймер
			chat_win.style.display = "none"; // скрываем форму чата
			chat_close.style.display = "none"; // удаляем кнопку закрыть
			document.getElementById('chat_open').style.display = "block"; // добавляем кнопку открыть
		}
	}
	// ==== события окна чата ==========================================================

	// Отправка сообщения по нажатию enter
	document.getElementById('mess').onkeydown = function ()
	{
		if (typeof event == "undefined")
		{
			event = window.event;
		}
		
		if (event.keyCode == 13)
		{
			fSend ();
		}
	};
	
	document.getElementById('send').onclick = fSend;
	
	// ==== / При клике по кнопке отправляем сообщение ============================================
	function fSend ()
	{
		// Получаем сод. поля ввода
		var mess = document.getElementById('mess').value;
		
		if (mess.length < 5)
		{
			document.getElementById('chat_log').innerHTML = "Сообщение слишком короткое";
		}
		else if (mess.length > 130)
		{
			document.getElementById('chat_log').innerHTML = "Сообщение слишком длинное";
		}
		else
		{
			document.getElementById('chat_log').innerHTML = "";
			
			req.open('POST', '/administrator/chat/frontend/sendmess.php', true);
			req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			req.send('mess='+encodeURIComponent(mess));
			
			req.onreadystatechange = function () 
			{
				if (req.readyState == 4) 
				{
					if (req.status == 200) 
					{
						// Очищаем поле ввода сообщения
						document.getElementById('mess').value = "";
						// Обновляем окно через секунду
						setTimeout(fGet, 1000);
					}
				}
			}
		}
	}
	// ==== / отправка сообщения =====================================================================
	
	
	// ==== / Выводим сообщения в окне ============================================
	function fGet ()
	{
		req.open('POST', '/administrator/chat/frontend/getmess.php', true);
		req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		req.send(null);
		
		req.onreadystatechange = function() 
		{
			if (req.readyState == 4) 
			{
				if (req.status == 200) 
				{
					// Вставляем в окно сообщения
					document.getElementById("chat-p").innerHTML = req.responseText;
				}
			}
		
		}
	}
	// ==== / вывод сообщений =====================================================================

	function fstartchat ()
	{
		fGet();
		startchat = setInterval(fGet, 10000);
	}
}