<?php
header("HTTP/1.0 404 Not Found");
 
echo '
	<html>
	<head>
	<meta http-equiv="Content-Language" content="ru">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>404 Not Found</title>
	<meta http-equiv="refresh" content="1; url=/">
	</head>

	<body>

		<p align="center"><img border="0" src="settings/404.png"></p>
		<p align="center">&nbsp;</p>
		<p align="center"><b>Страница не найдена (404-я ошибка). </b></p>
		<p align="center">К сожалению, такой страницы не существует. <br>
						Вероятно, она была удалена автором с сервера, <br>
			либо её здесь никогда не было.</p>
		<p align="center">Нажмите <a href="/">сюда</a> для возврата на главную страницу</p>

	</body>
	</html>
';
