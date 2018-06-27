<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';

$head->addFile('/administrator/modules/add.css');

function a_com()
{
	global $root, $db, $domain;

	$settings = accountSettings::getInstance();

	echo '
		<div class="container">
			<h1>Добавить модуль</h1>
			<table class="admin_table even_odd">
			<tr>
				<th style="width:50px;" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
				<th style="width:250px;">МОДУЛИ САЙТА</td>
				<th>Описание модулей</td>

			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Содержимое</h3></td>
			</tr>
			<tr>
				<td class="mod_link editor_ico">&nbsp;</td>
				<td><a href="/admin/modules/editor/add">Редактируемый модуль</a></td>
				<td>Подключает область, которую можно редатировать в визальном редакторе</td>
			</tr>
			<tr>
				<td class="mod_link article_ico">&nbsp;</td>
				<td><a href="/admin/modules/article/add">Модуль статей / новостей</a></td>
				<td>Выводит последние новости или случайные статьи из архива статей</td>
			</tr>
			';

/*
			echo'
			<tr>
				<td class="mod_link spoiler_ico">&nbsp;</td>
				<td><a href="/admin/modules/spoiler/add">Спойлер</a></td>
				<td>При нажатии на кнопку - раскрывает / сворачивает содержимое модуля</td>
			</tr>
			';
*/
			echo '
			<tr>
				<td class="mod_link search_ico">&nbsp;</td>
				<td><a href="/admin/modules/search/add">Поиск по сайту</a></td>
				<td>Модуль, выводящий поиск по сайту</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Плашки</h3></td>
			</tr>
			<tr>
				<td class="mod_link flat_rotate_ico">&nbsp;</td>
				<td><a href="/admin/modules/flat_rotate/add">Плашка с вращением</a></td>
				<td>Плоская плашка с анимацией вращения</td>
			</tr>
			<tr>
				<td class="mod_link flat_shadow_button_ico">&nbsp;</td>
				<td><a href="/admin/modules/flat_shadow_button/add">Плашка с затемнением и кнопкой</a></td>
				<td>2 разных принципа работы с адаптивными шаблонами - фиксированная ширина и ширина в процентах</td>
			</tr>
			<tr>
				<td class="mod_link photo_s_ico">&nbsp;</td>
				<td><a href="/admin/modules/photo_s/add">Изображение с описанием</a></td>
				<td>При наведении - показывается квадрат с текстом</td>
			</tr>
			<tr>
				<td class="mod_link icon_ico">&nbsp;</td>
				<td><a href="/admin/modules/icon/add">Иконка</a></td>
				<td>Иконка в фомате UTF-8 c эффектом при наведении</td>
			</tr>
			<tr>
				<td class="mod_link circle_ico">&nbsp;</td>
				<td><a href="/admin/modules/circle/add">Круг</a></td>
				<td>Круг с анимацией при наведении</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Изображения</h3></td>
			</tr>
			<tr>
				<td class="mod_link scroll_images_ico">&nbsp;</td>
				<td><a href="/admin/modules/scroll_images/add">Скроллер</a></td>
				<td>Ряд изображений, движущихся вправо / влево</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Взаимодействие с клиентом</h3></td>
			</tr>
			<tr>
				<td class="mod_link form_ico">&nbsp;</td>
				<td><a href="/admin/modules/form/add">Форма обратной связи</a></td>
				<td>Редактируемая форма обратной связи</td>
			</tr>
			<tr>
				<td class="mod_link calltoorder_ico">&nbsp;</td>
				<td><a href="/admin/modules/calltoorder/add">Заказать звонок</a></td>
				<td>Модуль выводит поле для ввода номера телефона и имени</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Карты</h3></td>
			</tr>
			<tr>
				<td class="mod_link route_ico">&nbsp;</td>
				<td><a href="/admin/modules/route/add">Карта с маршрутом</a></td>
				<td>Карта с проложением маршрута от посетителя до адреса</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Пользователь</h3></td>
			</tr>
			<tr>
				<td class="mod_link authorization_ico">&nbsp;</td>
				<td><a href="/admin/modules/authorization/add">Авторизация</a></td>
				<td>Модуль авторизации / регистрации пользователя.</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Прочее</h3></td>
			</tr>
			<tr>
				<td class="mod_link exchangerates_ico">&nbsp;</td>
				<td><a href="/admin/modules/exchangerates/add">Курс валюты</a></td>
				<td>Курс с сайта Центрального Банка России</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>';

			if($settings->ads_allow)
			{
				echo '
				<tr>
					<td class="td_sep">&nbsp;</td>
					<td class="td_sep" colspan="2"><h3 class="modules_h3">КОМПОНЕНТЫ</h3></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2"><h3 class="modules_h3">Объявления</h3></td>
				</tr>
				<tr>
					<td class="mod_link ads_ico">&nbsp;</td>
					<td><a href="/admin/modules/ads/add">Объявления</a></td>
					<td>Показывает последние объявления</td>
				</tr>';
			}


/*
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Интернет - магазин</h3></td>
			</tr>
			<tr>
				<td class="mod_link shop_ico">&nbsp;</td>
				<td><a href="">Товары</a></td>
				<td>Вывод последних или случайных товаров</td>
			</tr>
			<tr>
				<td class="mod_link cart_ico">&nbsp;</td>
				<td><a href="">Корзина</a></td>
				<td>Модуль корзины интернет магазина</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Новости / статьи</h3></td>
			</tr>
			<tr>
				<td class="mod_link article_ico">&nbsp;</td>
				<td><a href="">Статьи</a></td>
				<td>Вывод последних или случайных статей</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Навигация</h3></td>
			</tr>
			<tr>
				<td class="mod_link topmenu_ico">&nbsp;</td>
				<td><a href="">Верхнее меню</a></td>
				<td>Верхнее меню</td>
			</tr>
			<tr>
				<td class="mod_link leftmenu_ico">&nbsp;</td>
				<td><a href="">Левое меню</a></td>
				<td>Левое меню</td>
			</tr>
			<tr>
				<td class="mod_link breadcrumbs_ico">&nbsp;</td>
				<td><a href="">Путь по сайту</a></td>
				<td>Модуль, выводящий путь по сайту</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Изображения</h3></td>
			</tr>
			<tr>
				<td class="mod_link slider_skitter_ico">&nbsp;</td>
				<td><a href="">Слайдер</a></td>
				<td>Анимация изображений</td>
			</tr>
			<tr>
				<td class="mod_link scroll_images_ico">&nbsp;</td>
				<td><a href="">Скроллер</a></td>
				<td>Ряд изображений, движущихся вправо / влево</td>
			</tr>
			<tr>
				<td class="mod_link img4r_ico">&nbsp;</td>
				<td><a href="">Адаптивные изображения 4х</a></td>
				<td>Адаптивный модуль 4х2/2х4/1х8 изображений</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Обратная связь</h3></td>
			</tr>
			<tr>
				<td class="mod_link form_ico">&nbsp;</td>
				<td><a href="">Форма обратной связи</a></td>
				<td>Модуль выводит форму обратной связи</td>
			</tr>
			<tr>
				<td class="mod_link calltoorder_ico">&nbsp;</td>
				<td><a href="">Заказать звонок</a></td>
				<td>Модуль выводит поле для ввода номера телефона, который отправляется на email</td>
			</tr>
			</tr>
				<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><h3 class="modules_h3">Прочее</h3></td>
			</tr>
			<tr>
				<td class="mod_link exchangerates_ico">&nbsp;</td>
				<td><a href="">Курс валют</a></td>
				<td>Курс доллара / евро с сайта Центрального Банка России</td>
			</tr>
			<tr>
				<td class="mod_link timer_ico">&nbsp;</td>
				<td><a href="">Таймер</a></td>
				<td>Счётчик обратного отсчёта</td>
			</tr>
*/
		echo '
			</table>
		</div>
	';

} // конец функции компонента
?>