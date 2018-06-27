<?php
// Настройки сайта
defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $db, $domain, $root;

	// ------- УДАЛЯЕМ СТАРЫЕ ЗАПИСИ  -------------------------------------------------------
	$stmt_delete = $db->exec("DELETE FROM block WHERE id > '0'");	
	$stmt_ai = $db->exec("ALTER TABLE block AUTO_INCREMENT = 1");	
	// ------- / удаляем старые записи -------------------------------------------------------	
		
	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/tools.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Импорт блоков:</div>
		<div>&nbsp;</div>
		
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v"></td>
				<td class="cell-title-modules">Блок</td>
				<td class="cell-desc-modules">Описание</td>			
			</tr>
		</table>
		<table class="w100_bs1">			
	';	
	
	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	// загрузка XML-файла тем.
	$xmltemplate = simplexml_load_file($root.'/tmp/template.xml');
	
	if ($xmltemplate) 
	{	
		$i = 1;
		foreach ($xmltemplate->block as $b) 
		{	
			$name = $b->name;	
			$description = $b->description;
			
			
			// ------- ВСТАВЛЯЕМ ДАННЫЕ --------------------------------------------------------------
			$stmt_insert = $db->prepare("INSERT INTO block SET block = :block, description = :description");
			$stmt_insert->execute(array('block' => $name, 'description' => $description));			
			// ------- / вставляем данные ------------------------------------------------------------				
					
			echo '
				<tr>
					<td class="cell-v ">'.$i.'</td>
					<td class="cell-title-modules">'.$name.'</td>
					<td class="cell-desc-modules">'.$description.'</td>
				</tr>		
			';
			$i++;
		}
	}		
	// ======== / загрузка блоков вывода =======		

		
	// вывод параметров	
		echo'				
			</table>			
		';			

			
		
} // конец функции компонента
?>