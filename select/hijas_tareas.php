<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$tarea_origen=$_GET['tarea_origen'];
$usuario     =$_GET['usuario'];
$guardar_log ='';

DBConectar($GLOBALS["cfg_DataBase"]);
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HIJA] '".$usuario."',".$tarea_origen.""));
for(;DBNext($insert);)
{				
	$Campo=DBCampo($insert,"Resultado");		
}
DBClose();

echo $Campo;
?>