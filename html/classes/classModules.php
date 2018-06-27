<?php
// DAN 2015

defined('AUTH') or die('Restricted access');

class classModules
{
	static function view($_title, $_suffix, $_content)
	{
		if(strlen($_title) > 0)
		{
			$title = '<div class="mod-title'.$_suffix.'">'.$_title.'</div>';
		}

		echo '
			<div class="mod-main'.$_suffix.'">
				<div class="mod-top'.$_suffix.'">'.$title.'</div>
				<div class="mod-mid'.$_suffix.'">
					<div class="mod-padding'.$_suffix.'">
		';

		echo $_content;

		echo '
					</div>
				</div>
				<div class="mod-bot'.$suf_shop.'"></div>
			</div>
		';
	}
};

?>
