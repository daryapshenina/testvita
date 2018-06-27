<?php
defined('AUTH') or die('Restricted access');
include_once $root."/components/ads/classes/adsSectionItems.php";
include_once $root."/components/ads/frontend/section/tmp/tmp.php";
$head->addFile('/components/ads/frontend/section/tmp/section.css');
$head->addFile('/lib/css/font-awesome/css/font-awesome.min.css');
$head->addFile('/components/ads/frontend/my/tmp/my.css');

if(Auth::check())
{
	function component()
	{
		global $root, $db;

		$section_items = new adsSectionItems;

		$section_items->setPub(1);
		$section_items->setUser(Auth::check());		
		$section_items->setContent();
		$items = $section_items->getItems();


		$out = '';

		foreach($items as $item)
		{	
			if($item['image'] == 1)
			{
				$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
				$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];
				$item['image_out'] = '<img class="ads_image" alt="'.$item['title'].'" src="'.$path.'/'.$item['id'].'.jpg">';
			}
			else 
			{
				$item['image_out'] = '<img class="ads_image" alt="" style="width:50px;" src="/components/ads/frontend/my/tmp/nophoto.jpg">';
			}

			if(MobileDetector::getDevice())
			{
				$item['content_out'] = '<a class="ads_button ads_button_gray" href="/ads/my/edit/'.$item['id'].'">Редактировать</a><a class="ads_button ads_button_red" href="/ads/my/delete/'.$item['id'].'">Удалить</a>';
				$item['date_out'] = '';
			}
			else 
			{
				// Обрезаем текст по словам.
				if(mb_strlen($item['content']) > 300) $item['content'] = mb_substr($item['content'], 0, mb_strrpos(mb_substr($item['content'], 0, 300), ' ')).'...';
				$item['content_out'] = '<div class="ads_text">'.$item['content'].'<br><br><a class="ads_button ads_button_gray" href="/ads/my/edit/'.$item['id'].'">Редактировать</a><a class="ads_button ads_button_red" href="/ads/my/delete/'.$item['id'].'">Удалить</a></div>';

				$item['date_out'] = '<div class="ads_date_wrap">'.$item['date_c'].'</div>';
			}

			$out .= ads_section_item($item);
		}

		echo '<h1>Мои объявления</h1><div class="ads_add"><a class="button_green" href="/ads/my/add"><i class="ads_add_ico fa fa-plus" aria-hidden="true"></i>Добавить объявление</a></div>'.$out;
	}
}
else
{
	Header("Location: /account");
}


?>