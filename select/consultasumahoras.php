<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario_deseado= $_GET["usuario"];
$dia            = $_GET["dia"];
$HORAS          ='';

DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("Select CAST((CAST(ISNULL(SUM([Minutos]),0) as int)% (24 * 60 )) / (60) AS varchar(120)) + ',' +
						CAST(((CAST(ISNULL(SUM([Minutos]),0)as int)% (24 * 60 )) % (60 )) AS varchar(120)) AS HORAS
						FROM [Horas] where usuario = '".$usuario_deseado."' and Inicio BETWEEN '".$dia." 00:00:00' AND '".$dia." 23:59:59' AND 0 < (
						SELECT COUNT(*) FROM [Tareas y Proyectos] T WHERE T.Id = HORAS.Tarea AND T.Control <> 1)"));
$HORAS=DBCampo($q,"HORAS");

DBFree($q);	
DBClose();

echo $HORAS;
?>