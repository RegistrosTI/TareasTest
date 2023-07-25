<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario   =$_GET["usuario"];


DBConectar($GLOBALS["cfg_DataBase"]);

$insert ="
		
	INSERT INTO [GestionPDT].[dbo].[Feedback_Preguntas] (
		Usuario
		,FechaAlta
	)
	VALUES(
		'$usuario'
		,GETDATE()
	)
";

//die($insert);

DBSelect(utf8_decode($insert));

DBFree($insert);	
DBClose();
?>
