<?php
// DAN обновление - январь 2014
// выводит товар шаблон корзины
defined('AUTH') or die('Restricted access');

// если 'оплата' включёна в настройках ИМ
if ($shopSettings->getValue('payment') == '1')
{
	$button = '<input type="submit" value="'.LANG_PAYMENT.'" class="shop-button" name="shopbutton"/>';
	$url = 'http://'.$site.'/shop/basket/pay';
}
else
{
	$button = '<input type="submit" value="'.LANG_CHECKOUT.'" class="shop-button" name="shopbutton"/>';
	$url = 'http://'.$site.'/shop/basket/mail';
}


if($kolTovarov > 0) // есть заказ
{
	$agreement = '';
	if($shopSettings->getValue('agreement') == 1)
	{
		$agreement = '
			<tr>
				<td colspan="2">
					<label><input type="checkbox" required /> '.$shopSettings->getValue('agreement_text').'</label>
				</td>
			</tr>
		';
	}

	// шапка корзины
	echo
	'
	<form method="POST" action="'.$url.'">
		<div class="main-right-header-1"></div>
		<div class="main-right-header-2">
			<div class="shop-item-title-2">'.LANG_BASKET.'</div>
			<div class="basket-item">
				<table class="basket-item-tab">
					<tr>
						<td class="basket-item-title"><b>'.LANG_PRODUCT_NAME.'</b></td>
						<td class="basket-item-klv"><b>'.LANG_COUNT.'</b></td>
						<td class="basket-item-price"><b>'.LANG_PRICE.', '.$shopSettings->getValue('currency').'</b></td>
						<td class="basket-item-summa"><b>'.LANG_SUM.'</b></td>
					</tr>
	'.$basket_middle.'
				</table>
				<div class="w-sep"></div>
				<div class="shop-item-price-3">'.LANG_TOTAL.': <span id="summa">'.$summa.'</span> '.$shopSettings->getValue('currency').'</div>
				<div class="w-sep"></div>
				<table class="basket-item-tab">
					<tr>
						<td class="basket-client-text"><span class="fr_10"><b>'.LANG_CONTACT_DETAILS.':</b></span></td>
						<td class="basket-client-input">&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_FULL_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="fio" size="50" value="'.$fio.'" ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PHONE.':</span></td>
						<td class="basket-client-input"><input type="text" name="tel" size="50" value="'.$tel.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_EMAIL.':</span></td>
						<td class="basket-client-input"><input type="email" name="email" size="50" value="'.$email.'"></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_ADDRESS.':</span></td>
						<td class="basket-client-input"><input type="text" name="address" size="50" value="'.$address.'" ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_COMMENT.':</span></td>
						<td class="basket-client-input"><textarea rows="5" name="comments" cols="39">'.$comments.'</textarea></td>
					</tr>
					'.$agreement.'
				</table>
				<div class="w-sep"></div>
				<div id="test"></div>
	';

	if ($shopSettings->getValue('contract') == '1')
	{
		echo
		'
				<script type="text/javascript">
					function lico_type(lt)
					{
						if (lt == 2)
						{
							document.getElementById("ur_lico").innerHTML = ur_lico_out;
							document.getElementById("fiz_lico").innerHTML = "";
						}
						else
						{
							document.getElementById("ur_lico").innerHTML = "";
							document.getElementById("fiz_lico").innerHTML = fiz_lico_out;
						}

					}
				</script>

				<table class="basket-item-tab">
					<tr>
						<td  colspan="2" class="basket-client-text"><b>'.LANG_DATA_FOR_CONTRACT.':</b></td>
					</tr>
					<tr>
						<td class="basket-client-text basket-client-fizlico"><span class="fr_10">'.LANG_PHYSICAL_PERSON.':</span></td>
						<td class="basket-client-input basket-client-fizlico"><input type="radio" value="1" '.$lico_check_1.' name="lico" onclick="lico_type(1)"></td>
					</tr>
					<tr>
						<td class="basket-client-text basket-client-urlico"><span class="fr_10">'.LANG_LEGAL_PERSON.':</span></td>
						<td class="basket-client-input basket-client-urlico"><input type="radio" value="2" '.$lico_check_2.' name="lico" onclick="lico_type(2)"></td>
					</tr>
				</table>
				<div class="w-sep"></div>

				<div id="fiz_lico">
				<table class="basket-item-tab basket-client-fizlico">
					<tr>
						<td  colspan="2" class="basket-client-text"><b>'.LANG_PHYSICAL_PERSON.':</b></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_LAST_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_f" size="50" value="'.$fiz_f.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_FIRST_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_i" size="50" value="'.$fiz_i.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_MIDLE_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_o" size="50" value="'.$fiz_o.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_DATE_BIRTH.':</span></td>
						<td class="basket-client-input">
							<select name="fiz_dr" >
								<option value="">'.LANG_DAY.'</option>
		';

		for ($i = 1; $i <= 31; $i++)
		{
			echo '<option '.$fiz_dr_selected[$i].' value="'.$i.'">'.$i.'</option>
	';
		}

		echo '
							</select>
							<select name="fiz_mr" class="validate[required]">
								<option value="">'.LANG_MONTH.'</option>
								<option '.$fiz_mr_selected[1].' value="1">'.LANG_JANUARY.'</option>
								<option '.$fiz_mr_selected[2].' value="2">'.LANG_FEBRUARY.'</option>
								<option '.$fiz_mr_selected[3].' value="3">'.LANG_MARCH.'</option>
								<option '.$fiz_mr_selected[4].' value="4">'.LANG_APRIL.'</option>
								<option '.$fiz_mr_selected[5].' value="5">'.LANG_MAY.'</option>
								<option '.$fiz_mr_selected[6].' value="6">'.LANG_JUNE.'</option>
								<option '.$fiz_mr_selected[7].' value="7">'.LANG_JULY.'</option>
								<option '.$fiz_mr_selected[8].' value="8">'.LANG_AUGUST.'</option>
								<option '.$fiz_mr_selected[9].' value="9">'.LANG_SEPTEMBER.'</option>
								<option '.$fiz_mr_selected[10].' value="10">'.LANG_OCTOBER.'</option>
								<option '.$fiz_mr_selected[11].' value="11">'.LANG_NOVEMBER.'</option>
								<option '.$fiz_mr_selected[12].' value="12">'.LANG_DECEMBER.'</option>
							</select>
							<select name="fiz_gr">
								<option value="">'.LANG_YEAR.'</option>
		';

		for ($i = 1940; $i <= 2000; $i++)
		{
			echo '<option '.$fiz_gr_selected[$i].' value="'.$i.'">'.$i.'</option>
	';
		}

		echo '
							</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><b>'.LANG_PASSPORT.':</b></td>
						<td class="basket-client-input"></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PASSPORT_SERIES.':</span></td>
						<td class="basket-client-input"><input type="number" min="1000" max="9999" maxlength="4" name="fiz_pasportseries" size="4" required value="'.$fiz_pasportseries.'">&nbsp;&nbsp;&nbsp; '.LANG_PASSPORT_NUMBER.': <input type="text"  min="100000" max="999999" size="6" maxlength="6" name="fiz_pasportnumber" size="6" required value="'.$fiz_pasportnumber.'"></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PASSPORT_ISSUED.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_kemvidanpassport" size="50" value="'.$fiz_kemvidanpassport.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PASSPORT_ISSUED_DATE.':</span></td>
						<td class="basket-client-input">
							<select name="fiz_dv" >
								<option value="">'.LANG_DAY.'</option>
		';

		for ($i = 1; $i <= 31; $i++)
		{
			echo '<option '.$fiz_dv_selected[$i].' value="'.$i.'">'.$i.'</option>
	';
		}

		echo '
							</select>
							<select name="fiz_mv" class="validate[required]">
								<option value="">'.LANG_MONTH.'</option>
								<option '.$fiz_mv_selected[1].' value="1">'.LANG_JANUARY.'</option>
								<option '.$fiz_mv_selected[2].' value="2">'.LANG_FEBRUARY.'</option>
								<option '.$fiz_mv_selected[3].' value="3">'.LANG_MARCH.'</option>
								<option '.$fiz_mv_selected[4].' value="4">'.LANG_APRIL.'</option>
								<option '.$fiz_mv_selected[5].' value="5">'.LANG_MAY.'</option>
								<option '.$fiz_mv_selected[6].' value="6">'.LANG_JUNE.'</option>
								<option '.$fiz_mv_selected[7].' value="7">'.LANG_JULY.'</option>
								<option '.$fiz_mv_selected[8].' value="8">'.LANG_AUGUST.'</option>
								<option '.$fiz_mv_selected[9].' value="9">'.LANG_SEPTEMBER.'</option>
								<option '.$fiz_mv_selected[10].' value="10">'.LANG_OCTOBER.'</option>
								<option '.$fiz_mv_selected[11].' value="11">'.LANG_NOVEMBER.'</option>
								<option '.$fiz_mv_selected[12].' value="12">'.LANG_DECEMBER.'</option>
							</select>
							<select name="fiz_gv">
								<option value="">'.LANG_YEAR.'</option>
		';

		for ($i = 2000; $i <= 2013; $i++)
		{
			echo '<option '.$fiz_gv_selected[$i].' value="'.$i.'">'.$i.'</option>
	';
		}

		echo '
							</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PASSPORT_REGISTRATION.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_propiska" size="50" value="'.$fiz_propiska.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><b>'.LANG_ADRESS_POST.':</b></td>
						<td class="basket-client-input"></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_INDEX.':</span></td>
						<td class="basket-client-input"><input type="number" min="100000" max="999999" name="fiz_indeks" size="6" value="'.$fiz_indeks.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_REALM.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_oblast" size="50" value="'.$fiz_oblast.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_CITY.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_gorod" size="50" value="'.$fiz_gorod.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_ADRESS.':</span></td>
						<td class="basket-client-input"><input type="text" name="fiz_adres" size="50" value="'.$fiz_adres.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<div class="w-sep"></div>
			</div>


			<div id="ur_lico">
				<table class="basket-item-tab basket-client-urlico">
					<tr>
						<td  colspan="2" class="basket-client-text"><b>'.LANG_LEGAL_PERSON.':</b></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_COMPANY_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_naimenovanie" size="50" value="'.$ur_naimenovanie.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_INN.':</span></td>
						<td class="basket-client-input"><input type="number" name="ur_inn" size="20" value="'.$ur_inn.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_KPP.':</span></td>
						<td class="basket-client-input"><input type="number" name="ur_kpp" size="20" value="'.$ur_kpp.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_OGRN.':</span></td>
						<td class="basket-client-input"><input type="number" name="ur_ogrn" size="20" value="'.$ur_ogrn.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><b>'.LANG_LEGAL_ADRESS.':</b></td>
						<td class="basket-client-input"></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_INDEX.':</span></td>
						<td class="basket-client-input"><input type="number" min="100000" max="999999" name="ur_urindeks" size="6" value="'.$ur_urindeks.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_REALM.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_uroblast" size="50" value="'.$ur_uroblast.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_CITY.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_urgorod" size="50" value="'.$ur_urgorod.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_ADRESS.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_uradres" size="50" value="'.$ur_uradres.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="basket-client-text"><b>'.LANG_REAL_ADRESS.':</b></td>
						<td class="basket-client-input"></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_INDEX.':</span></td>
						<td class="basket-client-input"><input type="number" min="100000" max="999999" name="ur_faktindeks" size="6" value="'.$ur_faktindeks.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_REALM.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_faktoblast" size="50" value="'.$ur_faktoblast.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_CITY.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_faktgorod" size="50" value="'.$ur_faktgorod.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_ADRESS.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_faktadres" size="50" value="'.$ur_faktadres.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_PHONE.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_fakttel" size="50" value="'.$ur_fakttel.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_EMAIL.':</span></td>
						<td class="basket-client-input"><input type="email" name="ur_faktemail" size="50" value="'.$ur_faktemail.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td  colspan="2" class="basket-client-text"><b>'.LANG_DIRECTOR.':</b></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_LAST_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_direktor_f" size="50" value="'.$ur_direktor_f.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_FIRST_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_direktor_i" size="50" value="'.$ur_direktor_i.'" required ></td>
					</tr>
					<tr>
						<td class="basket-client-text"><span class="fr_10">'.LANG_MIDLE_NAME.':</span></td>
						<td class="basket-client-input"><input type="text" name="ur_direktor_o" size="50" value="'.$ur_direktor_o.'" required ></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<div class="w-sep"></div>
			</div>


		';
	}



	echo'
			</div>
			<div class="shop-but" >'.$button.'</div>
		</div>
		<div>&nbsp;</div>

		<script type="text/javascript">
			if(document.getElementById("fiz_lico") && document.getElementById("ur_lico"))
			{
				var fiz_lico_out = document.getElementById("fiz_lico").innerHTML;
				var ur_lico_out = document.getElementById("ur_lico").innerHTML;
				lico_type(1);
			}
		</script>

	</form>
	';
}
else // нет заказа
{
	echo'
		<div class="main-right-header-1"></div>
		<div class="main-right-header-2">
			<div class="shop-item-title-2">'.LANG_BASKET_IS_EMPTY.'</div>
			<div class="basket-item">'.LANG_YOU_SHOULD_ADD_ITEMS.'</div>
		</div>
		';
}

?>
