<?php
namespace Modules\Article;
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

$title = $modules_title;
$title_pub = $modules_titlepub;
$suf_article = $modules_module_csssuf;

if ($modules_pub == "1") // Публикуем если включено
{
	$cd['01'] = LANG_MOD_ARTICLE_JANUARY;
	$cd['02'] = LANG_MOD_ARTICLE_FEBRUARY;
	$cd['03'] = LANG_MOD_ARTICLE_MARCH;
	$cd['04'] = LANG_MOD_ARTICLE_APRIL;
	$cd['05'] = LANG_MOD_ARTICLE_MAY;
	$cd['06'] = LANG_MOD_ARTICLE_JUNE;
	$cd['07'] = LANG_MOD_ARTICLE_JULY;
	$cd['08'] = LANG_MOD_ARTICLE_AUGUST;
	$cd['09'] = LANG_MOD_ARTICLE_SEPTEMBER;
	$cd['10'] = LANG_MOD_ARTICLE_OCTOBER;
	$cd['11'] = LANG_MOD_ARTICLE_NOVEMBER;
	$cd['12'] = LANG_MOD_ARTICLE_DECEMBER;

	// Заголовок модуля
	if ($title_pub == "1"){$title_out = '<div class="mod-title">'.$title.'</div>';} else{$title_out = '';}

	// Какое кол-во статей выводить
	$quantity_articles = $modules_p3;

	if((int)$modules_p2 > 0)
	{
		$settings_cat_arr = explode(';', $modules_p2);

		for($i = 0; $i < count($settings_cat_arr); $i++)
		{
			if($i == 0)
				$settings_category = " and (section = '".$settings_cat_arr[$i]."'";
			else
				$settings_category .= " OR section = '".$settings_cat_arr[$i]."'";
		}

		$settings_category .= ')';
	}
	else
	{
		$settings_category = '';
	}

	$section_link_out = '';
	$articles_out = '';

	// Считаем количество записей
	$article_query = $db->query('SELECT id FROM com_article_item WHERE pub = \'1\' '.$settings_category.'  ');
	$article_count = $article_query->rowCount();

	// Если статьи отсутствуют
	if($article_count == 0)
	{
		$articles_out .= '<div class="mod-article-intro" style="display:inline-block !important;" >'.LANG_MOD_ARTICLE_NO_ARTICLES.'</div>';
	}
	else
	{
		// Если 0 то выводим случайную статью
		if ($modules_p1 == '0')
		{
			// выбираем несколько случайных чисел
			for ($i = 0; $i < $quantity_articles ;$i++)
			{
				$row_r[$i] = round(rand(0, ($article_count - 1)));
				$aricle_query = $db->query('SELECT id, title, introtext, cdate FROM com_article_item WHERE pub = \'1\' '.$settings_category.' ORDER BY cdate DESC LIMIT '.$row_r[$i].',1');

				$article = $aricle_query->fetch();

				// Функция вывода тела статьи
				$articles_out .= article_out($article, $modules_p4, $modules_p5, $modules_p6, $cd);
			}

		}
		else // Иначе "1" - то последнии статьи
		{
			$aricle_query = $db->query('SELECT id, title, introtext, cdate FROM com_article_item WHERE pub = \'1\' '.$settings_category.' ORDER BY cdate DESC LIMIT '.$quantity_articles.' ');

			while($article = $aricle_query->fetch())
			{
				// Функция вывода тела статьи
				$articles_out .= article_out($article, $modules_p4, $modules_p5, $modules_p6, $cd);
			}
		}
	}

	// вывод линка "Все статьи"
	if (!isset($modules_p5) || $modules_p5 == 0 || $modules_p5 == '')
	{
		$section_link_out = '';
	}
	else
	{
		$section_link_out = '<div>&nbsp;</div>';
		$section_link_out .= '<div><a class="mod-article-section-link" href="/article/section/'.$modules_p5.'" rel="nofollow" title="'.LANG_MOD_ARTICLE_ALL_MATERIALS.'">'.$modules_p6.'</a></div>';
	}

	$out = '<div class="mod-main'.$suf_article.'">
		<div class="mod-top">'.$title_out.'</div>
		<div class="mod-mid">
			<div class="mod-padding">
			'.$articles_out.'
			'.$section_link_out.'
            </div>
     	</div>
		<div class="mod-bot'.$suf_article.'"></div></div>
	';


	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_article" data-id="'.$modules_id.'">'.$out.'</div>';}
	else {echo $out;}
}


?>