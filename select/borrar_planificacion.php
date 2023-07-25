<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario   =$_GET["usuario"];
$Id     =$_GET["Id"];


DBConectar($GLOBALS["cfg_DataBase"]);

$query = "
	SET DATEFORMAT dmy; 	
	UPDATE [GestionPDT].[dbo].[Planificador]
	SET
		Estado = 'Borrado'
		,Fecha_Cambio = GETDATE()
		,Usuario_Cambio = '$usuario'
	WHERE Id = $Id		;
";
//die($query);

DBSelect(utf8_decode($query));
DBFree($query);	
DBClose();
?>
