<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea        = $_GET["tarea"];
$numero       = $_GET["numero"];
$resultado    = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("DELETE FROM [tarea_pda] WHERE tarea = ".$tarea ." AND pda = ".$numero .""));
DBFree($insert);	
$insert=DBSelect(utf8_decode("SELECT COUNT(*) AS RESULTADO FROM [tarea_pda] WHERE Tarea =".$tarea." "));
$cantidad		= DBCampo($insert,("RESULTADO"));

DBClose();
echo $cantidad;
?>