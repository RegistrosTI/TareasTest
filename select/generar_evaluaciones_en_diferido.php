<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$femFechaPeriodo				= $_POST["femFechaPeriodo"];
$femAutocompleteUsuarioDiferido	= $_POST["femAutocompleteUsuarioDiferido"];
$femOficinaDiferida	= $_POST["femOficinaDiferida"];


// Obtenemos el nombre del evaluador
$nomEvaluador = "";
$q = "
	EXEC [dbo].[INICIAR_EVALUACIONES_A_UNA_FECHA] 
		@FECHA = '$femFechaPeriodo'
		,@USUARIO = N'$femAutocompleteUsuarioDiferido'
		,@OFICINA = N'$femOficinaDiferida'
 ";
DBSelect ( utf8_decode ( $q ) );
	
DBClose();
?>