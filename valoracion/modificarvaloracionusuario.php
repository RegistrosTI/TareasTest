<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$usuario = $_GET['usuario'];
$tipo    = $_GET['tipo'];
$valor   = $_GET['valor'];	
$tarea   = $_GET['tarea'];


DBConectar($GLOBALS["cfg_DataBase"]);

$insertar = "INSERT INTO [Valoraciones] ([Usuario],[Envio],[Tarea],[Fecha],[Valoracion],[Concepto],[Observacion]) SELECT '".$usuario."',null,".$tarea.",GETDATE(),[Numero],[Tipo],null
			FROM [Tipo_Valoraciones] WHERE Tipo = ".$tipo." AND Numero = ".$valor." AND Id <> 0	";
		   
$q=DBSelect(utf8_decode("DELETE FROM [Valoraciones] WHERE tarea = ".$tarea." AND Concepto = ".$tipo." AND Usuario = '".$usuario."'	".$insertar."  "));

DBFree($q);				
DBClose();

echo '';
?>