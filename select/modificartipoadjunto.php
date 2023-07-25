<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     = $_GET["Id"];
$tipo   = $_GET["tipo"];
$USuario= $_GET["usuario"];
$tarea  = $_GET["tarea"];

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(("UPDATE [Adjuntos] SET [Tipo] = ".$tipo." WHERE Numero = ".$id." "));

if ($tipo=="1")
{
	$tipo_realizado="46";
}
if ($tipo=="0")
{
	$tipo_realizado="45";
}
if ($tipo=="2")
{
	$tipo_realizado="47";
}

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$Usuario."',".$tarea.",".$tipo_realizado.""));
DBClose();

?>
