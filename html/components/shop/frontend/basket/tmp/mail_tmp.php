<?php
// DAN обновление - февраль 2014
// выводит товар в шаблоне корзины с отправкой на email

if($err_mail == 1)
{
	echo'<div class="shop-item-title-2">'.$err_mail_out.'</div>';
	exit;
}


// Если сумма равна 0
if($summa == 0)
{
	$summa = "-";
}



// шапка корзины
echo
'
	<div class="main-right-header-1"></div>
	<div class="main-right-header-2">
		<div class="shop-item-title-2">ВАШ ЗАКАЗ ПРИНЯТ '.$data.'</div><br />
		<div class="basket-item">
		<div style="padding:0px 0px 20px 0px">В ближайшее время менеджер нашей компании свяжется с Вами по телефону '.$tel.'.<br />На Ваш электронный адрес отправлено письмо с содержимым заказа.</div>
			<table class="basket-item-tab">
				<tr>
					<td class="basket-item-title"><b>Наименование</b></td>
					<td class="basket-item-klv"><b>Количество</b></td>
					<td class="basket-item-price"><b>Цена, '.$shop_settings->['currency'].'</b></td>
					<td class="basket-item-summa"><b>Сумма</b></td>
				</tr>
'.$basket_middle.'
				<tr>
					<td class="basket-item-title">&nbsp</td>
					<td class="basket-item-klv">&nbsp</td>
					<td class="basket-item-price"><b>Итого:</b></td>
					<td class="basket-item-summa"><div style="text-align:center; font-size:16px; font-weight:bold; color:#000000;">'.$summa.'</div></td>
				</tr>
			</table>
		</div>
		<div class="w-sep"></div>
		<div class="basket-item">
			<table class="basket-item-tab">
				<tr>
					<td class="basket-client-text"><b>ФИО:</b></td>
					<td class="basket-client-input">'.$fio.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Телефон контакта:</b></td>
					<td class="basket-client-input">'.$tel.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Email:</b></td>
					<td class="basket-client-input">'.$email_client.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Адрес доставки:</b></td>
					<td class="basket-client-input">'.$address.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Комментарии:</b></td>
					<td class="basket-client-input">'.$comments.'</td>
				</tr>
			</table>
		</div>
		<div class="w-sep"></div>
	</div>
	<div class="w-sep"></div>
	<div class="basket-item">
		<table class="basket-item-tab">
			<tr>
				<td class="basket-client-text"><b>'.LANG_PAY_METHOD.':</b></td>
				<td class="basket-client-input">'.$paymethod_type.'</td>
			</tr>
		</table>
	</div>
';



if ($shop_settings['contract'] == '1')
{
	// ------- Физическое лицо -------	
	if ($lico == 1)
	{
		$lico_out = '
			<table class="basket-item-tab basket-client-fizlico">
				<tr>
					<td  colspan="2" class="basket-client-text"><b>Физическое лицо:</b></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Фамилия:</span></td>
					<td class="basket-client-input">'.$fiz_f.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Имя:</span></td>
					<td class="basket-client-input">'.$fiz_i.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Отчество:</span></td>
					<td class="basket-client-input">'.$fiz_o.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Дата рождения:</span></td>
					<td class="basket-client-input">'.$fiz_dr.' '.$mes[$fiz_mr].' '.$fiz_gr.'
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Паспорт:</b></td>
					<td class="basket-client-input"></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Паспорт - серия:</span></td>
					<td class="basket-client-input">'.$fiz_pasportseries.' номер: '.$fiz_pasportnumber.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Кем выдан паспорт:</span></td>
					<td class="basket-client-input">'.$fiz_kemvidanpassport.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Дата выдачи паспорта:</span></td>
					<td class="basket-client-input">'.$fiz_dv.' '.$mes[$fiz_mv].' '.$fiz_gv.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Прописка:</span></td>
					<td class="basket-client-input">'.$fiz_propiska.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Почтовый адрес:</b></td>
					<td class="basket-client-input"></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Индекс:</span></td>
					<td class="basket-client-input">'.$fiz_indeks.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Область:</span></td>
					<td class="basket-client-input">'.$fiz_oblast.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Город:</span></td>
					<td class="basket-client-input">'.$fiz_gorod.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Адрес:</span></td>
					<td class="basket-client-input">'.$fiz_adres.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		';				
	}
	// ------- / физ. лицо / -------



	// ------- Юридическое лицо -------	
	if ($lico == 2)
	{
		$lico_out = '
			<table class="basket-item-tab basket-client-urlico">
				<tr>
					<td  colspan="2" class="basket-client-text"><b>Юридическое лицо:</b></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Наименование организации:</span></td>
					<td class="basket-client-input">'.$ur_naimenovanie.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">ИНН:</span></td>
					<td class="basket-client-input">'.$ur_inn.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">КПП:</span></td>
					<td class="basket-client-input">'.$ur_kpp.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">ОГРН:</span></td>
					<td class="basket-client-input">'.$ur_ogrn.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Юридический адрес:</b></td>
					<td class="basket-client-input"></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Индекс:</span></td>
					<td class="basket-client-input">'.$ur_urindeks.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Область:</span></td>
					<td class="basket-client-input">'.$ur_uroblast.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Город:</span></td>
					<td class="basket-client-input">'.$ur_urgorod.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Адрес:</span></td>
					<td class="basket-client-input">'.$ur_uradres.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="basket-client-text"><b>Фактический адрес:</b></td>
					<td class="basket-client-input"></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Индекс:</span></td>
					<td class="basket-client-input">'.$ur_faktindeks.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Область:</span></td>
					<td class="basket-client-input">'.$ur_faktoblast.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Город:</span></td>
					<td class="basket-client-input">'.$ur_faktgorod.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Адрес:</span></td>
					<td class="basket-client-input">'.$ur_faktadres.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Телефон:</span></td>
					<td class="basket-client-input">'.$ur_fakttel.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Email:</span></td>
					<td class="basket-client-input">'.$ur_faktemail.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td  colspan="2" class="basket-client-text"><b>Директор:</b></td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Фамилия:</span></td>
					<td class="basket-client-input">'.$ur_direktor_f.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Имя:</span></td>
					<td class="basket-client-input">'.$ur_direktor_i.'</td>
				</tr>
				<tr>
					<td class="basket-client-text"><span class="fr_10">Отчество:</span></td>
					<td class="basket-client-input">'.$ur_direktor_o.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		';
	}
	// ------- / юр. лицо / -------


	
	echo '
		<div class="w-sep"></div>
		<div class="basket-item">'.$lico_out.'</div>
	';
}
else
{
	$lico_out = '';
}

// ======= ШАБЛОН ДЛЯ EMAIL ========================================================

// шапка корзины
$basket_mail_header_tmp =
'
<div align="center"><b>ВАШ ЗАКАЗ ПРИНЯТ '.$data.'</b></div>
<div>&nbsp;</div>
<div align="center">В ближайшее время менеджер нашей компании свяжется с Вами по телефону '.$tel.'.<br />На Ваш электронный адрес отправлено письмо с содержимым заказа.</div>
<div>&nbsp;</div>
';

$basket_mail_tmp =
'<table style="width:600px; border-collapse:collapse; background-color:#ffffff">
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;"><b>Наименование</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:75px; vertical-align:middle; text-align:center;"><b>Количество</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:75px; vertical-align:middle; text-align:center;"><b>Цена, '.$shop_settings['currency'].'</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:75px; vertical-align:middle; text-align:center;"><b>Сумма</b></td>
	</tr>
'.$basket_middle.'
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;">&nbsp</td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:75px; vertical-align:middle; text-align:center;">&nbsp</td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:75px; vertical-align:middle; text-align:center;"><b>Итого:</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:75px; vertical-align:middle; text-align:center;"><div style="text-align:center; font-size:16px; font-weight:bold; color:#000000;">'.$summa.'</div></td>
	</tr>
</table>
<div>&nbsp;</div>
<table style="width:600px; border-collapse:collapse; background-color:#ffffff">
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:150px; vertical-align:middle;"><b>ФИО:</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;" >'.$fio.'</td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;"><b>Телефон контакта:</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;">'.$tel.'</td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;"><b>Email:</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;">'.$email_client.'</td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;"><b>Адрес доставки:</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;">'.$address.'</td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;"><b>Комментарии:</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;">'.$comments.'</td>
	</tr>
</table>
<table style="width:600px; border-collapse:collapse; background-color:#ffffff">
	<tr>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; width:150px; vertical-align:middle;"><b>'.LANG_PAY_METHOD.':</b></td>
		<td style="padding:10px; border:solid; border-width:1px; border-color:#dedede; vertical-align:middle;" >'.$paymethod_type.'</td>
	</tr>
</table>
<div>&nbsp;</div>
'.$lico_out.'
';

?>
