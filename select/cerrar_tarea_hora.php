<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea_hora=$_GET['tarea_hora'];
$opcion    =$_GET['opcion'];					

DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("EXECUTE [CERRAR_TAREA_HORA] ".$tarea_hora.",".$opcion.""));
$Resultado=(DBCampo($q,"Resultado"));		
DBFree($q);				
DBClose();
echo $Resultado;
?>