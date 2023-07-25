<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        = $_GET["tarea"];
$prioridad = $_GET["prioridad"];
$usuario   = $_GET["usuario"];


DBConectar($GLOBALS["cfg_DataBase"]);
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$id.",".$prioridad." "));
DBFree($insert);	
DBClose();
?>