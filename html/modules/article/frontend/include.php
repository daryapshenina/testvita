<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

if($frontend_edit == 1){$head->addFile('/modules/article/frontend/edit.js');}

function article_out($article, $modules_p4, $modules_p5, $modules_p6, $cd)
{
	global $domain, $url_arr;

	// если есть в массиве ЧПУ - заменяем
	if(isset($url_arr['article/item/'.$article['id']]) && $url_arr['article/item/'.$article['id']] != '')
	{
		$article_url = '/'.$url_arr['article/item/'.$article['id']];
	}
	else
	{
		$article_url = '/article/item/'.$article['id'];
	}


	// --- Выводим ---
	$article_out = '<div class="mod-article-container">';

	// выводим дату из бд и меняем её вид
	$article_cdate = substr($article['cdate'], 0, 10);
	$cdate = explode("-",$article_cdate);

	// выводим дату
	$article_out .= '<div class="mod-article-date">'.$cdate[2].' '.$cd[$cdate[1]].' '.$cdate[0].'</div>';

	if ($modules_p4 == '1')
	{
		$article_out .= '<div class="mod-article-title"><a href="'.$article_url.'">'.$article['title'].'</a></div>';
		$article_out .= '<div class="mod-article-intro" style="display:inline-block !important;" >'.$article['introtext'].'</div>';
	}
	else
	{
		$article_out .= '<div class="mod-article-title"><a href="'.$article_url.'">'.$article['title'].'</a></div>';
	}

	$article_out .= '<a class="mod-article-readmore" href="/article/item/'.$article['id'].'">'.LANG_MOD_ARTICLE_READ_MORE.' &#8594;</a>';
	$article_out .= '</div>';

	return $article_out;
}


?>
