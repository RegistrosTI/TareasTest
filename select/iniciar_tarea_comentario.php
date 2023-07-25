<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea  =$_GET['tarea'];
$usuario=$_GET['usuario'];					

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("EXECUTE [INICIAR_TAREA_COMENTARIO] ".$tarea.",'".utf8_encode($usuario)."'"));
$Resultado=(DBCampo($q,"Resultado"));		
DBFree($q);				
DBClose();

echo $Resultado;
?>