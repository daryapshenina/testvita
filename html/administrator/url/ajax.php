<?php
// Проверка ЧПУ на ajax
include("../../config.php");
include("../../lib/lib.php");

// ======= MySQL =======================================================
$db_host = $host;
$db_name = $dbname;
$db_user = $user;
$db_password = $passwd;

$db_dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$db_opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"	
);
$db = new PDO($db_dsn, $db_user, $db_password, $db_opt);
// ======= / MySQL =====================================================


$sef = $_GET["sef"];

// проверяем на символы
if (!preg_match("/^[a-z0-9-_\/]{0,255}$/is",$sef))
{
	$sef_err = 1;
	echo '<div><font color="#FF0000">Поле заполнено не правильно и будет исправлено автоматически. Разрешается использовать только английские буквы, цифры и сивмолы -_/ без пробела. </font></div>';
}
else
{
	if(classvalidation::checkReservedWord($sef))
	{
		echo '<div><font color="#FF0000">Слово <b>'.$matches[0].'</b> занято системой управления для автоматической генерации url. Во избежание путаницы - его нельзя использовать в ручном режиме.</font></div>';
	}
	else
	{
		$stmt_url = $db->prepare("SELECT * FROM url WHERE sef = :sef AND sef <> ''");
		$stmt_url->execute(array('sef' => $sef));

		if($stmt_url->rowCount() > 0)
		{
			echo '<div><font color="#FF0000">Занято для страницы <a target="_blank" href="/'.$sef.'"><b>/'.$sef.'</b></a></font></div>';
		}
		else
		{
			if ($sef == '')
			{
				echo '<div><font color="#007800">URL будет сгенерирован автоматически</font></div>';
			}
			else
			{
				echo '<div><font color="#007800">Свободно</font></div>';
			}
		}
	}
}

?>
