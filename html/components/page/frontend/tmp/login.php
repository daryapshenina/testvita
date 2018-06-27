<?php
// DAN 2010
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

include_once __DIR__.'/../lang/'.LANG.'.php';

if (strlen($page_item['title']) > 0)
{
	echo'
		<h1 class="title">'.$page_item['title'].'</h1>
	';
}

echo '
	<table class="main-tab">
		<tr>
			<td class="w30pc-h220px">&nbsp;</td>
			<td class="w40pc-h220px">
				<div align="center">
					<table class="main-tab-lg">
						<tr>
							<td class="w120px-h80px">&nbsp;</td>
							<td class="vhod">'.LANG_PAGE_ENTER_PASSWORD.'</td>
							<td >&nbsp;</td>
						</tr>
						<tr>
							<td class="w120px-h120px">&nbsp;</td>
							<td class="logintext">
								<form method="post" action="'.$url.'">
									'.LANG_PAGE_TO_VIEW_PAGE.'
									<div>&nbsp;</div>
									'.LANG_PAGE_PASSWORD.'<br/>
									<input class="inp" name="pass_in" type="password" size="20"/>
									<div>&nbsp;</div>
									'.LANG_PAGE_ENTER_NUMBER.'<br/>
									<img src="/administrator/captcha/pic.php" align="middle">
									<input class="inp" type="text" name="cod" size="4" maxlength="4">
									<div>&nbsp;</div>
									<input class="cursor-pointer" type="submit" value="'.LANG_PAGE_ENTER.'" name="but"/>
									<div>&nbsp;</div>
								</form>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="h40px">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</td>
			<td class="w30pc-h220px">&nbsp;</td>
		</tr>
	</table>
';


?>