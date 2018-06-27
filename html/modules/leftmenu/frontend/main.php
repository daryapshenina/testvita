<?php
// DAN 2014
// выводит пункты меню.
defined('AUTH') or die('Restricted access');

$modules_title_left = $modules_title;
$modules_titlepub_left = $modules_titlepub;
$suf_left = $modules_module_csssuf;


// если модуль опубликовани и разрешён
if ($modules_pub == '1')
{
	// верх модуля
	$out = '<div class="mod-main'.$suf_left.'">
	<div class="mod-top'.$suf_left.'">';

	// Заголовок модуля
	if ($modules_titlepub_left == "1")
	{
		$out .= '<div class="mod-title'.$suf_left.'">'.$modules_title_left.'</div>';
	}

	$out .= '</div>
		<div class="mod-mid'.$suf_left.'">
            <div class="mod-padding'.$suf_left.'">
	';



	// ======= ВЫВОД МЕНЮ =======================================================================
	// вывод обычного меню
	if (!isset($modules_p1) || $modules_p1 == "" || $modules_p1 == 0 || $modules_p1 == 1)
	{
		$out .= $menu->getMenu('left', 'tree', 0, 0);
	}

	// вывод свёрнутого меню
	if ($modules_p1 == 2)
	{
		$out .= $menu->getMenu('left', 'min', 0, 0);
	}

	// вывод аккордеоном
	if ($modules_p1 == 3)
	{
		$out .= '
		<script type="text/javascript">
			$(document).ready(function()
			{
				$("div.expand").show();
				$("#leftaccordion a.left_head").click(function()
				{
					$(this).next("div.left_body").slideToggle(300).siblings("div.left_body").slideUp("slow");
				});
			});
		</script>
		<div id ="leftaccordion">
		';

		$out .= $menu->getMenu('left', 'accordion', 0, 0);

		$out .= '
		</div>
		';
	}
	
	// вывод выдвигающегося меню
	if ($modules_p1 == 4)
	{
		$out .= '
		<script type="text/javascript">
			function menuleft_ext(parent_block, ev)
			{ 
				if(ev ==\'over\')
				{	
					parent_block.childNodes[1].style.display="block"; 
				}
				if(ev ==\'out\')
				{
					parent_block.childNodes[1].style.display="none";
				}				
			}	
		</script>
		';
		
		$out .= $menu->getMenu('left', 'extension', 0, 0);
	}
	
	// листом (интернет - магазин)
	if ($modules_p1 == 5)
	{
		$out .= '
		<script type="text/javascript">
		
			function menu_list(parent_block, ev)
			{
				if (document.body.offsetWidth > 500)
				{
					if(ev ==\'over\'){parent_block.childNodes[1].style.display="block";}
					if(ev ==\'out\'){parent_block.childNodes[1].style.display="none";}					
				}
			}
			
			function menu_list_click(parent_block, url)
			{
				if (document.body.offsetWidth < 501)
				{
					if(parent_block.childNodes[1].style.display == "block"){parent_block.childNodes[1].style.display = "none";}else{parent_block.childNodes[1].style.display = "block";}
				}
				else
				{
					document.location = "/" + url;
				}
			}

		</script>
		';		
		$out .= $menu->getMenu('left', 'list', 0, 0);
	}

	// ======= / вывод меню / ===================================================================



	$out .= '
            </div>
     	</div>
		<div class="mod-bot'.$suf_left.'"></div>
	</div>
	';

	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_leftmenu" data-id="'.$modules_id.'">'.$out.'</div>';}
	else {echo $out;}		

} // конец условия разрешения публикации модуля

?>