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

$q=DBSelect(utf8_decode("Select COUNT(*) AS HORAS
						FROM [Comentarios] where Fecha BETWEEN '".$dia." 00:00:00' AND '".$dia." 23:59:59' AND 0 < (
						SELECT COUNT(*) FROM [Tareas y Proyectos] T WHERE T.Id = Comentarios.Tarea AND T.Control <> 1 and [Asignado a] = '".$usuario_deseado."')"));
$HORAS=DBCampo($q,"HORAS");

DBFree($q);	
DBClose();

echo $HORAS;
?>