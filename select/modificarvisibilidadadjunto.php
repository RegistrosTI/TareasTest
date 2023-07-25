<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     = $_GET["Id"];
$tipo   = $_GET["tipo"];
$Usuario= $_GET["usuario"];
$tarea  = $_GET["tarea"];

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(("UPDATE [Adjuntos] SET [Publico] = ".$tipo."  WHERE Numero = ".$id." "));

if ($tipo=="1")
{
	$tipo_realizado="41";
}
if ($tipo=="0")
{
	$tipo_realizado="40";
}
if ($tipo=="2")
{
	$tipo_realizado="42";
}
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$Usuario."',".$tarea.",".$tipo_realizado.""));

DBClose();
?>
