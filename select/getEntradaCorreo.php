<?php
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_MENU.php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";

	DBConectar($GLOBALS["cfg_DataBase"]);
	
	$info = array();
	$busquedacorreos = DBSelect(utf8_decode("
																				SELECT 
																					COUNT(*) As Tareas 
																					,(SELECT COUNT(*) FROM [GestionPDT].[dbo].[Entrada_Correo] Where Estado = 'Pendiente' AND Prioridad= '2 - Importante') as Importantes
																				FROM [GestionPDT].[dbo].[Entrada_Correo] Where Estado = 'Pendiente'"));
	
	$Tareas = DBCampo ( $busquedacorreos , "Tareas" );
	$Importantes = DBCampo ( $busquedacorreos , "Importantes" );
	
	$info [] = array (
			"Tareas" => ($Tareas) ,
			"Importantes" => ($Importantes)
	);

	$php_json = json_encode ( $info );
	echo $php_json;

	DBClose ();
?>