<?php
// DAN 2012
// Профиль пользователя
defined('AUTH') or die('Restricted access');

$country_arr[1]  = 'Россия';
$country_arr[2]  = 'Украина';
$country_arr[3]  = 'Беларусь';
$country_arr[4]  = 'Казахстан';
$country_arr[5]  = 'Азербайджан';
$country_arr[6]  = 'Армения';
$country_arr[7]  = 'Грузия';
$country_arr[8]  = 'Кыргызстан';
$country_arr[9]  = 'Молдова';
$country_arr[10]  = 'Таджикистан';
$country_arr[11]  = 'Туркменистан';
$country_arr[12]  = 'Узбекистан';
$country_arr[13]  = 'Австралия';
$country_arr[14]  = 'Австрия';
$country_arr[15]  = 'Албания';
$country_arr[16]  = 'Алжир';
$country_arr[17]  = 'Американское Самоа';
$country_arr[18]  = 'Ангилья';
$country_arr[19]  = 'Ангола';
$country_arr[20]  = 'Андорра';
$country_arr[21]  = 'Антигуа и Барбуда';
$country_arr[22]  = 'Аргентина';
$country_arr[23]  = 'Аруба';
$country_arr[24]  = 'Афганистан';
$country_arr[25]  = 'Багамы';
$country_arr[26]  = 'Бангладеш';
$country_arr[27]  = 'Барбадос';
$country_arr[28]  = 'Бахрейн';
$country_arr[29]  = 'Белиз';
$country_arr[30]  = 'Бельгия';
$country_arr[31]  = 'Бенин';
$country_arr[32]  = 'Бермуды';
$country_arr[33]  = 'Болгария';
$country_arr[34]  = 'Боливия';
$country_arr[35]  = 'Бонайре, Синт-Эстатиус и Саба';
$country_arr[36]  = 'Босния и Герцеговина';
$country_arr[37]  = 'Ботсвана';
$country_arr[38]  = 'Бразилия';
$country_arr[39]  = 'Бруней-Даруссалам';
$country_arr[40]  = 'Буркина-Фасо';
$country_arr[41]  = 'Бурунди';
$country_arr[42]  = 'Бутан';
$country_arr[43]  = 'Вануату';
$country_arr[44]  = 'Ватикан';
$country_arr[45]  = 'Великобритания';
$country_arr[46]  = 'Венгрия';
$country_arr[47]  = 'Венесуэла';
$country_arr[48]  = 'Виргинские острова, Британские';
$country_arr[49]  = 'Виргинские острова, США';
$country_arr[50]  = 'Восточный Тимор';
$country_arr[51]  = 'Вьетнам';
$country_arr[52]  = 'Габон';
$country_arr[53]  = 'Гаити';
$country_arr[54]  = 'Гайана';
$country_arr[55]  = 'Гамбия';
$country_arr[56]  = 'Гана';
$country_arr[57]  = 'Гваделупа';
$country_arr[58]  = 'Гватемала';
$country_arr[59]  = 'Гвинея';
$country_arr[60]  = 'Гвинея-Бисау';
$country_arr[61]  = 'Германия';
$country_arr[62]  = 'Гибралтар';
$country_arr[63]  = 'Гондурас';
$country_arr[64]  = 'Гонконг';
$country_arr[65]  = 'Гренада';
$country_arr[66]  = 'Гренландия';
$country_arr[67]  = 'Греция';
$country_arr[68]  = 'Гуам';
$country_arr[69]  = 'Дания';
$country_arr[70]  = 'Джибути';
$country_arr[71]  = 'Доминика';
$country_arr[72]  = 'Доминиканская Республика';
$country_arr[73]  = 'Египет';
$country_arr[74]  = 'Замбия';
$country_arr[75]  = 'Западная Сахара';
$country_arr[76]  = 'Зимбабве';
$country_arr[77]  = 'Израиль';
$country_arr[78]  = 'Индия';
$country_arr[79]  = 'Индонезия';
$country_arr[80]  = 'Иордания';
$country_arr[81]  = 'Ирак';
$country_arr[82]  = 'Иран';
$country_arr[83]  = 'Ирландия';
$country_arr[84]  = 'Исландия';
$country_arr[85]  = 'Испания';
$country_arr[86]  = 'Италия';
$country_arr[87]  = 'Йемен';
$country_arr[88]  = 'Кабо-Верде';
$country_arr[89]  = 'Камбоджа';
$country_arr[90]  = 'Камерун';
$country_arr[91]  = 'Канада';
$country_arr[92]  = 'Катар';
$country_arr[93]  = 'Кения';
$country_arr[94]  = 'Кипр';
$country_arr[95]  = 'Кирибати';
$country_arr[96]  = 'Китай';
$country_arr[97]  = 'Колумбия';
$country_arr[98]  = 'Коморы';
$country_arr[99]  = 'Конго';
$country_arr[100]  = 'Конго, демократическая республика';
$country_arr[101]  = 'Коста-Рика';
$country_arr[102]  = 'Кот д`Ивуар';
$country_arr[103]  = 'Куба';
$country_arr[104]  = 'Кувейт';
$country_arr[105]  = 'Кюрасао';
$country_arr[106]  = 'Лаос';
$country_arr[107]  = 'Латвия';
$country_arr[108]  = 'Лесото';
$country_arr[109]  = 'Либерия';
$country_arr[110]  = 'Ливан';
$country_arr[111]  = 'Ливия';
$country_arr[112]  = 'Литва';
$country_arr[113]  = 'Лихтенштейн';
$country_arr[114]  = 'Люксембург';
$country_arr[115]  = 'Маврикий';
$country_arr[116]  = 'Мавритания';
$country_arr[117]  = 'Мадагаскар';
$country_arr[118]  = 'Макао';
$country_arr[119]  = 'Македония';
$country_arr[120]  = 'Малави';
$country_arr[121]  = 'Малайзия';
$country_arr[122]  = 'Мали';
$country_arr[123]  = 'Мальдивы';
$country_arr[124]  = 'Мальта';
$country_arr[125]  = 'Марокко';
$country_arr[126]  = 'Мартиника';
$country_arr[127]  = 'Маршалловы Острова';
$country_arr[128]  = 'Мексика';
$country_arr[129]  = 'Микронезия, федеративные штаты';
$country_arr[130]  = 'Мозамбик';
$country_arr[131]  = 'Монако';
$country_arr[132]  = 'Монголия';
$country_arr[133]  = 'Монтсеррат';
$country_arr[134]  = 'Мьянма';
$country_arr[135]  = 'Намибия';
$country_arr[136]  = 'Науру';
$country_arr[137]  = 'Непал';
$country_arr[138]  = 'Нигер';
$country_arr[139]  = 'Нигерия';
$country_arr[140]  = 'Нидерланды';
$country_arr[141]  = 'Никарагуа';
$country_arr[142]  = 'Ниуэ';
$country_arr[143]  = 'Новая Зеландия';
$country_arr[144]  = 'Новая Каледония';
$country_arr[145]  = 'Норвегия';
$country_arr[146]  = 'Объединенные Арабские Эмираты';
$country_arr[147]  = 'Оман';
$country_arr[147]  = 'Остров Мэн';
$country_arr[149]  = 'Остров Норфолк';
$country_arr[150]  = 'Острова Кайман';
$country_arr[151]  = 'Острова Кука';
$country_arr[152]  = 'Острова Теркс и Кайкос';
$country_arr[153]  = 'Пакистан';
$country_arr[154]  = 'Палау';
$country_arr[155]  = 'Палестинская автономия';
$country_arr[156]  = 'Панама';
$country_arr[157]  = 'Папуа - Новая Гвинея';
$country_arr[158]  = 'Парагвай';
$country_arr[159]  = 'Перу';
$country_arr[160]  = 'Питкерн';
$country_arr[161]  = 'Польша';
$country_arr[162]  = 'Португалия';
$country_arr[163]  = 'Пуэрто-Рико';
$country_arr[164]  = 'Реюньон';
$country_arr[165]  = 'Руанда';
$country_arr[166]  = 'Румыния';
$country_arr[167]  = 'США';
$country_arr[168]  = 'Сальвадор';
$country_arr[169]  = 'Самоа';
$country_arr[170]  = 'Сан-Марино';
$country_arr[171]  = 'Сан-Томе и Принсипи';
$country_arr[172]  = 'Саудовская Аравия';
$country_arr[173]  = 'Свазиленд';
$country_arr[174]  = 'Святая Елена';
$country_arr[175]  = 'Северная Корея';
$country_arr[176]  = 'Северные Марианские острова';
$country_arr[177]  = 'Сейшелы';
$country_arr[178]  = 'Сенегал';
$country_arr[179]  = 'Сент-Винсент';
$country_arr[180]  = 'Сент-Китс и Невис';
$country_arr[181]  = 'Сент-Люсия';
$country_arr[182]  = 'Сент-Пьер и Микелон';
$country_arr[183]  = 'Сербия';
$country_arr[184]  = 'Сингапур';
$country_arr[185]  = 'Синт-Мартен';
$country_arr[186]  = 'Сирийская Арабская Республика';
$country_arr[187]  = 'Словакия';
$country_arr[188]  = 'Словения';
$country_arr[189]  = 'Соломоновы Острова';
$country_arr[190]  = 'Сомали';
$country_arr[191]  = 'Судан';
$country_arr[192]  = 'Суринам';
$country_arr[193]  = 'Сьерра-Леоне';
$country_arr[194]  = 'Таиланд';
$country_arr[195]  = 'Тайвань';
$country_arr[196]  = 'Танзания';
$country_arr[197]  = 'Того';
$country_arr[198]  = 'Токелау';
$country_arr[199]  = 'Тонга';
$country_arr[200]  = 'Тринидад и Тобаго';
$country_arr[201]  = 'Тувалу';
$country_arr[202]  = 'Тунис';
$country_arr[203]  = 'Турция';
$country_arr[204]  = 'Уганда';
$country_arr[205]  = 'Уоллис и Футуна';
$country_arr[206]  = 'Уругвай';
$country_arr[207]  = 'Фарерские острова';
$country_arr[208]  = 'Фиджи';
$country_arr[209]  = 'Филиппины';
$country_arr[210]  = 'Финляндия';
$country_arr[211]  = 'Фолклендские острова';
$country_arr[212]  = 'Франция';
$country_arr[213]  = 'Французская Гвиана';
$country_arr[214]  = 'Французская Полинезия';
$country_arr[215]  = 'Хорватия';
$country_arr[216]  = 'Центрально-Африканская Республика';
$country_arr[217]  = 'Чад';
$country_arr[218]  = 'Черногория';
$country_arr[219]  = 'Чехия';
$country_arr[220]  = 'Чили';
$country_arr[221]  = 'Швейцария';
$country_arr[222]  = 'Швеция';
$country_arr[223]  = 'Шпицберген и Ян Майен';
$country_arr[224]  = 'Шри-Ланка';
$country_arr[225]  = 'Эквадор';
$country_arr[226]  = 'Экваториальная Гвинея';
$country_arr[227]  = 'Эритрея';
$country_arr[228]  = 'Эстония';
$country_arr[229]  = 'Эфиопия';
$country_arr[230]  = 'Южная Корея';
$country_arr[231]  = 'Южно-Африканская Республика';
$country_arr[232]  = 'Южный Судан';
$country_arr[233]  = 'Ямайка';
$country_arr[234]  = 'Япония';

?>