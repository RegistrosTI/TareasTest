<?php
if(file_exists("./conf/config.php"))
{		
	include_once "./php/funciones.php";
	include_once "./conf/config.php";
	include_once "./conf/config_".curPageName().".php";
	include_once "./../soporte/DB.php";
	include_once "./../soporte/funcionesgenerales.php";	
}
else
{
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_".curPageName().".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";	
}

$envio    =$_GET['envio'];
$contador =0;


DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("DELETE FROM [Valoraciones] WHERE Envio = ".$envio."	"));
DBFree($insert);
$insert=DBSelect(utf8_decode("DELETE FROM [Mail] WHERE Envio = ".$envio."	"));
DBFree($insert);

DBClose();
?>