<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Usuario=$_GET["Usuario"];
$Id     =$_GET["Id"];


DBConectar($GLOBALS["cfg_DataBase"]);

$query = "	
	UPDATE [GestionPDT].[dbo].[Feedback_Preguntas]
	SET
		Borrada = 'SI'
		,FechaBorra = GETDATE()
		,UsuarioBorra = '$Usuario'
	WHERE Id = $Id		;
";
//die($query);

DBSelect(utf8_decode($query));
//DBFree($query);	
DBClose();
?>
