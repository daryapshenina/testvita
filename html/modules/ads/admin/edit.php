<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT'].'/modules/ads/classes/modAds.php';
include_once($root.'/administrator/modules/classes/lang/'.LANG.'.php');

$head->addFile('/modules/ads/admin/style.css');


function a_com()
{
	global $db, $domain, $d;

	$mod_id  = intval($d[3]);

	$stmt = $db->prepare("SELECT pub, content, block, ordering FROM modules WHERE id = :id");
	$stmt->execute(array('id' => $mod_id));
	$mod = $stmt->fetch();

	$block_query = $db->query("SELECT * FROM block");

	$block_option = '';
	if($block_query->rowCount() > 0)
	{
		while($b = $block_query->fetch())
		{
			if ($b['block'] == $mod['block']){$block_selected = 'selected';} else {$block_selected = '';}
			$block_option .= '<option '.$block_selected.' value='.$b['block'].'>'.$b['description'].'</option>';
		}
	}

	$mod_s = unserialize($mod['content']);
	
	if($mod_s->title_pub) $title_pub_checked = 'checked';
		else $title_pub_checked = '';

	// устанавливаем признак публикации
	$pub_0 = $pub_1 = $pub_2 = $pub_3 = '';

	switch($mod['pub'])
	{
		case 0:
			$pub_0 = 'selected="selected"';
			break;

		case 1:
			$pub_1 = 'selected="selected"';
			break;

		case 2:
			$pub_2 = 'selected="selected"';
			break;

		case 3:
			$pub_3 = 'selected="selected"';
			break;
	}

	echo '
		<h1><img border="0" src="/modules/ads/admin/ico.png" style="float:left; margin-right:10px;" />Объявления</h1>
		<form enctype="multipart/form-data" method="POST" action="/admin/modules/ads/'.$mod_id.'/update">
			<table class="admin_table_2">
				<tr>
					<td>'.LANG_M_TITLE.'</td>
					<td><input class="input mod_title_input_checkbox" id="title_pub" name="title_pub" type="checkbox"  value="1" '.$title_pub_checked.' ><label for="title_pub" class="mod_title_label"></label><input class="input mod_title_input" type="text" name="title" size="50" value="'.$mod_s->title.'"></td>
				</tr>
				<tr>
					<td>'.LANG_M_VIEW_MODULE.'</td>
					<td>
						<select class="input" name="pub">
							<option value="1" '.$pub_1.'>'.LANG_M_VIEW_MODULE_ON.'</option>
							<option value="2" '.$pub_2.'>'.LANG_M_VIEW_MODULE_PC.'</option>
							<option value="3" '.$pub_3.'>'.LANG_M_VIEW_MODULE_PHONE.'</option>
							<option value="0" '.$pub_0.'>'.LANG_M_VIEW_MODULE_OFF.'</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_POSITION.'</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						<span class="block_text">&nbsp;'.LANG_M_POSITION_DESCRIPTION.'</div>
					</td>
				</tr>
				<tr>
					<td>'.LANG_M_ORDER.'</td>
					<td><input class="input" type="number" name="ordering" size="3" value="'.$mod['ordering'].'" style="width:80px;"></td>
				</tr>
				<tr>
					<td>Количество объявлений</td>
					<td><input class="input" type="number" name="quantity" size="3" value="'.$mod_s->quantity.'" style="width:80px;"></td>
				</tr>
			</table>
			<div style="margin-top:40px;">
				<input type="hidden" name="id" value="1" '.$mod_id.' >
				<input id="button_save" class="greenbutton" type="submit" value="'.LANG_M_SAVE.'" name="bt_save">&nbsp;<input id="button_apply" class="bluebutton" type="submit" value="'.LANG_M_ACCEPT.'" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_CANCEL.'" name="bt_none">
			</div>
		</form>
	</div>
	';

}

?>